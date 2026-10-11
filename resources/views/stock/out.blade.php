@extends('layouts.app')

@section('title', 'Pencatatan Barang Keluar (Stock OUT)')
@section('page_title', 'Transaksi Barang Keluar (OUT)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-box-arrow-up me-2"></i>Pencatatan Stok Keluar (Stock OUT)</h6>
                    <small class="text-muted">Mengurangi kuantitas fisik persediaan dengan proteksi stok negatif (Pessimistic Lock)</small>
                </div>
                <a href="{{ route('stock.history') }}" class="btn btn-sm btn-light border">
                    <i class="bi bi-clock-history"></i> Riwayat Mutasi
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('stock.out.store') }}" method="POST" id="outForm">
                    @csrf

                    <div class="mb-3">
                        <label for="product_id" class="form-label small fw-semibold">Pilih Produk <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required autofocus>
                            <option value="">-- Pilih Produk yang Keluar --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-stock="{{ $product->stock }}" 
                                        data-min="{{ $product->minimum_stock }}"
                                        data-sku="{{ $product->sku }}"
                                        {{ (old('product_id', $selectedProductId) == $product->id) ? 'selected' : '' }}>
                                    [{{ $product->sku }}] {{ $product->name }} &mdash; (Sisa Stok: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label small fw-semibold">Jumlah Kuantitas Keluar <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" min="1" name="quantity" id="quantity" 
                                   class="form-control fs-5 fw-bold text-danger @error('quantity') is-invalid @enderror" 
                                   placeholder="0" value="{{ old('quantity', 1) }}" required>
                            <span class="input-group-text bg-light text-muted">Unit</span>
                        </div>
                        @error('quantity')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Live Dynamic Stock Deficit / Deduction Calculator -->
                    <div class="card mb-3" id="calcCard" style="display: none;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-secondary d-block">Stok Saat Ini:</small>
                                    <span class="fw-bold fs-6 text-dark" id="txtCurrentStock">0</span> unit
                                </div>
                                <div class="text-danger fs-4 fw-bold">&minus;</div>
                                <div>
                                    <small class="text-secondary d-block">Jumlah Keluar:</small>
                                    <span class="fw-bold fs-6 text-danger" id="txtSubQty">0</span> unit
                                </div>
                                <div class="text-secondary fs-4 fw-bold">=</div>
                                <div>
                                    <small class="text-secondary d-block">Sisa Stok Akhir:</small>
                                    <span class="fw-bold fs-5" id="txtRemainingStock">0</span> unit
                                </div>
                            </div>

                            <div id="stockAlertMessage" class="mt-2 small fw-semibold" style="display: none;"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label small fw-semibold">Catatan / Keperluan Pengeluaran</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="Contoh: Pengiriman pesanan DO-2026-108 atau pemakaian operasional">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-danger px-4 fw-semibold" id="btnSubmitOut">
                            <i class="bi bi-box-arrow-up me-1"></i> Simpan Transaksi Keluar
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
    const txtSubQty = document.getElementById('txtSubQty');
    const txtRemainingStock = document.getElementById('txtRemainingStock');
    const stockAlert = document.getElementById('stockAlertMessage');
    const btnSubmit = document.getElementById('btnSubmitOut');

    function updateStockCalc() {
        const selectedOpt = productSelect.options[productSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const currentStock = parseInt(selectedOpt.getAttribute('data-stock')) || 0;
            const subQty = parseInt(qtyInput.value) || 0;
            const remaining = currentStock - subQty;

            txtCurrentStock.textContent = currentStock;
            txtSubQty.textContent = subQty;
            txtRemainingStock.textContent = remaining;
            calcCard.style.display = 'block';

            if (currentStock <= 0) {
                calcCard.className = 'card bg-danger-subtle border-danger-subtle mb-3';
                txtRemainingStock.className = 'fw-bold fs-5 text-danger';
                stockAlert.style.display = 'block';
                stockAlert.className = 'mt-2 small fw-bold text-danger';
                stockAlert.innerHTML = '<i class="bi bi-x-octagon-fill me-1"></i> Stok produk saat ini adalah 0 (Habis). Tidak dapat melakukan pengeluaran!';
                btnSubmit.disabled = true;
            } else if (subQty > currentStock) {
                calcCard.className = 'card bg-danger-subtle border-danger-subtle mb-3';
                txtRemainingStock.className = 'fw-bold fs-5 text-danger';
                stockAlert.style.display = 'block';
                stockAlert.className = 'mt-2 small fw-bold text-danger';
                stockAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> Kuantitas melebihi stok yang ada! Defisit: ${Math.abs(remaining)} unit.`;
                btnSubmit.disabled = true;
            } else {
                calcCard.className = 'card bg-light border mb-3';
                txtRemainingStock.className = 'fw-bold fs-5 ' + (remaining <= 5 ? 'text-warning' : 'text-success');
                stockAlert.style.display = 'none';
                btnSubmit.disabled = false;
            }
        } else {
            calcCard.style.display = 'none';
            btnSubmit.disabled = false;
        }
    }

    productSelect.addEventListener('change', updateStockCalc);
    qtyInput.addEventListener('input', updateStockCalc);

    if (productSelect.value) {
        updateStockCalc();
    }
</script>
@endpush
