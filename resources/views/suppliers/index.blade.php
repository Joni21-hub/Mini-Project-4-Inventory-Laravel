@extends('layouts.app')

@section('title', 'Daftar Supplier')
@section('page_title', 'Manajemen Supplier & Pemasok')

@section('content')
<div class="d-flex flex-column gap-3">

    <!-- Header & Search Toolbar -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Pemasok (Suppliers)</h5>
                    <p class="text-muted small mb-0">Kelola direktori kontak vendor dan distributor pengadaan barang.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <div>
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3">
                            <i class="bi bi-plus-lg"></i>
                            <span>Tambah Supplier</span>
                        </a>
                    </div>
                @endif
            </div>

            <hr class="my-3">

            <!-- Search Form -->
            <form action="{{ route('suppliers.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari nama supplier, kontak PIC, atau no. telp..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('suppliers.index') }}" class="btn btn-light border" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Perusahaan / Vendor</th>
                        <th>Kontak PIC</th>
                        <th>No. Telepon</th>
                        <th>Email</th>
                        <th class="text-center">Katalog Produk</th>
                        <th class="text-end px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <a href="{{ route('suppliers.show', $supplier) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $supplier->name }}
                                </a>
                                @if($supplier->address)
                                    <div class="small text-muted text-truncate" style="max-width: 280px;">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $supplier->address }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $supplier->contact_name ?: '-' }}</span>
                            </td>
                            <td>
                                @if($supplier->phone)
                                    <a href="tel:{{ $supplier->phone }}" class="text-decoration-none text-secondary">
                                        <i class="bi bi-telephone me-1 text-primary"></i>{{ $supplier->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->email)
                                    <a href="mailto:{{ $supplier->email }}" class="text-decoration-none text-secondary">
                                        <i class="bi bi-envelope me-1 text-primary"></i>{{ $supplier->email }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="badge bg-info-subtle text-info-emphasis border border-info-subtle text-decoration-none px-2 py-1">
                                    {{ $supplier->products_count }} Produk
                                </a>
                            </td>
                            <td class="text-end px-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-light border" title="Lihat Profil & Katalog">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-light border text-primary" title="Edit Supplier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-light border text-danger" title="Hapus Supplier" 
                                                data-bs-toggle="modal" data-bs-target="#delSupModal{{ $supplier->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>

                                @if(Auth::user()->isAdmin())
                                    <!-- Delete Supplier Modal -->
                                    <div class="modal fade text-start" id="delSupModal{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-danger">
                                                        <i class="bi bi-exclamation-triangle me-1"></i> Hapus Supplier
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-1">Apakah Anda yakin ingin menghapus data supplier <strong>{{ $supplier->name }}</strong>?</p>
                                                    <small class="text-muted">
                                                        Relasi supplier ini dengan {{ $supplier->products_count }} produk akan dilepaskan secara otomatis.
                                                    </small>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Hapus Supplier</button>
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
                                <i class="bi bi-truck fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada data supplier yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $suppliers->firstItem() ?? 0 }} - {{ $suppliers->lastItem() ?? 0 }} dari {{ $suppliers->total() }} supplier
                </small>
                <div>
                    {{ $suppliers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
