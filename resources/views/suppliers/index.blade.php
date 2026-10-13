@extends('layouts.app')

@section('title', 'Daftar Supplier')
@section('page_title', 'Master Data Mitra Vendor')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Header & Search Toolbar -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Daftar Pemasok (Suppliers)</h5>
                    <p class="text-muted small mb-0">Kelola direktori kontak vendor dan distributor pengadaan barang inventaris.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <div>
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary px-3 shadow-sm">
                            + Tambah Supplier
                        </a>
                    </div>
                @endif
            </div>

            <hr class="my-3" style="border-color: #f1f5f9;">

            <!-- Search Form -->
            <form action="{{ route('suppliers.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Ketik nama supplier, kontak PIC, atau no. telepon..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('suppliers.index') }}" class="btn btn-light border px-3" title="Reset">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Perusahaan / Vendor</th>
                        <th>Kontak PIC</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th class="text-center">Katalog Produk</th>
                        <th class="text-end px-3">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <a href="{{ route('suppliers.show', $supplier) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block" style="font-size: 0.9rem;">
                                    {{ $supplier->name }}
                                </a>
                                @if($supplier->address)
                                    <small class="text-muted text-truncate d-block" style="max-width: 280px; font-size: 0.75rem;">
                                        {{ $supplier->address }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold text-dark" style="font-size: 0.85rem;">{{ $supplier->contact_name ?: '-' }}</span>
                            </td>
                            <td>
                                @if($supplier->phone)
                                    <span class="text-secondary font-monospace" style="font-size: 0.82rem;">{{ $supplier->phone }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->email)
                                    <span class="text-secondary" style="font-size: 0.82rem;">{{ $supplier->email }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="badge bg-light text-primary border text-decoration-none px-3 py-1" style="font-size: 0.78rem;">
                                    {{ $supplier->products_count }} Produk
                                </a>
                            </td>
                            <td class="text-end px-3">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.78rem;" title="Lihat Profil">
                                        Profil
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-light border py-1 px-2 text-primary" style="font-size: 0.78rem;" title="Edit Supplier">
                                            Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border py-1 px-2 text-danger" style="font-size: 0.78rem;" title="Hapus Supplier" 
                                                data-bs-toggle="modal" data-bs-target="#delSupModal{{ $supplier->id }}">
                                            Hapus
                                        </button>
                                    @endif
                                </div>

                                @if(Auth::user()->isAdmin())
                                    <!-- Delete Supplier Modal -->
                                    <div class="modal fade text-start" id="delSupModal{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-3 border-0 shadow-lg">
                                                <div class="modal-header border-bottom">
                                                    <h6 class="modal-title fw-bold text-danger">Hapus Supplier</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="mb-2">Apakah Anda yakin ingin menghapus data supplier <strong>{{ $supplier->name }}</strong>?</p>
                                                    <div class="p-2 bg-light rounded-2 border text-muted small">
                                                        Relasi supplier ini dengan {{ $supplier->products_count }} produk akan dilepaskan secara otomatis dari pivot table.
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top">
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
                                <span class="d-block fw-bold text-dark mb-1">Tidak ada data supplier yang ditemukan.</span>
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
