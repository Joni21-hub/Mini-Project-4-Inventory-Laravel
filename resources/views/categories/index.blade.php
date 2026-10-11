@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('page_title', 'Daftar Kategori')

@section('content')
<div class="d-flex flex-column gap-3">

    <!-- Header & Search Toolbar -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Manajemen Kategori</h5>
                    <p class="text-muted small mb-0">Kelompokkan produk ke dalam kategori untuk pengelolaan inventaris yang lebih rapi.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <div>
                        <a href="{{ route('categories.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3">
                            <i class="bi bi-plus-lg"></i>
                            <span>Tambah Kategori</span>
                        </a>
                    </div>
                @endif
            </div>

            <hr class="my-3">

            <!-- Search Form -->
            <form action="{{ route('categories.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari nama atau deskripsi kategori..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('categories.index') }}" class="btn btn-light border" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Produk</th>
                        <th class="text-center">Total Fisik Stok</th>
                        <th class="text-end px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark fs-6">{{ $category->name }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $category->description ?: 'Tidak ada keterangan tambahan' }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none px-2 py-1">
                                    {{ $category->products_count }} Produk
                                </a>
                            </td>
                            <td class="text-center fw-semibold text-secondary">
                                {{ number_format($category->products_sum_stock ?? 0) }} unit
                            </td>
                            <td class="text-end px-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="btn btn-light border" title="Lihat Produk Terkait">
                                        <i class="bi bi-box-seam"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-light border text-primary" title="Edit Kategori">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-light border text-danger" title="Hapus Kategori" 
                                                data-bs-toggle="modal" data-bs-target="#delCatModal{{ $category->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>

                                @if(Auth::user()->isAdmin())
                                    <!-- Delete Category Modal -->
                                    <div class="modal fade text-start" id="delCatModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-danger">
                                                        <i class="bi bi-exclamation-triangle me-1"></i> Hapus Kategori
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if($category->products_count > 0)
                                                        <div class="alert alert-warning mb-2 small">
                                                            <i class="bi bi-lock-fill me-1"></i> Kategori ini memiliki <strong>{{ $category->products_count }}</strong> produk aktif. 
                                                            Penghapusan diblokir demi menjaga integritas relasi data (Foreign Key Restrict).
                                                        </div>
                                                        <p class="mb-0 text-muted small">Pindahkan atau hapus produk di dalamnya terlebih dahulu sebelum menghapus kategori ini.</p>
                                                    @else
                                                        <p class="mb-0">Apakah Anda yakin ingin menghapus kategori <strong>{{ $category->name }}</strong>?</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                                    @if($category->products_count === 0)
                                                        <form action="{{ route('categories.destroy', $category) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Hapus Kategori</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-tags fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada kategori yang ditemukan.
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
