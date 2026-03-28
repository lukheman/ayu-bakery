<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1" style="font-weight: 700; color: var(--text-primary);">Keranjang Belanja</h3>
            <p class="mb-0" style="color: var(--text-secondary);">Pilih produk dan checkout pesanan Anda</p>
        </div>
        <a href="{{ route('reseller.katalog') }}" class="btn"
            style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 500; padding: 0.6rem 1.25rem;">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Katalog
        </a>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert d-flex align-items-center gap-2 mb-4" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; color: var(--success-color); padding: 1rem 1.25rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert d-flex align-items-center gap-2 mb-4" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 12px; color: var(--danger-color); padding: 1rem 1.25rem;">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    @if ($items->count() > 0)
        <div class="row g-4">
            {{-- Cart Items --}}
            <div class="col-lg-8">
                <div class="modern-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0" style="font-weight: 600; color: var(--text-primary);">
                            Item Keranjang ({{ $items->count() }})
                        </h5>
                        {{-- Select All --}}
                        <button wire:click="toggleSelectAll" class="btn btn-sm"
                            style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 500; padding: 0.4rem 1rem; font-size: 0.8rem;">
                            @if(count($selectedItems) === $items->count())
                                <i class="fas fa-check-square me-1" style="color: var(--primary-color);"></i> Batal Pilih Semua
                            @else
                                <i class="far fa-square me-1"></i> Pilih Semua
                            @endif
                        </button>
                    </div>

                    @foreach ($items as $item)
                        @php $isSelected = in_array($item->id, $selectedItems); @endphp
                        <div class="d-flex align-items-center gap-3 py-3" wire:key="cart-item-{{ $item->id }}"
                            style="border-bottom: 1px solid var(--border-light); {{ $isSelected ? 'background: rgba(99,102,241,0.04); margin: 0 -1.75rem; padding-left: 1.75rem; padding-right: 1.75rem;' : '' }}">

                            {{-- Checkbox --}}
                            <label style="cursor: pointer; flex-shrink: 0;">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}"
                                    style="width: 18px; height: 18px; accent-color: var(--primary-color); cursor: pointer;">
                            </label>

                            {{-- Product Image --}}
                            @if($item->produk->gambar)
                                <img src="{{ Storage::url($item->produk->gambar) }}" alt="{{ $item->produk->nama_produk }}"
                                    style="width: 64px; height: 64px; object-fit: cover; border-radius: 10px; flex-shrink: 0;">
                            @else
                                <div style="width: 64px; height: 64px; border-radius: 10px; background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-birthday-cake" style="color: var(--text-muted); font-size: 1.25rem;"></i>
                                </div>
                            @endif

                            {{-- Product Info --}}
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $item->produk->nama_produk }}</div>
                                <div style="color: var(--primary-color); font-weight: 600; font-size: 0.9rem;">
                                    Rp {{ number_format($item->produk->harga_jual_satuan, 0, ',', '.') }}
                                    <small style="font-weight: 400; color: var(--text-muted);">/{{ $item->produk->unit_kecil ?? 'pcs' }}</small>
                                </div>
                            </div>

                            {{-- Qty Controls --}}
                            <div class="d-flex align-items-center gap-2">
                                <button wire:click="decrementQty({{ $item->id }})" class="btn btn-sm"
                                    style="width: 32px; height: 32px; padding: 0; border-radius: 8px; background: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                </button>
                                <input type="number" wire:change="updateQty({{ $item->id }}, $event.target.value)"
                                    value="{{ $item->jumlah }}" min="1"
                                    style="width: 50px; text-align: center; border: 1px solid var(--border-color); border-radius: 8px; padding: 4px; background: var(--input-bg); color: var(--text-primary); font-weight: 600;">
                                <button wire:click="incrementQty({{ $item->id }})" class="btn btn-sm"
                                    style="width: 32px; height: 32px; padding: 0; border-radius: 8px; background: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                                </button>
                            </div>

                            {{-- Subtotal --}}
                            <div class="text-end" style="min-width: 120px;">
                                <div style="font-weight: 700; color: var(--text-primary);">
                                    Rp {{ number_format($item->jumlah * $item->produk->harga_jual_satuan, 0, ',', '.') }}
                                </div>
                            </div>

                            {{-- Remove --}}
                            <button wire:click="removeItem({{ $item->id }})"
                                class="btn btn-sm"
                                style="width: 32px; height: 32px; padding: 0; border-radius: 8px; background: rgba(239,68,68,0.1); border: none; color: var(--danger-color); display: flex; align-items: center; justify-content: center;"
                                title="Hapus item">
                                <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="col-lg-4">
                <div class="modern-card" style="position: sticky; top: 80px;">
                    <h5 class="mb-4" style="font-weight: 600; color: var(--text-primary);">Ringkasan Pesanan</h5>

                    @if($selectedCount > 0)
                        <div class="d-flex justify-content-between mb-2" style="color: var(--text-secondary);">
                            <span>Produk Dipilih</span>
                            <span class="fw-semibold">{{ $selectedCount }} dari {{ $items->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="color: var(--text-secondary);">
                            <span>Total Item Dipilih</span>
                            <span>{{ $items->whereIn('id', $selectedItems)->sum('jumlah') }} pcs</span>
                        </div>

                        <hr style="border-color: var(--border-color);">

                        <div class="d-flex justify-content-between mb-4">
                            <span style="font-weight: 600; color: var(--text-primary); font-size: 1.1rem;">Total Checkout</span>
                            <span style="font-weight: 700; color: var(--primary-color); font-size: 1.1rem;">
                                Rp {{ number_format($selectedTotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @else
                        <div class="text-center py-3" style="color: var(--text-muted);">
                            <i class="fas fa-hand-pointer mb-2" style="font-size: 1.5rem;"></i>
                            <p class="mb-0" style="font-size: 0.9rem;">Pilih produk untuk melihat ringkasan</p>
                        </div>
                        <hr style="border-color: var(--border-color);">
                    @endif

                    {{-- Catatan --}}
                    <div class="mb-4">
                        <label for="catatan" class="form-label" style="font-size: 0.9rem;">Catatan (opsional)</label>
                        <textarea class="form-control" id="catatan" wire:model="catatan" rows="2"
                            placeholder="Tambahkan catatan untuk pesanan..." style="font-size: 0.9rem;"></textarea>
                    </div>

                    <button wire:click="openCheckoutModal" class="btn w-100"
                        style="background: {{ $selectedCount > 0 ? 'var(--success-color)' : 'var(--text-muted)' }}; color: white; border: none; border-radius: 10px; font-weight: 700; padding: 0.85rem; font-size: 1rem; transition: all 0.2s; {{ $selectedCount === 0 ? 'cursor: not-allowed; opacity: 0.7;' : '' }}"
                        {{ $selectedCount === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-shopping-bag me-2"></i>
                        Checkout{{ $selectedCount > 0 ? ' (' . $selectedCount . ' produk)' : '' }}
                    </button>
                </div>
            </div>
        </div>
    @else
        {{-- Empty Cart --}}
        <div class="modern-card text-center py-5">
            <i class="fas fa-shopping-cart mb-3" style="font-size: 4rem; color: var(--text-muted);"></i>
            <h5 style="color: var(--text-primary); font-weight: 600;">Keranjang Belanja Kosong</h5>
            <p style="color: var(--text-secondary);">Belum ada produk di keranjang. Mulai belanja sekarang!</p>
            <a href="{{ route('reseller.katalog') }}" class="btn mt-2"
                style="background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.7rem 2rem;">
                <i class="fas fa-store me-1"></i> Lihat Katalog
            </a>
        </div>
    @endif

    {{-- Checkout Confirmation Modal --}}
    @if ($showCheckoutModal)
        <div class="modal-backdrop-custom" wire:click.self="closeCheckoutModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 420px;">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">Konfirmasi Checkout</h5>
                    <button type="button" class="modal-close-btn" wire:click="closeCheckoutModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="text-center mb-4">
                    <div style="width: 64px; height: 64px; margin: 0 auto 1rem; background: rgba(16,185,129,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-bag" style="font-size: 1.5rem; color: var(--success-color);"></i>
                    </div>
                    <p style="color: var(--text-secondary);">
                        Anda akan membuat pesanan dengan <strong>{{ $selectedCount }}</strong> produk senilai
                        <strong style="color: var(--primary-color);">Rp {{ number_format($selectedTotal, 0, ',', '.') }}</strong>.
                    </p>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">
                        Item yang dipilih akan dihapus dari keranjang setelah checkout.
                    </p>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn"
                        style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 500; padding: 0.6rem 1.25rem;"
                        wire:click="closeCheckoutModal">
                        Batal
                    </button>
                    <button type="button" class="btn"
                        style="background: var(--success-color); color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.6rem 1.25rem;"
                        wire:click="checkout">
                        <i class="fas fa-check me-1"></i> Konfirmasi Pesanan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
