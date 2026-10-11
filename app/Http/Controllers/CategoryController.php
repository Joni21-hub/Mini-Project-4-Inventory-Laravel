<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with product counts and stock totals.
     */
    public function index(Request $request): View
    {
        $query = Category::withCount('products')
            ->withSum('products', 'stock');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Category::create([
            'name' => trim($validated['name']),
            'description' => $validated['description'] ? trim($validated['description']) : null,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $category->update([
            'name' => trim($validated['name']),
            'description' => $validated['description'] ? trim($validated['description']) : null,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category with integrity protection.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            $count = $category->products()->count();
            return back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih memiliki {$count} produk terhubung.");
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', "Kategori '{$category->name}' berhasil dihapus.");
    }
}
