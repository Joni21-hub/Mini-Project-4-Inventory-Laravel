@extends('layouts.app')

@section('title', 'Pencatatan Barang Masuk (Stock IN)')
@section('page_title', 'Transaksi Barang Masuk (IN)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-success"><i class="bi bi-box-arrow-in-down me-2"></i>Pencatatan Stok Masuk (Stock IN)</h6>
                    <small class="text-muted">Menambahkan kuantitas fisik persediaan produk secara atomik</small>
                </div>
                <a href="{{ route('stock.history') }}" class="btn btn-sm btn-light border">
                    <i class="bi bi-clock-history"></i> Riwayat Mutasi
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('stock.in.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="product_id" class="form-label small fw-semibold">Pilih Produk <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required autofocus>
                            <option value="">-- Pilih Produk yang Masuk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-stock="{{ $product->stock }}" 
                                        data-min="{{ $product->minimum_stock }}"
                                        data-sku="{{ $product->sku }}"
                                        {{ (old('product_id', $selectedProductId) == $product->id) ? 'selected' : '' }}>
                                    [{{ $product->sku }}] {{ $product->name }} &mdash; (Stok saat ini: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label small fw-semibold">Jumlah Kuantitas Masuk <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" min="1" name="quantity" id="quantity" 
                                   class="form-control fs-5 fw-bold text-success @error('quantity') is-invalid @enderror" 
                                   placeholder="0" value="{{ old('quantity', 1) }}" required>
                            <span class="input-group-text bg-light text-muted">Unit</span>
                        </div>
                        @error('quantity')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Live Dynamic Stock Calculator Preview -->
                    <div class="card bg-success-subtle border-success-subtle mb-3" id="calcCard" style="display: none;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-secondary d-block">Stok Awal:</small>
                                    <span class="fw-bold fs-6 text-dark" id="txtCurrentStock">0</span> unit
                                </div>
                                <div class="text-success fs-4 fw-bold">+</div>
                                <div>
                                    <small class="text-secondary d-block">Jumlah Masuk:</small>
                                    <span class="fw-bold fs-6 text-success" id="txtAddQty">0</span> unit
                                </div>
                                <div class="text-secondary fs-4 fw-bold">=</div>
                                <div>
                                    <small class="text-secondary d-block">Proyeksi Stok Baru:</small>
                                    <span class="fw-bold fs-5 text-success" id="txtNewStock">0</span> unit
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label small fw-semibold">Catatan / Keterangan Transaksi</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="Contoh: Penerimaan barang dari PO-2026-004 atau restock distributor">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-success px-4 fw-semibold">
                            <i class="bi bi-box-arrow-in-down me-1"></i> Simpan Transaksi Masuk
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
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('quantity');
    const calcCard = document.getElementById('calcCard');
    const txtCurrentStock = document.getElementById('txtCurrentStock');
    const txtAddQty = document.getElementById('txtAddQty');
    const txtNewStock = document.getElementById('txtNewStock');

    function updateStockCalc() {
        const selectedOpt = productSelect.options[productSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const currentStock = parseInt(selectedOpt.getAttribute('data-stock')) || 0;
            const addQty = parseInt(qtyInput.value) || 0;
            const newStock = currentStock + addQty;

            txtCurrentStock.textContent = currentStock;
            txtAddQty.textContent = addQty;
            txtNewStock.textContent = newStock;
            calcCard.style.display = 'block';
        } else {
            calcCard.style.display = 'none';
        }
    }

    productSelect.addEventListener('change', updateStockCalc);
    qtyInput.addEventListener('input', updateStockCalc);

    // Initial check
    if (productSelect.value) {
        updateStockCalc();
    }
</script>
@endpush
