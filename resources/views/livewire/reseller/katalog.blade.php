<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1" style="font-weight: 700; color: var(--text-primary);">Katalog Produk</h3>
            <p class="mb-0" style="color: var(--text-secondary);">Jelajahi produk dan tambahkan ke keranjang</p>
        </div>

        <div class="input-group" style="max-width: 350px;">
            <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                <i class="fas fa-search" style="color: var(--text-muted);"></i>
            </span>
            <input type="text" class="form-control" placeholder="Cari produk atau varian..."
                wire:model.live.debounce.300ms="search">
        </div>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert d-flex align-items-center gap-2 mb-4" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; color: var(--success-color); padding: 1rem 1.25rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Product Grid --}}
    <div class="row g-4">
        @forelse ($produks as $produk)
            <div class="col-6 col-md-4 col-lg-3" wire:key="produk-{{ $produk->id }}">
                <div class="modern-card h-100 d-flex flex-column" style="padding: 0; overflow: hidden; cursor: default;">
                    {{-- Product Image --}}
                    <div style="height: 180px; background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        @if ($produk->gambar)
                            <img src="{{ Storage::url($produk->gambar) }}" alt="{{ $produk->nama_produk }}"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <i class="fas fa-birthday-cake" style="font-size: 3rem; color: var(--text-muted);"></i>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="d-flex flex-column flex-grow-1 p-3">
                        <h6 class="mb-1" style="font-weight: 600; color: var(--text-primary); line-height: 1.3;">
                            {{ $produk->nama_produk }}
                        </h6>
                        @if($produk->varian_rasa)
                            <small style="color: var(--text-muted);">{{ $produk->varian_rasa }}</small>
                        @endif

                        <div class="mt-2">
                            <div style="font-weight: 700; color: var(--primary-color); font-size: 1.05rem;">
                                Rp {{ number_format($produk->harga_jual_satuan, 0, ',', '.') }}
                                <small style="font-weight: 400; color: var(--text-muted);">/{{ $produk->unit_kecil ?? 'pcs' }}</small>
                            </div>
                            @if($produk->harga_jual && $produk->unit_besar)
                                <small style="color: var(--text-secondary);">
                                    Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}/{{ $produk->unit_besar }}
                                </small>
                            @endif
                        </div>

                        {{-- Stock Info --}}
                        <div class="mt-2">
                            @if($produk->total_stok > 0)
                                <span style="font-size: 0.75rem; padding: 3px 8px; border-radius: 50px; background: rgba(16,185,129,0.1); color: var(--success-color); font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> Stok: {{ $produk->stok_text }}
                                </span>
                            @else
                                <span style="font-size: 0.75rem; padding: 3px 8px; border-radius: 50px; background: rgba(239,68,68,0.1); color: var(--danger-color); font-weight: 600;">
                                    <i class="fas fa-times-circle"></i> Habis
                                </span>
                            @endif
                        </div>

                        {{-- Add to Cart Button --}}
                        <div class="mt-auto pt-3">
                            <button wire:click="openCartModal({{ $produk->id }})"
                                class="btn w-100"
                                style="background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.6rem; font-size: 0.85rem; transition: all 0.2s;"
                                @if($produk->total_stok <= 0) disabled style="opacity: 0.5; cursor: not-allowed; background: var(--text-muted); color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.6rem; font-size: 0.85rem;" @endif>
                                <i class="fas fa-cart-plus me-1"></i>
                                {{ $produk->total_stok > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-box-open mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-0 fs-5">Produk tidak ditemukan</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($produks->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $produks->links() }}
        </div>
    @endif

    {{-- Add to Cart Modal --}}
    @if ($showCartModal)
        @php
            $modalProduk = \App\Models\Produk::withSum('persediaan', 'jumlah')->find($selectedProdukId);
        @endphp
        @if($modalProduk)
            <div class="modal-backdrop-custom" wire:click.self="closeCartModal">
                <div class="modal-content-custom" wire:click.stop style="max-width: 420px;">
                    <div class="modal-header-custom">
                        <h5 class="modal-title-custom">Pesan Produk</h5>
                        <button type="button" class="modal-close-btn" wire:click="closeCartModal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-4" style="padding: 1rem; background: var(--bg-tertiary); border-radius: 12px;">
                        @if($modalProduk->gambar)
                            <img src="{{ Storage::url($modalProduk->gambar) }}" alt="{{ $modalProduk->nama_produk }}"
                                style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px;">
                        @else
                            <div style="width: 60px; height: 60px; border-radius: 10px; background: var(--bg-secondary); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-birthday-cake" style="color: var(--text-muted); font-size: 1.5rem;"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-semibold" style="color: var(--text-primary);">{{ $modalProduk->nama_produk }}</div>
                            <div style="color: var(--primary-color); font-weight: 700;">
                                Rp {{ number_format($modalProduk->harga_jual_satuan, 0, ',', '.') }}
                                <small style="font-weight: 400; color: var(--text-muted);">/{{ $modalProduk->unit_kecil ?? 'pcs' }}</small>
                            </div>
                            <div style="font-size: 0.75rem; margin-top: 4px; color: var(--text-secondary);">
                                <i class="fas fa-box" style="color: var(--text-muted);"></i> Stok Tersedia: <strong style="color: var(--text-primary);">{{ $modalProduk->stok_text }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        @if($modalProduk->unit_besar && $modalProduk->tingkat_konversi > 1)
                            <div class="row g-2" x-data="{ 
                                qtyKecil: @entangle('qty'),
                                konversi: {{ $modalProduk->tingkat_konversi }},
                                updateKecil(val) {
                                    let newQty = val ? Math.floor(parseFloat(val) * this.konversi) : '';
                                    if (this.qtyKecil !== newQty) {
                                        this.qtyKecil = newQty;
                                    }
                                },
                                updateBesar(val) {
                                    let newBesar = val ? (val / this.konversi) : '';
                                    if (parseFloat($refs.inputBesar.value || 0) !== parseFloat(newBesar || 0)) {
                                        $refs.inputBesar.value = newBesar;
                                    }
                                }
                            }" x-init="
                                $watch('qtyKecil', value => updateBesar(value));
                                updateBesar(qtyKecil);
                            ">
                                <div class="col-6">
                                    <label for="qty_besar" class="form-label">Jumlah ({{ $modalProduk->unit_besar }})</label>
                                    <input type="number" class="form-control" id="qty_besar" x-ref="inputBesar" @input="updateKecil($event.target.value)" min="0" max="{{ $modalProduk->total_stok / $modalProduk->tingkat_konversi }}" step="any">
                                </div>
                                <div class="col-6">
                                    <label for="qty" class="form-label">Jumlah ({{ $modalProduk->unit_kecil ?? 'pcs' }})</label>
                                    <input type="number" class="form-control" id="qty" x-model="qtyKecil" @input="updateBesar($event.target.value)" min="1" max="{{ $modalProduk->total_stok }}">
                                </div>
                            </div>
                            @error('qty') <span style="font-size: 0.75rem; color: var(--danger-color); display: block; margin-top: 4px;">{{ $message }}</span> @enderror
                        @else
                            <label for="qty" class="form-label">Jumlah ({{ $modalProduk->unit_kecil ?? 'pcs' }})</label>
                            <input type="number" class="form-control" id="qty" wire:model="qty" min="1" max="{{ $modalProduk->total_stok }}" value="1">
                            @error('qty') <span style="font-size: 0.75rem; color: var(--danger-color); display: block; margin-top: 4px;">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn" style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 500; padding: 0.6rem 1.25rem;"
                            wire:click="closeCartModal">
                            Batal
                        </button>
                        <button type="button" class="btn" style="background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.6rem 1.25rem;"
                            wire:click="addToCart">
                            <i class="fas fa-cart-plus me-1"></i> Keranjang
                        </button>
                        <button type="button" class="btn" style="background: var(--success-color); color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.6rem 1.25rem;"
                            wire:click="directOrder">
                            <i class="fas fa-shopping-bag me-1"></i> Pesan Langsung
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
