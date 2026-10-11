<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display reports overview and analytical summaries.
     */
    public function index(Request $request): View
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Filter movements based on date
        $movementsQuery = StockMovement::with(['product.category', 'user']);
        if ($dateFrom) {
            $movementsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $movementsQuery->whereDate('created_at', '<=', $dateTo);
        }

        $totalMovements = (clone $movementsQuery)->count();
        $totalInQty = (clone $movementsQuery)->where('type', 'IN')->sum('quantity');
        $totalOutQty = (clone $movementsQuery)->where('type', 'OUT')->sum('quantity');

        // Category valuation breakdown
        $categoryBreakdown = Category::withCount('products')
            ->withSum('products', 'stock')
            ->get()
            ->map(function ($category) {
                $valuation = Product::where('category_id', $category->id)
                    ->selectRaw('SUM(price * stock) as total_val')
                    ->value('total_val') ?? 0;
                $category->total_valuation = $valuation;
                return $category;
            });

        // Top 5 products by transaction activity
        $topActiveProducts = Product::with('category')
            ->withCount('stockMovements')
            ->orderBy('stock_movements_count', 'desc')
            ->take(5)
            ->get();

        // Low stock audit list
        $criticalStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->get();

        return view('reports.index', compact(
            'dateFrom',
            'dateTo',
            'totalMovements',
            'totalInQty',
            'totalOutQty',
            'categoryBreakdown',
            'topActiveProducts',
            'criticalStockProducts'
        ));
    }

    /**
     * Export stock movements to CSV.
     */
    public function exportMovementsCsv(Request $request): StreamedResponse
    {
        $fileName = 'laporan-mutasi-stok-' . date('Y-m-d-His') . '.csv';

        $query = StockMovement::with(['product.category', 'user'])->latest('id');

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ID', 'Waktu', 'SKU', 'Nama Produk', 'Kategori', 'Tipe', 'Jumlah', 'Petugas', 'Catatan']);

            $query->chunk(200, function ($movements) use ($handle) {
                foreach ($movements as $m) {
                    fputcsv($handle, [
                        $m->id,
                        $m->created_at->format('Y-m-d H:i:s'),
                        $m->product->sku ?? '-',
                        $m->product->name ?? '-',
                        $m->product->category->name ?? '-',
                        $m->type,
                        $m->quantity,
                        $m->user->name ?? 'System',
                        $m->notes ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export current products inventory list to CSV.
     */
    public function exportProductsCsv(): StreamedResponse
    {
        $fileName = 'laporan-inventaris-produk-' . date('Y-m-d-His') . '.csv';

        $products = Product::with('category')->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($products) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['SKU', 'Nama Produk', 'Kategori', 'Harga Satuan (IDR)', 'Stok Saat Ini', 'Batas Minimal', 'Total Nilai (IDR)', 'Status']);

            foreach ($products as $p) {
                $status = $p->stock <= 0 ? 'Habis' : ($p->stock <= $p->minimum_stock ? 'Menipis' : 'Aman');
                $totalVal = $p->price * $p->stock;

                fputcsv($handle, [
                    $p->sku,
                    $p->name,
                    $p->category->name ?? '-',
                    $p->price,
                    $p->stock,
                    $p->minimum_stock,
                    $totalVal,
                    $status,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
