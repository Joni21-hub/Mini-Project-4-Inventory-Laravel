@extends('layouts.app')

@section('title', 'Pencatatan Barang Masuk (Stock IN)')
@section('page_title', 'Transaksi Mutasi Stok')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-success">Pencatatan Stok Masuk (Stock IN)</h6>
                    <small class="text-muted">Menambahkan saldo fisik persediaan produk secara atomik & transaksional</small>
                </div>
                <a href="{{ route('stock.history') }}" class="btn btn-sm btn-light border py-1 px-3">
                    Riwayat Mutasi
                </a>
            </div>

            <div class="card-body p-4 p-lg-5">
                <form action="{{ route('stock.in.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="product_id" class="form-label">Pilih Produk Yang Diterima <span class="text-danger">*</span></label>
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
                        <label for="quantity" class="form-label">Jumlah Kuantitas Masuk <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" min="1" name="quantity" id="quantity" 
                                   class="form-control fs-4 fw-bold text-success @error('quantity') is-invalid @enderror" 
                                   placeholder="0" value="{{ old('quantity', 1) }}" required>
                            <span class="input-group-text bg-light text-muted fw-semibold">Unit Fisik</span>
                        </div>
                        @error('quantity')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Live Dynamic Stock Calculator Preview with Product Photography -->
                    <div class="card border-0 mb-4 p-3" id="calcCard" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0 !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="product-thumb-container" style="width: 54px; height: 54px;">
                                <img src="" id="calcProductImg" alt="Thumbnail" loading="lazy">
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-bold text-dark d-block" id="calcProductName" style="font-size: 0.88rem;">Nama Produk</span>
                                <span class="badge-sku" id="calcProductSku">SKU</span>
                            </div>
                        </div>
                        <hr class="my-3" style="border-color: #bbf7d0;">
                        <div class="d-flex align-items-center justify-content-between text-center">
                            <div>
                                <small class="text-secondary d-block">Stok Awal</small>
                                <span class="fw-bold fs-6 text-dark" id="txtCurrentStock">0</span> unit
                            </div>
                            <div class="text-success fs-5 fw-bold">+</div>
                            <div>
                                <small class="text-secondary d-block">Jumlah Masuk</small>
                                <span class="fw-bold fs-6 text-success" id="txtAddQty">0</span> unit
                            </div>
                            <div class="text-secondary fs-5 fw-bold">=</div>
                            <div>
                                <small class="text-secondary d-block">Proyeksi Saldo</small>
                                <span class="fw-bold fs-5 text-success" id="txtNewStock">0</span> unit
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">Catatan / Referensi Transaksi</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="Contoh: Penerimaan barang dari PO-2026-004 atau restock batch mingguan">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            Simpan Transaksi Masuk
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
    const txtAddQty = document.getElementById('txtAddQty');
    const txtNewStock = document.getElementById('txtNewStock');

    function updateStockCalc() {
        const selectedOpt = productSelect.options[productSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const currentStock = parseInt(selectedOpt.getAttribute('data-stock')) || 0;
            const addQty = parseInt(qtyInput.value) || 0;
            const newStock = currentStock + addQty;
            const imgUrl = selectedOpt.getAttribute('data-img') || '';
            const pName = selectedOpt.getAttribute('data-name') || '';
            const pSku = selectedOpt.getAttribute('data-sku') || '';

            calcProductImg.src = imgUrl;
            calcProductName.textContent = pName;
            calcProductSku.textContent = pSku;
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

    if (productSelect.value) {
        updateStockCalc();
    }
</script>
@endpush
