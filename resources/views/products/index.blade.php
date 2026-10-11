@extends('layouts.app')

@section('title', 'Katalog Produk')
@section('page_title', 'Daftar Produk')

@section('content')
<div class="d-flex flex-column gap-3">

    <!-- Header Actions & Search Toolbar -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Manajemen Inventaris Produk</h5>
                    <p class="text-muted small mb-0">Kelola informasi produk, pantau level stok, dan kendalikan pasokan.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <div>
                        <a href="{{ route('products.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3">
                            <i class="bi bi-plus-lg"></i>
                            <span>Tambah Produk Baru</span>
                        </a>
                    </div>
                @endif
            </div>

            <hr class="my-3">

            <!-- Filter & Search Form -->
            <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" 
                               placeholder="Cari SKU atau nama produk..." value="{{ request('search') }}">
                    </div>
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
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'stock_status', 'sort']))
                        <a href="{{ route('products.index') }}" class="btn btn-light border" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'sku', 'direction' => ($sort === 'sku' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-secondary">
                                SKU 
                                @if($sort === 'sku')
                                    <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-secondary">
                                Nama Produk
                                @if($sort === 'name')
                                    <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                @endif
                            </a>
                        </th>
                        <th>Kategori</th>
                        <th class="text-end">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'price', 'direction' => ($sort === 'price' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-secondary">
                                Harga Jual
                                @if($sort === 'price')
                                    <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'stock', 'direction' => ($sort === 'stock' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-secondary">
                                Sisa Stok
                                @if($sort === 'stock')
                                    <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">Min. Stok</th>
                        <th class="text-center">Status</th>
                        <th class="text-end px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border px-2 py-1">
                                    {{ $product->sku }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('products.show', $product) }}" class="fw-bold text-decoration-none text-dark hover-primary">
                                    {{ $product->name }}
                                </a>
                                @if($product->suppliers->isNotEmpty())
                                    <div class="small text-muted">
                                        <i class="bi bi-truck me-1"></i>{{ $product->suppliers->pluck('name')->join(', ') }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis px-2 py-1">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="text-center fw-bold fs-6 {{ $product->stock <= 0 ? 'text-danger' : ($product->stock <= $product->minimum_stock ? 'text-warning' : 'text-success') }}">
                                {{ number_format($product->stock) }}
                            </td>
                            <td class="text-center text-muted">
                                {{ number_format($product->minimum_stock) }}
                            </td>
                            <td class="text-center">
                                @if($product->stock <= 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle badge-status">
                                        <i class="bi bi-x-circle me-1"></i>Habis
                                    </span>
                                @elseif($product->stock <= $product->minimum_stock)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle badge-status">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Menipis
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle badge-status">
                                        <i class="bi bi-check-circle me-1"></i>Aman
                                    </span>
                                @endif
                            </td>
                            <td class="text-end px-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-light border" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('stock.in', ['product_id' => $product->id]) }}" class="btn btn-light border text-success" title="Stok Masuk">
                                        <i class="bi bi-box-arrow-in-down"></i>
                                    </a>
                                    <a href="{{ route('stock.out', ['product_id' => $product->id]) }}" class="btn btn-light border text-danger" title="Stok Keluar">
                                        <i class="bi bi-box-arrow-up"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-light border text-primary" title="Edit Produk">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-light border text-danger" title="Hapus Produk" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>

                                @if(Auth::user()->isAdmin())
                                    <!-- Delete Modal -->
                                    <div class="modal fade text-start" id="deleteModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-danger">
                                                        <i class="bi bi-exclamation-triangle me-1"></i> Konfirmasi Hapus Produk
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-1">Apakah Anda yakin ingin menghapus produk <strong>{{ $product->name }}</strong> (SKU: <code>{{ $product->sku }}</code>)?</p>
                                                    <small class="text-muted d-block mt-2">
                                                        Catatan: Produk yang telah memiliki riwayat pergerakan stok tidak dapat dihapus demi integritas data audit.
                                                    </small>
                                                </div>
                                                <div class="modal-footer">
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
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                <span class="fw-semibold">Tidak ada data produk yang ditemukan.</span>
                                @if(request()->hasAny(['search', 'category_id', 'stock_status']))
                                    <div class="mt-2">
                                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">
                                            Bersihkan Filter Pencarian
                                        </a>
                                    </div>
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
