<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the inventory management dashboard.
     */
    public function index(): View
    {
        // 1. Core Summary Metrics
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        $totalStockQty = Product::sum('stock');
        $totalInventoryValue = Product::selectRaw('SUM(price * stock) as total_val')->value('total_val') ?? 0;

        $lowStockCount = Product::whereColumn('stock', '<=', 'minimum_stock')
            ->where('stock', '>', 0)
            ->count();

        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        $monthlyIn = StockMovement::where('type', 'IN')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity');

        $monthlyOut = StockMovement::where('type', 'OUT')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity');

        // 2. Critical Stock Alerts (Items needing attention)
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // 3. Recent Stock Activity
        $recentMovements = StockMovement::with(['product', 'user'])
            ->latest('id')
            ->take(7)
            ->get();

        // 4. Data for Category Distribution Chart (Donut)
        $categoriesData = Category::has('products')
            ->withCount('products')
            ->get(['id', 'name']);

        $categoryLabels = $categoriesData->pluck('name')->toArray();
        $categoryCounts = $categoriesData->pluck('products_count')->toArray();

        // 5. Data for 7-Day Stock In/Out Trend Chart (Bar/Line)
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->put(now()->subDays($i)->format('Y-m-d'), [
                'label' => now()->subDays($i)->isoFormat('D MMM'),
                'in' => 0,
                'out' => 0,
            ]);
        }

        $startDate = now()->subDays(6)->startOfDay();
        $movementStats = StockMovement::select(
            DB::raw('DATE(created_at) as date'),
            'type',
            DB::raw('SUM(quantity) as total_qty')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date', 'type')
            ->get();

        foreach ($movementStats as $stat) {
            $d = $stat->date;
            if ($dates->has($d)) {
                $item = $dates->get($d);
                if ($stat->type === 'IN') {
                    $item['in'] = (int) $stat->total_qty;
                } else {
                    $item['out'] = (int) $stat->total_qty;
                }
                $dates->put($d, $item);
            }
        }

        $chartLabels = $dates->pluck('label')->values()->toArray();
        $chartDataIn = $dates->pluck('in')->values()->toArray();
        $chartDataOut = $dates->pluck('out')->values()->toArray();

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalStockQty',
            'totalInventoryValue',
            'lowStockCount',
            'outOfStockCount',
            'monthlyIn',
            'monthlyOut',
            'lowStockProducts',
            'recentMovements',
            'categoryLabels',
            'categoryCounts',
            'chartLabels',
            'chartDataIn',
            'chartDataOut'
        ));
    }
}
