@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('page_title', 'Tambah Produk Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Form Tambah Produk Baru</h6>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-light border">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <h6 class="fw-bold text-secondary text-uppercase small mb-3">1. Informasi Utama Produk</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="sku" class="form-label small fw-semibold">Kode SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" id="sku" class="form-control font-monospace @error('sku') is-invalid @enderror" 
                                   placeholder="Contoh: ELEC-001" value="{{ old('sku') }}" required autofocus>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Kode unik pengenal produk (SKU).</div>
                        </div>

                        <div class="col-md-5">
                            <label for="name" class="form-label small fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Nama barang atau model" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="category_id" class="form-label small fw-semibold">Kategori <span class="text-danger">*</span></label>
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

                    <h6 class="fw-bold text-secondary text-uppercase small mb-3">2. Penentuan Harga & Level Stok</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="price" class="form-label small fw-semibold">Harga Jual Satuan (IDR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" step="100" min="0" name="price" id="price" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       placeholder="0" value="{{ old('price', 0) }}" required>
                            </div>
                            @error('price')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="stock" class="form-label small fw-semibold">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" min="0" name="stock" id="stock" 
                                   class="form-control @error('stock') is-invalid @enderror" 
                                   placeholder="0" value="{{ old('stock', 0) }}" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Stok fisik awal saat produk pertama kali dibuat.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="minimum_stock" class="form-label small fw-semibold">Batas Minimum Stok <span class="text-danger">*</span></label>
                            <input type="number" min="0" name="minimum_stock" id="minimum_stock" 
                                   class="form-control @error('minimum_stock') is-invalid @enderror" 
                                   placeholder="5" value="{{ old('minimum_stock', 5) }}" required>
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Peringatan restock aktif jika stok &le; angka ini.</div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-secondary text-uppercase small mb-3">3. Hubungan Pemasok (Suppliers)</h6>
                    <div class="card bg-light border mb-4">
                        <div class="card-body p-3">
                            <p class="small text-muted mb-3">
                                Pilih mitra supplier yang menyuplai produk ini dan masukkan estimasi harga beli/kulakan per unit.
                            </p>

                            <div class="row g-2">
                                @forelse($suppliers as $supplier)
                                    <div class="col-md-6">
                                        <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between gap-2">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input supplier-checkbox" type="checkbox" 
                                                       name="suppliers[]" value="{{ $supplier->id }}" id="sup_{{ $supplier->id }}"
                                                       {{ in_array($supplier->id, old('suppliers', [])) ? 'checked' : '' }}
                                                       onchange="togglePriceInput('{{ $supplier->id }}')">
                                                <label class="form-check-label fw-semibold small" for="sup_{{ $supplier->id }}">
                                                    {{ $supplier->name }}
                                                </label>
                                            </div>
                                            <div style="width: 140px;">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text py-0 px-1 bg-light text-muted">Rp</span>
                                                    <input type="number" name="supplier_prices[{{ $supplier->id }}]" 
                                                           id="price_input_{{ $supplier->id }}" class="form-control py-0" 
                                                           placeholder="Harga Beli" 
                                                           value="{{ old("supplier_prices.{$supplier->id}") }}"
                                                           {{ in_array($supplier->id, old('suppliers', [])) ? '' : 'disabled' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted small">Belum ada data supplier. Tambahkan di menu Supplier.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i> Simpan Produk
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
