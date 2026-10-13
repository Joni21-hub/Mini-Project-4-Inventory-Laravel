@extends('layouts.app')

@section('title', 'Katalog Produk')
@section('page_title', 'Master Data Inventaris')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Header Toolbar & Search Filter -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Katalog Produk & Pengendalian Stok</h5>
                    <p class="text-muted small mb-0">Kelola informasi produk fisik, pantau kuantitas, dan kendalikan pasokan mitra.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <div>
                        <a href="{{ route('products.create') }}" class="btn btn-primary px-3 shadow-sm">
                            <span>+ Tambah Produk Baru</span>
                        </a>
                    </div>
                @endif
            </div>

            <hr class="my-3" style="border-color: #f1f5f9;">

            <!-- Filter & Search Toolbar -->
            <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Ketik SKU atau nama produk..." value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="stock_status" class="form-select">
                        <option value="all" {{ request('stock_status') == 'all' ? 'selected' : '' }}>Semua Status Stok</option>
                        <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Stok Aman</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis (<= Min)</option>
                        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'stock_status', 'sort']))
                        <a href="{{ route('products.index') }}" class="btn btn-light border px-3" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Products Data Table with Real Product Thumbnails -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th style="width: 70px;">Visual</th>
                        <th>
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'sku', 'direction' => ($sort === 'sku' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-muted">
                                SKU {{ $sort === 'sku' ? ($direction === 'asc' ? '↑' : '↓') : '' }}
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-muted">
                                Nama Produk & Mitra {{ $sort === 'name' ? ($direction === 'asc' ? '↑' : '↓') : '' }}
                            </a>
                        </th>
                        <th>Kategori</th>
                        <th class="text-end">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'price', 'direction' => ($sort === 'price' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-muted">
                                Harga Jual {{ $sort === 'price' ? ($direction === 'asc' ? '↑' : '↓') : '' }}
                            </a>
                        </th>
                        <th class="text-center">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'stock', 'direction' => ($sort === 'stock' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-muted">
                                Sisa Stok {{ $sort === 'stock' ? ($direction === 'asc' ? '↑' : '↓') : '' }}
                            </a>
                        </th>
                        <th class="text-center">Min. Stok</th>
                        <th class="text-center">Status</th>
                        <th class="text-end px-3">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <!-- Visual Thumbnail -->
                            <td>
                                <div class="product-thumb-container">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                                </div>
                            </td>

                            <!-- SKU -->
                            <td>
                                <span class="badge-sku">
                                    {{ $product->sku }}
                                </span>
                            </td>

                            <!-- Product Name & Suppliers -->
                            <td>
                                <div>
                                    <a href="{{ route('products.show', $product) }}" class="fw-bold text-decoration-none text-dark hover-primary d-block" style="font-size: 0.9rem;">
                                        {{ $product->name }}
                                    </a>
                                    @if($product->suppliers->isNotEmpty())
                                        <small class="text-muted d-block" style="font-size: 0.74rem;">
                                            Pasokan: {{ $product->suppliers->pluck('name')->join(', ') }}
                                        </small>
                                    @endif
                                </div>
                            </td>

                            <!-- Category -->
                            <td>
                                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.75rem;">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </td>

                            <!-- Price -->
                            <td class="text-end fw-semibold text-dark">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            <!-- Stock -->
                            <td class="text-center fw-bold fs-6 {{ $product->stock <= 0 ? 'text-danger' : ($product->stock <= $product->minimum_stock ? 'text-warning' : 'text-success') }}">
                                {{ number_format($product->stock) }}
                            </td>

                            <!-- Min Stock -->
                            <td class="text-center text-muted fw-medium">
                                {{ number_format($product->minimum_stock) }}
                            </td>

                            <!-- Status Pill (No Icons) -->
                            <td class="text-center">
                                @if($product->stock <= 0)
                                    <span class="status-badge badge-out">
                                        <span class="dot"></span> Habis
                                    </span>
                                @elseif($product->stock <= $product->minimum_stock)
                                    <span class="status-badge badge-low">
                                        <span class="dot"></span> Menipis
                                    </span>
                                @else
                                    <span class="status-badge badge-safe">
                                        <span class="dot"></span> Aman
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="text-end px-3">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.78rem;" title="Lihat Detail">
                                        Detail
                                    </a>
                                    <a href="{{ route('stock.in', ['product_id' => $product->id]) }}" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 0.78rem;" title="Barang Masuk">
                                        +IN
                                    </a>
                                    <a href="{{ route('stock.out', ['product_id' => $product->id]) }}" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 0.78rem;" title="Barang Keluar">
                                        -OUT
                                    </a>

                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-light border py-1 px-2 text-primary" style="font-size: 0.78rem;" title="Edit Produk">
                                            Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border py-1 px-2 text-danger" style="font-size: 0.78rem;" title="Hapus Produk" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}">
                                            Hapus
                                        </button>
                                    @endif
                                </div>

                                @if(Auth::user()->isAdmin())
                                    <!-- Delete Modal -->
                                    <div class="modal fade text-start" id="deleteModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-3 border-0 shadow-lg">
                                                <div class="modal-header border-bottom">
                                                    <h6 class="modal-title fw-bold text-danger">Konfirmasi Hapus Produk</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="mb-2">Apakah Anda yakin ingin menghapus produk <strong>{{ $product->name }}</strong> (SKU: <code>{{ $product->sku }}</code>)?</p>
                                                    <div class="p-2 bg-light rounded-2 border text-muted small">
                                                        Produk yang telah memiliki histori mutasi stok terlindungi dan tidak dapat dihapus sembarangan demi integritas data audit.
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('products.destroy', $product) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Hapus Permanen</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <span class="d-block fw-bold text-dark fs-6 mb-1">Tidak ada produk yang cocok dengan pencarian Anda.</span>
                                <span class="d-block small mb-3">Coba ubah kata kunci atau bersihkan kriteria filter.</span>
                                @if(request()->hasAny(['search', 'category_id', 'stock_status']))
                                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-primary">
                                        Reset Semua Filter
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                </small>
                <div>
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
