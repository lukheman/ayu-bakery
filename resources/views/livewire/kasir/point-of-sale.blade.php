<div class="d-flex" style="height: 100%;">
    {{-- LEFT: Product List --}}
    <div style="flex: 1; display: flex; flex-direction: column; border-right: 1px solid var(--pos-border);">
        {{-- Search --}}
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--pos-border);">
            <div class="input-group">
                <span class="input-group-text"
                    style="background: var(--pos-surface-2); border-color: var(--pos-border); color: var(--pos-text-muted);">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" wire:model.live.debounce.200ms="search"
                    placeholder="Cari produk / kode produk..." style="font-size: 0.95rem;">
            </div>
        </div>

        {{-- Product Grid --}}
        <div style="flex: 1; overflow-y: auto; padding: 1rem 1.25rem;">
            <div class="row g-2">
                @forelse($produks as $produk)
                    <div class="col-xl-3 col-lg-4 col-md-6" wire:key="produk-{{ $produk->id }}">
                        <div
                            style="background: var(--pos-surface); border: 1px solid var(--pos-border); border-radius: 12px; overflow: hidden; height: 100%; display: flex; flex-direction: column;">
                            {{-- Product image/icon --}}
                            <div style="padding: 0.75rem 0.75rem 0.5rem;">
                                @if($produk->gambar)
                                    <img src="{{ Storage::url($produk->gambar) }}" alt="{{ $produk->nama_produk }}"
                                        style="width: 100%; height: 72px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <div
                                        style="width: 100%; height: 72px; background: var(--pos-surface-2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-birthday-cake"
                                            style="font-size: 1.5rem; color: var(--pos-text-muted);"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Product info --}}
                            <div style="padding: 0 0.75rem 0.5rem; flex: 1;">
                                <div
                                    style="font-weight: 600; font-size: 0.8rem; color: var(--pos-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $produk->nama_produk }}
                                </div>
                                @if($produk->varian_rasa)
                                    <div
                                        style="font-size: 0.7rem; color: var(--pos-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $produk->varian_rasa }}
                                    </div>
                                @endif
                                @if($produk->kode_produk)
                                    <div style="font-size: 0.65rem; color: var(--pos-text-muted); margin-top: 2px;">
                                        <i class="fas fa-barcode me-1"></i>{{ $produk->kode_produk }}
                                    </div>
                                @endif
                                {{-- Persediaan / Stock info --}}
                                <div style="margin-top: 4px;">
                                    @if($produk->total_stok > 0)
                                        <span
                                            style="font-size: 0.62rem; font-weight: 600; color: #10b981; background: rgba(16,185,129,0.12); padding: 1px 6px; border-radius: 4px;">
                                            <i class="fas fa-boxes me-1"></i>{{ $produk->stok_text }}
                                        </span>
                                    @else
                                        <span
                                            style="font-size: 0.62rem; font-weight: 600; color: #ef4444; background: rgba(239,68,68,0.12); padding: 1px 6px; border-radius: 4px;">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Stok habis
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Unit buttons --}}
                            <div style="padding: 0 0.75rem 0.75rem; display: flex; flex-direction: column; gap: 4px;">
                                {{-- Unit Besar button --}}
                                <button wire:click="addToCart({{ $produk->id }}, 'besar')"
                                    @if($produk->total_stok <= 0) disabled @endif
                                    style="width: 100%; padding: 5px 8px; border-radius: 7px; border: 1px solid {{ $produk->total_stok > 0 ? 'var(--pos-primary)' : 'var(--pos-border)' }}; background: {{ $produk->total_stok > 0 ? 'rgba(99,102,241,0.1)' : 'var(--pos-surface-2)' }}; color: {{ $produk->total_stok > 0 ? 'var(--pos-primary-light)' : 'var(--pos-text-muted)' }}; cursor: {{ $produk->total_stok > 0 ? 'pointer' : 'not-allowed' }}; font-size: 0.75rem; font-weight: 600; text-align: left; transition: all 0.15s; {{ $produk->total_stok <= 0 ? 'opacity: 0.5;' : '' }}"
                                    @if($produk->total_stok > 0)
                                        onmouseover="this.style.background='var(--pos-primary)'; this.style.color='white'"
                                        onmouseout="this.style.background='rgba(99,102,241,0.1)'; this.style.color='var(--pos-primary-light)'"
                                    @endif>
                                    <span style="font-size: 0.65rem; opacity: 0.8;">📦
                                        {{ $produk->unit_besar ?? 'unit' }}</span><br>
                                    <span>Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                                </button>

                                @if($produk->unit_kecil && $produk->harga_jual_satuan > 0)
                                    {{-- Unit Kecil button --}}
                                    <button wire:click="addToCart({{ $produk->id }}, 'kecil')"
                                        @if($produk->total_stok <= 0) disabled @endif
                                        style="width: 100%; padding: 5px 8px; border-radius: 7px; border: 1px solid var(--pos-border); background: var(--pos-surface-2); color: {{ $produk->total_stok > 0 ? 'var(--pos-text-secondary)' : 'var(--pos-text-muted)' }}; cursor: {{ $produk->total_stok > 0 ? 'pointer' : 'not-allowed' }}; font-size: 0.75rem; font-weight: 600; text-align: left; transition: all 0.15s; {{ $produk->total_stok <= 0 ? 'opacity: 0.5;' : '' }}"
                                        @if($produk->total_stok > 0)
                                            onmouseover="this.style.borderColor='var(--pos-accent)'; this.style.color='var(--pos-accent)'"
                                            onmouseout="this.style.borderColor='var(--pos-border)'; this.style.color='var(--pos-text-secondary)'"
                                        @endif>
                                        <span style="font-size: 0.65rem; opacity: 0.8;">🍫 {{ $produk->unit_kecil }}</span><br>
                                        <span>Rp {{ number_format($produk->harga_jual_satuan, 0, ',', '.') }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-search" style="font-size: 2.5rem; color: var(--pos-text-muted);"></i>
                        <p class="mt-2" style="color: var(--pos-text-muted);">Produk tidak ditemukan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RIGHT: Cart & Payment --}}
    <div style="width: 380px; display: flex; flex-direction: column; background: var(--pos-surface);">
        {{-- Cart Header --}}
        <div
            style="padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--pos-border); display: flex; justify-content: space-between; align-items: center;">
            <h6 style="margin: 0; font-weight: 700; color: var(--pos-text);">
                <i class="fas fa-shopping-cart me-2" style="color: var(--pos-primary);"></i>
                Keranjang
                @if($this->cartCount > 0)
                    <span
                        style="font-size: 0.7rem; background: var(--pos-primary); color: white; padding: 2px 7px; border-radius: 50px; margin-left: 4px;">{{ $this->cartCount }}</span>
                @endif
            </h6>
            @if(count($cart) > 0)
                <button wire:click="clearCart"
                    style="background: none; border: none; color: var(--pos-danger); font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-trash me-1"></i>Kosongkan
                </button>
            @endif
        </div>

        {{-- Cart Items --}}
        <div style="flex: 1; overflow-y: auto; padding: 0.5rem 1rem;">
            @forelse($cart as $key => $item)
                <div style="padding: 0.65rem 0; border-bottom: 1px solid rgba(71,85,105,0.4);" wire:key="cart-{{ $key }}">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div style="flex: 1; min-width: 0;">
                            <div
                                style="font-weight: 600; font-size: 0.85rem; color: var(--pos-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $item['nama_produk'] }}
                            </div>
                            <div style="font-size: 0.72rem; color: var(--pos-text-muted);">
                                Rp {{ number_format($item['harga'], 0, ',', '.') }} / {{ $item['unit_label'] }}
                                @if($item['unit'] === 'besar' && isset($item['qty_konversi']) && $item['qty_konversi'] > 1)
                                    <span style="opacity: 0.6;">(= {{ $item['qty_konversi'] }} unit kecil)</span>
                                @endif
                            </div>
                        </div>
                        <button wire:click="removeItem('{{ $key }}')"
                            style="background: none; border: none; color: var(--pos-danger); padding: 0 0 0 8px; cursor: pointer; font-size: 0.8rem;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-1">
                            <button wire:click="decrementQty('{{ $key }}')"
                                style="width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--pos-border); background: var(--pos-surface-2); color: var(--pos-text); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" value="{{ $item['jumlah'] }}" min="1"
                                wire:change="updateQty('{{ $key }}', $event.target.value)"
                                style="width: 45px; text-align: center; background: var(--pos-bg); border: 1px solid var(--pos-border); border-radius: 6px; color: var(--pos-text); font-weight: 700; font-size: 0.85rem; padding: 3px;">
                            <button wire:click="incrementQty('{{ $key }}')"
                                style="width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--pos-border); background: var(--pos-surface-2); color: var(--pos-text); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                <i class="fas fa-plus"></i>
                            </button>
                            <span
                                style="font-size: 0.72rem; color: var(--pos-text-muted); margin-left: 2px;">{{ $item['unit_label'] }}</span>
                        </div>
                        <span
                            style="font-weight: 700; font-size: 0.9rem; color: var(--pos-primary-light); font-family: 'JetBrains Mono', monospace;">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-shopping-basket" style="font-size: 2rem; color: var(--pos-text-muted);"></i>
                    <p class="mt-2 mb-0" style="color: var(--pos-text-muted); font-size: 0.85rem;">Keranjang kosong</p>
                    <small style="color: var(--pos-text-muted);">Klik tombol produk untuk menambahkan</small>
                </div>
            @endforelse
        </div>

        {{-- Payment Section --}}
        @if(count($cart) > 0)
            <div
                style="border-top: 2px solid var(--pos-primary); padding: 1rem 1.25rem; background: rgba(99,102,241,0.03);">
                {{-- Flash --}}
                @if(session('error'))
                    <div
                        style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; padding: 0.5rem 0.75rem; margin-bottom: 0.75rem; color: var(--pos-danger); font-size: 0.8rem;">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
                    </div>
                @endif

                {{-- Total --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span style="font-weight: 600; font-size: 0.95rem; color: var(--pos-text-secondary);">TOTAL</span>
                    <span
                        style="font-weight: 900; font-size: 1.5rem; color: var(--pos-text); font-family: 'JetBrains Mono', monospace;">
                        Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Payment Method --}}
                <div class="mb-2">
                    <select wire:model="metodePembayaran" class="form-select"
                        style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                        <option value="tunai">💵 Tunai</option>
                        <option value="transfer">💳 Transfer</option>
                    </select>
                </div>

                {{-- Amount Paid --}}
                <div class="mb-2">
                    <label
                        style="font-size: 0.75rem; color: var(--pos-text-muted); font-weight: 600; margin-bottom: 4px; display: block;">BAYAR</label>
                    <input type="number" wire:model.live="bayar" class="form-control" placeholder="Masukkan jumlah bayar"
                        style="font-family: 'JetBrains Mono', monospace; font-size: 1.1rem; font-weight: 700; padding: 0.6rem 0.75rem;">
                </div>

                {{-- Quick Amount Buttons --}}
                <div class="d-flex gap-1 mb-2 flex-wrap">
                    @foreach([1000, 2000, 5000, 10000, 20000, 50000, 100000] as $nominal)
                        <button wire:click="$set('bayar', {{ $nominal }})"
                            style="flex: 1; min-width: 60px; padding: 4px 6px; font-size: 0.65rem; font-weight: 600; border-radius: 6px; border: 1px solid var(--pos-border); background: var(--pos-surface-2); color: var(--pos-text-secondary); cursor: pointer; transition: all 0.1s;"
                            onmouseover="this.style.borderColor='var(--pos-primary)'; this.style.color='var(--pos-primary-light)'"
                            onmouseout="this.style.borderColor='var(--pos-border)'; this.style.color='var(--pos-text-secondary)'">
                            {{ number_format($nominal / 1000) }}rb
                        </button>
                    @endforeach
                    <button wire:click="$set('bayar', {{ $this->grandTotal }})"
                        style="flex: 1; min-width: 60px; padding: 4px 6px; font-size: 0.65rem; font-weight: 700; border-radius: 6px; border: 1px solid var(--pos-success); background: rgba(16,185,129,0.1); color: var(--pos-success); cursor: pointer;">
                        PAS
                    </button>
                </div>

                {{-- Change --}}
                @if($bayar >= $this->grandTotal && $bayar > 0)
                    <div class="d-flex justify-content-between align-items-center mb-3"
                        style="padding: 0.5rem 0.75rem; background: rgba(16,185,129,0.1); border-radius: 8px; border: 1px solid rgba(16,185,129,0.2);">
                        <span style="font-weight: 500; font-size: 0.85rem; color: var(--pos-success);">KEMBALIAN</span>
                        <span
                            style="font-weight: 800; font-size: 1.1rem; color: var(--pos-success); font-family: 'JetBrains Mono', monospace;">
                            Rp {{ number_format($this->kembalian, 0, ',', '.') }}
                        </span>
                    </div>
                @endif

                {{-- Pay Button --}}
                <button wire:click="processPayment" class="w-100" @if($bayar < $this->grandTotal) disabled @endif
                    style="padding: 0.85rem; font-size: 1rem; font-weight: 800; border-radius: 10px; border: none; cursor: pointer; transition: all 0.15s;
                                        {{ $bayar >= $this->grandTotal ? 'background: var(--pos-success); color: white;' : 'background: var(--pos-surface-2); color: var(--pos-text-muted); cursor: not-allowed;' }}">
                    <i class="fas fa-check-circle me-2"></i>BAYAR
                </button>
            </div>
        @endif
    </div>

    {{-- Receipt Modal --}}
    @if($showReceipt && $receiptData)
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1050; display: flex; align-items: center; justify-content: center; animation: fadeIn 0.2s;"
            wire:click.self="closeReceipt">
            <div style="background: white; border-radius: 16px; width: 340px; padding: 0; overflow: hidden;"
                wire:click.stop>
                {{-- Receipt Content --}}
                <div id="receipt-content" style="color: #1e293b; padding: 1.5rem;">
                    <div class="text-center mb-3">
                        <div style="font-weight: 800; font-size: 1.1rem; color: #1e293b;">🎂 Ayu Bakery</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">Struk Pembayaran</div>
                        <hr style="border-style: dashed; border-color: #e2e8f0; margin: 0.75rem 0;">
                    </div>

                    <div style="font-size: 0.8rem; margin-bottom: 0.75rem;">
                        <div class="d-flex justify-content-between"><span style="color: #64748b;">No. Struk</span><span
                                style="font-weight: 600;">{{ $receiptData['nomor_struk'] }}</span></div>
                        <div class="d-flex justify-content-between"><span
                                style="color: #64748b;">Tanggal</span><span>{{ $receiptData['tanggal'] }}</span></div>
                        <div class="d-flex justify-content-between"><span
                                style="color: #64748b;">Kasir</span><span>{{ $receiptData['kasir'] }}</span></div>
                        <div class="d-flex justify-content-between"><span
                                style="color: #64748b;">Pembayaran</span><span>{{ $receiptData['metode'] }}</span></div>
                    </div>

                    <hr style="border-style: dashed; border-color: #e2e8f0; margin: 0.5rem 0;">

                    @foreach($receiptData['items'] as $item)
                        <div style="font-size: 0.8rem; margin-bottom: 6px;">
                            <div style="font-weight: 600;">{{ $item['nama_produk'] }}</div>
                            <div class="d-flex justify-content-between" style="color: #64748b;">
                                <span>{{ $item['jumlah'] }} {{ $item['unit_label'] }} × Rp
                                    {{ number_format($item['harga'], 0, ',', '.') }}</span>
                                <span style="color: #1e293b; font-weight: 600;">Rp
                                    {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach

                    <hr style="border-style: dashed; border-color: #e2e8f0; margin: 0.5rem 0;">

                    <div style="font-size: 0.85rem;">
                        <div class="d-flex justify-content-between mb-1"><span style="font-weight: 700;">TOTAL</span><span
                                style="font-weight: 800; font-size: 1rem;">Rp
                                {{ number_format($receiptData['total'], 0, ',', '.') }}</span></div>
                        <div class="d-flex justify-content-between mb-1"><span style="color: #64748b;">Bayar</span><span>Rp
                                {{ number_format($receiptData['bayar'], 0, ',', '.') }}</span></div>
                        <div class="d-flex justify-content-between"><span style="color: #64748b;">Kembalian</span><span
                                style="font-weight: 700; color: #10b981;">Rp
                                {{ number_format($receiptData['kembalian'], 0, ',', '.') }}</span></div>
                    </div>

                    <hr style="border-style: dashed; border-color: #e2e8f0; margin: 0.75rem 0;">
                    <div class="text-center" style="font-size: 0.7rem; color: #94a3b8;">
                        Terima kasih atas kunjungan Anda!<br>— Ayu Bakery —
                    </div>
                </div>

                {{-- Actions --}}
                <div style="padding: 0.75rem 1.5rem 1.25rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('kasir.pos.cetak', $receiptData['nomor_struk']) }}" target="_blank"
                        style="flex: 1; text-align: center; text-decoration: none; padding: 0.6rem; background: #6366f1; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                        <i class="fas fa-print me-1"></i> Cetak
                    </a>
                    <button wire:click="closeReceipt"
                        style="flex: 1; padding: 0.6rem; background: #f1f5f9; color: #1e293b; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                        Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>