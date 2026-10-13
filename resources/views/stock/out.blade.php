@extends('layouts.app')

@section('title', 'Pencatatan Barang Keluar (Stock OUT)')
@section('page_title', 'Transaksi Mutasi Stok')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-danger">Pencatatan Stok Keluar (Stock OUT)</h6>
                    <small class="text-muted">Pengurangan kuantitas fisik persediaan dengan proteksi stok negatif (Pessimistic Locking)</small>
                </div>
                <a href="{{ route('stock.history') }}" class="btn btn-sm btn-light border py-1 px-3">
                    Riwayat Mutasi
                </a>
            </div>

            <div class="card-body p-4 p-lg-5">
                <form action="{{ route('stock.out.store') }}" method="POST" id="outForm">
                    @csrf

                    <div class="mb-4">
                        <label for="product_id" class="form-label">Pilih Produk Yang Dikeluarkan <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required autofocus>
                            <option value="">-- Pilih Produk dari Katalog --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-stock="{{ $product->stock }}" 
                                        data-min="{{ $product->minimum_stock }}"
                                        data-sku="{{ $product->sku }}"
                                        data-img="{{ $product->image_url }}"
                                        data-name="{{ $product->name }}"
                                        {{ (old('product_id', $selectedProductId) == $product->id) ? 'selected' : '' }}>
                                    [{{ $product->sku }}] {{ $product->name }} — (Sisa: {{ $product->stock }} unit)
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="form-label">Jumlah Kuantitas Keluar <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" min="1" name="quantity" id="quantity" 
                                   class="form-control fs-4 fw-bold text-danger @error('quantity') is-invalid @enderror" 
                                   placeholder="0" value="{{ old('quantity', 1) }}" required>
                            <span class="input-group-text bg-light text-muted fw-semibold">Unit Fisik</span>
                        </div>
                        @error('quantity')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Live Dynamic Stock Deficit / Deduction Calculator with Real Photo -->
                    <div class="card border-0 mb-4 p-3" id="calcCard" style="display: none; background: #fff1f2; border: 1px solid #fecdd3 !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="product-thumb-container" style="width: 54px; height: 54px;">
                                <img src="" id="calcProductImg" alt="Thumbnail" loading="lazy">
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-bold text-dark d-block" id="calcProductName" style="font-size: 0.88rem;">Nama Produk</span>
                                <span class="badge-sku" id="calcProductSku">SKU</span>
                            </div>
                        </div>
                        <hr class="my-3" style="border-color: #fecdd3;">
                        <div class="d-flex align-items-center justify-content-between text-center">
                            <div>
                                <small class="text-secondary d-block">Stok Saat Ini</small>
                                <span class="fw-bold fs-6 text-dark" id="txtCurrentStock">0</span> unit
                            </div>
                            <div class="text-danger fs-5 fw-bold">&minus;</div>
                            <div>
                                <small class="text-secondary d-block">Jumlah Keluar</small>
                                <span class="fw-bold fs-6 text-danger" id="txtSubQty">0</span> unit
                            </div>
                            <div class="text-secondary fs-5 fw-bold">=</div>
                            <div>
                                <small class="text-secondary d-block">Sisa Stok Akhir</small>
                                <span class="fw-bold fs-5" id="txtRemainingStock">0</span> unit
                            </div>
                        </div>

                        <div id="stockAlertMessage" class="mt-3 p-2 rounded-2 small fw-bold" style="display: none;"></div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">Catatan / Keperluan Pengeluaran</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="Contoh: Pengiriman DO-2026-108, alokasi perakitan, atau pemakaian divisi">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-danger px-4 shadow-sm" id="btnSubmitOut">
                            Simpan Transaksi Keluar
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
    const calcProductImg = document.getElementById('calcProductImg');
    const calcProductName = document.getElementById('calcProductName');
    const calcProductSku = document.getElementById('calcProductSku');
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
            const imgUrl = selectedOpt.getAttribute('data-img') || '';
            const pName = selectedOpt.getAttribute('data-name') || '';
            const pSku = selectedOpt.getAttribute('data-sku') || '';

            calcProductImg.src = imgUrl;
            calcProductName.textContent = pName;
            calcProductSku.textContent = pSku;
            txtCurrentStock.textContent = currentStock;
            txtSubQty.textContent = subQty;
            txtRemainingStock.textContent = remaining;
            calcCard.style.display = 'block';

            if (currentStock <= 0) {
                txtRemainingStock.className = 'fw-bold fs-5 text-danger';
                stockAlert.style.display = 'block';
                stockAlert.style.background = '#ffe4e6';
                stockAlert.style.color = '#9f1239';
                stockAlert.textContent = 'Peringatan: Stok produk saat ini adalah 0 (Habis). Transaksi pengeluaran tidak dapat diproses!';
                btnSubmit.disabled = true;
            } else if (subQty > currentStock) {
                txtRemainingStock.className = 'fw-bold fs-5 text-danger';
                stockAlert.style.display = 'block';
                stockAlert.style.background = '#ffe4e6';
                stockAlert.style.color = '#9f1239';
                stockAlert.textContent = `Peringatan: Permintaan kuantitas (${subQty}) melebihi saldo fisik (${currentStock})! Defisit: ${Math.abs(remaining)} unit.`;
                btnSubmit.disabled = true;
            } else {
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
