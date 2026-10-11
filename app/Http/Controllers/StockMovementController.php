<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    /**
     * Display movement history with rich filtering.
     */
    public function history(Request $request): View
    {
        $query = StockMovement::with(['product.category', 'user']);

        // Filter by movement type
        if ($type = $request->input('type')) {
            if (in_array($type, ['IN', 'OUT'], true)) {
                $query->where('type', $type);
            }
        }

        // Filter by product
        if ($productId = $request->input('product_id')) {
            $query->where('product_id', $productId);
        }

        // Filter by date range
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $movements = $query->latest('id')->paginate(15)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);

        return view('stock.history', compact('movements', 'products'));
    }

    /**
     * Show form for Stock IN (Barang Masuk).
     */
    public function createIn(Request $request): View
    {
        $products = Product::orderBy('name')->get();
        $selectedProductId = $request->input('product_id');

        return view('stock.in', compact('products', 'selectedProductId'));
    }

    /**
     * Store Stock IN transaction with DB transaction.
     */
    public function storeIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $productName = '';
        $newStock = 0;

        DB::transaction(function () use ($validated, &$productName, &$newStock) {
            $product = Product::where('id', $validated['product_id'])->lockForUpdate()->firstOrFail();

            $product->increment('stock', $validated['quantity']);
            $product->refresh();

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id() ?? 1,
                'type' => 'IN',
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ? trim($validated['notes']) : 'Penerimaan stok barang masuk',
            ]);

            $productName = $product->name;
            $newStock = $product->stock;
        });

        return redirect()->route('stock.history')
            ->with('success', "Berhasil mencatat Barang Masuk (+{$validated['quantity']}) untuk '{$productName}'. Total stok saat ini: {$newStock}.");
    }

    /**
     * Show form for Stock OUT (Barang Keluar).
     */
    public function createOut(Request $request): View
    {
        $products = Product::orderBy('name')->get();
        $selectedProductId = $request->input('product_id');

        return view('stock.out', compact('products', 'selectedProductId'));
    }

    /**
     * Store Stock OUT transaction with pessimistic locking preventing negative stock.
     */
    public function storeOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $productName = '';
        $remainingStock = 0;

        DB::transaction(function () use ($validated, &$productName, &$remainingStock) {
            // Pessimistic Concurrency Control (lockForUpdate)
            $product = Product::where('id', $validated['product_id'])->lockForUpdate()->firstOrFail();

            if ($product->stock < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok tidak mencukupi! Stok saat ini untuk '{$product->name}' hanya tersisa {$product->stock} unit (permintaan keluar: {$validated['quantity']} unit).",
                ]);
            }

            $product->decrement('stock', $validated['quantity']);
            $product->refresh();

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id() ?? 1,
                'type' => 'OUT',
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ? trim($validated['notes']) : 'Pengeluaran stok barang keluar',
            ]);

            $productName = $product->name;
            $remainingStock = $product->stock;
        });

        return redirect()->route('stock.history')
            ->with('success', "Berhasil mencatat Barang Keluar (-{$validated['quantity']}) untuk '{$productName}'. Sisa stok saat ini: {$remainingStock}.");
    }
}
