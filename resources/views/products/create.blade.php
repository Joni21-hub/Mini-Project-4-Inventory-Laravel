@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('page_title', 'Form Registrasi Produk')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Form Tambah Produk Baru</h6>
                    <small class="text-muted">Daftarkan item barang baru ke dalam basis data inventaris</small>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-light border py-1 px-3">
                    &larr; Kembali
                </a>
            </div>

            <div class="card-body p-4 p-lg-5">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <!-- Section 1 -->
                    <div class="mb-4">
                        <span class="badge bg-light text-primary border px-2 py-1 mb-2 fw-bold" style="font-size: 0.72rem;">BAGIAN 1</span>
                        <h6 class="fw-bold text-dark mb-3">Identitas & Klasifikasi Produk</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="sku" class="form-label">Kode SKU <span class="text-danger">*</span></label>
                                <input type="text" name="sku" id="sku" class="form-control font-monospace @error('sku') is-invalid @enderror" 
                                       placeholder="Contoh: PRD-LPT-001" value="{{ old('sku') }}" required autofocus>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text small">Kode unik identifikasi stok (Stock Keeping Unit).</div>
                            </div>

                            <div class="col-md-5">
                                <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="Nama item atau seri produk" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: #f1f5f9;">

                    <!-- Section 2 -->
                    <div class="mb-4">
                        <span class="badge bg-light text-primary border px-2 py-1 mb-2 fw-bold" style="font-size: 0.72rem;">BAGIAN 2</span>
                        <h6 class="fw-bold text-dark mb-3">Harga & Parameter Persediaan</h6>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="price" class="form-label">Harga Jual Satuan (IDR) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted">Rp</span>
                                    <input type="number" step="100" min="0" name="price" id="price" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           placeholder="0" value="{{ old('price', 0) }}" required>
                                </div>
                                @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="stock" class="form-label">Stok Fisik Awal <span class="text-danger">*</span></label>
                                <input type="number" min="0" name="stock" id="stock" 
                                       class="form-control @error('stock') is-invalid @enderror" 
                                       placeholder="0" value="{{ old('stock', 0) }}" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text small">Tercatat otomatis sebagai saldo mutasi awal.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="minimum_stock" class="form-label">Batas Minimum Stok <span class="text-danger">*</span></label>
                                <input type="number" min="0" name="minimum_stock" id="minimum_stock" 
                                       class="form-control @error('minimum_stock') is-invalid @enderror" 
                                       placeholder="5" value="{{ old('minimum_stock', 5) }}" required>
                                @error('minimum_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text small">Peringatan restock aktif jika kuantitas &le; nilai ini.</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: #f1f5f9;">

                    <!-- Section 3 -->
                    <div class="mb-4">
                        <span class="badge bg-light text-primary border px-2 py-1 mb-2 fw-bold" style="font-size: 0.72rem;">BAGIAN 3</span>
                        <h6 class="fw-bold text-dark mb-2">Penetapan Mitra Supplier</h6>
                        <p class="small text-muted mb-3">Centang mitra supplier yang menyuplai produk ini dan tentukan estimasi harga beli kulakan.</p>

                        <div class="p-3 bg-light rounded-3 border">
                            <div class="row g-2">
                                @forelse($suppliers as $supplier)
                                    <div class="col-md-6">
                                        <div class="p-2 border rounded-2 bg-white d-flex align-items-center justify-content-between gap-2">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="suppliers[]" value="{{ $supplier->id }}" id="sup_{{ $supplier->id }}"
                                                       {{ in_array($supplier->id, old('suppliers', [])) ? 'checked' : '' }}
                                                       onchange="togglePriceInput('{{ $supplier->id }}')">
                                                <label class="form-check-label fw-semibold small text-dark" for="sup_{{ $supplier->id }}">
                                                    {{ $supplier->name }}
                                                </label>
                                            </div>
                                            <div style="width: 140px;">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text py-0 px-2 bg-light text-muted" style="font-size: 0.75rem;">Rp</span>
                                                    <input type="number" name="supplier_prices[{{ $supplier->id }}]" 
                                                           id="price_input_{{ $supplier->id }}" class="form-control py-1" 
                                                           placeholder="Harga Beli" 
                                                           value="{{ old("supplier_prices.{$supplier->id}") }}"
                                                           {{ in_array($supplier->id, old('suppliers', [])) ? '' : 'disabled' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted small py-2">Belum ada mitra supplier terdaftar.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3">
                        <a href="{{ route('products.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            Simpan Produk Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePriceInput(supId) {
        const checkbox = document.getElementById('sup_' + supId);
        const input = document.getElementById('price_input_' + supId);
        if (checkbox && input) {
            input.disabled = !checkbox.checked;
            if (checkbox.checked && !input.value) {
                const productPrice = document.getElementById('price').value;
                if (productPrice) {
                    input.value = productPrice;
                }
            }
        }
    }
</script>
@endpush
