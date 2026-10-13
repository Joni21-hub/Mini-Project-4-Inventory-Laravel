@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('page_title', 'Master Data Klasifikasi')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Header & Search Toolbar -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Manajemen Kategori Produk</h5>
                    <p class="text-muted small mb-0">Kelompokkan produk ke dalam kategori untuk pengelolaan inventaris yang terstruktur.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <div>
                        <a href="{{ route('categories.create') }}" class="btn btn-primary px-3 shadow-sm">
                            + Tambah Kategori
                        </a>
                    </div>
                @endif
            </div>

            <hr class="my-3" style="border-color: #f1f5f9;">

            <!-- Search Form -->
            <form action="{{ route('categories.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Ketik nama atau deskripsi kategori..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('categories.index') }}" class="btn btn-light border px-3" title="Reset">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Data Table with Category Imagery -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th style="width: 70px;">Visual</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Produk</th>
                        <th class="text-center">Total Fisik Stok</th>
                        <th class="text-end px-3">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="product-thumb-container">
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" loading="lazy">
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark fs-6 d-block">{{ $category->name }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $category->description ?: 'Tidak ada keterangan tambahan' }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none px-3 py-1" style="font-size: 0.78rem;">
                                    {{ $category->products_count }} Item
                                </a>
                            </td>
                            <td class="text-center fw-semibold text-secondary">
                                {{ number_format($category->products_sum_stock ?? 0) }} unit
                            </td>
                            <td class="text-end px-3">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.78rem;" title="Lihat Produk Terkait">
                                        Produk
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-light border py-1 px-2 text-primary" style="font-size: 0.78rem;" title="Edit Kategori">
                                            Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border py-1 px-2 text-danger" style="font-size: 0.78rem;" title="Hapus Kategori" 
                                                data-bs-toggle="modal" data-bs-target="#delCatModal{{ $category->id }}">
                                            Hapus
                                        </button>
                                    @endif
                                </div>

                                @if(Auth::user()->isAdmin())
                                    <!-- Delete Category Modal -->
                                    <div class="modal fade text-start" id="delCatModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-3 border-0 shadow-lg">
                                                <div class="modal-header border-bottom">
                                                    <h6 class="modal-title fw-bold text-danger">Konfirmasi Hapus Kategori</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="mb-2">Apakah Anda yakin ingin menghapus kategori <strong>{{ $category->name }}</strong>?</p>
                                                    <div class="p-2 bg-light rounded-2 border text-muted small">
                                                        Kategori yang masih memiliki produk terhubung tidak dapat dihapus demi mencegah produk berstatus tanpa kategori (Foreign Key Restrict).
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('categories.destroy', $category) }}" method="POST">
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <span class="d-block fw-bold text-dark mb-1">Tidak ada data kategori yang ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} dari {{ $categories->total() }} kategori
                </small>
                <div>
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
