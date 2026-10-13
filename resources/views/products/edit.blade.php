@extends('layouts.app')

@section('title', 'Edit Produk — ' . $product->name)
@section('page_title', 'Form Perubahan Data Produk')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Perbarui Data: {{ $product->name }}</h6>
                    <small class="text-muted font-monospace">SKU: {{ $product->sku }}</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-light border py-1 px-3">
                        Lihat Detail
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-light border py-1 px-3">
                        &larr; Kembali
                    </a>
                </div>
            </div>

            <div class="card-body p-4 p-lg-5">
                <form action="{{ route('products.update', $product) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Section 1 -->
                    <div class="mb-4">
                        <span class="badge bg-light text-primary border px-2 py-1 mb-2 fw-bold" style="font-size: 0.72rem;">BAGIAN 1</span>
                        <h6 class="fw-bold text-dark mb-3">Informasi Utama Produk</h6>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="sku" class="form-label">Kode SKU <span class="text-danger">*</span></label>
                                <input type="text" name="sku" id="sku" class="form-control font-monospace @error('sku') is-invalid @enderror" 
                                       value="{{ old('sku', $product->sku) }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5">
                                <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                        <h6 class="fw-bold text-dark mb-3">Harga Jual & Parameter Stok</h6>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="price" class="form-label">Harga Jual Satuan (IDR) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted">Rp</span>
                                    <input type="number" step="100" min="0" name="price" id="price" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           value="{{ old('price', $product->price) }}" required>
                                </div>
                                @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Saldo Stok Saat Ini</label>
                                <div class="form-control bg-light d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark">{{ number_format($product->stock) }} Unit</span>
                                    <span class="badge bg-light text-secondary border">Terkunci Audit</span>
                                </div>
                                <div class="form-text small">Perubahan saldo fisik wajib lewat transaksi Barang Masuk / Keluar.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="minimum_stock" class="form-label">Batas Minimum Stok <span class="text-danger">*</span></label>
                                <input type="number" min="0" name="minimum_stock" id="minimum_stock" 
                                       class="form-control @error('minimum_stock') is-invalid @enderror" 
                                       value="{{ old('minimum_stock', $product->minimum_stock) }}" required>
                                @error('minimum_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: #f1f5f9;">

                    <!-- Section 3 -->
                    <div class="mb-4">
                        <span class="badge bg-light text-primary border px-2 py-1 mb-2 fw-bold" style="font-size: 0.72rem;">BAGIAN 3</span>
                        <h6 class="fw-bold text-dark mb-2">Sinkronisasi Pemasok (Suppliers)</h6>
                        <p class="small text-muted mb-3">Kelola mitra supplier aktif dan perbarui harga beli per unit.</p>

                        <div class="p-3 bg-light rounded-3 border">
                            <div class="row g-2">
                                @forelse($suppliers as $supplier)
                                    @php
                                        $linkedSupplier = $product->suppliers->firstWhere('id', $supplier->id);
                                        $isLinked = (bool) $linkedSupplier;
                                        $currentPrice = $linkedSupplier ? $linkedSupplier->pivot->supplier_price : '';
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="p-2 border rounded-2 bg-white d-flex align-items-center justify-content-between gap-2">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="suppliers[]" value="{{ $supplier->id }}" id="sup_{{ $supplier->id }}"
                                                       {{ $isLinked ? 'checked' : '' }}
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
                                                           value="{{ old("supplier_prices.{$supplier->id}", $currentPrice) }}"
                                                           {{ $isLinked ? '' : 'disabled' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted small py-2">Belum ada supplier terdaftar.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3">
                        <a href="{{ route('products.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            Simpan Perubahan
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
        }
    }
</script>
@endpush
