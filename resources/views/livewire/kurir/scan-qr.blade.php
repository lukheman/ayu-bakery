<div>
    <div class="mb-4">
        <h3 class="mb-1" style="font-weight: 700; color: var(--text-primary);">Scan QR Konfirmasi</h3>
        <p class="mb-0" style="color: var(--text-secondary);">Scan QR code atau masukkan kode konfirmasi untuk
            menyelesaikan pengiriman</p>
    </div>

    <div class="modern-card">
        {{-- QR Scanner --}}
        <div class="text-center mb-4">
            <div
                style="width: 180px; height: 180px; margin: 0 auto 1.5rem; background: var(--bg-tertiary); border-radius: 16px; display: flex; align-items: center; justify-content: center; border: 2px dashed var(--border-color);">
                <div>
                    <i class="fas fa-qrcode" style="font-size: 3.5rem; color: var(--primary-color);"></i>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Arahkan kamera ke QR
                    </div>
                </div>
            </div>

            {{-- Camera Scanner --}}
            <div id="qr-reader" style="max-width: 400px; margin: 0 auto 1.5rem; display: none;"></div>
            <button id="start-scan-btn" onclick="startScanner()" class="btn mb-3"
                style="background: var(--primary-color); color: white; border: none; border-radius: 10px; font-weight: 600; padding: 0.7rem 2rem;">
                <i class="fas fa-camera me-2"></i> Buka Kamera
            </button>
            <button id="stop-scan-btn" onclick="stopScanner()" class="btn mb-3"
                style="display: none; background: var(--danger-color); color: white; border: none; border-radius: 10px; font-weight: 600; padding: 0.7rem 2rem;">
                <i class="fas fa-times me-2"></i> Tutup Kamera
            </button>
        </div>

        <div class="text-center mb-3" style="color: var(--text-muted); font-size: 0.85rem;">
            <span style="display: inline-block; width: 40px; border-top: 1px solid var(--border-color);"></span>
            <span class="mx-2">atau masukkan kode manual</span>
            <span style="display: inline-block; width: 40px; border-top: 1px solid var(--border-color);"></span>
        </div>

        {{-- Manual Input --}}
        <div class="d-flex gap-2 justify-content-center" style="max-width: 400px; margin: 0 auto;">
            <input type="text" class="form-control text-center" wire:model="kodeKonfirmasi"
                placeholder="Masukkan kode konfirmasi..."
                style="font-size: 1.1rem; letter-spacing: 3px; font-weight: 700; padding: 0.8rem;"
                wire:keydown.enter="confirmDelivery">
            <button wire:click="confirmDelivery" class="btn"
                style="background: var(--success-color); color: white; border: none; border-radius: 10px; font-weight: 600; padding: 0.8rem 1.5rem; white-space: nowrap;">
                <i class="fas fa-check me-1"></i> Konfirmasi
            </button>
        </div>
    </div>

    {{-- Scan Result --}}
    @if($scanResult)
        <div class="modern-card mt-4"
            style="border-left: 4px solid {{ $resultType === 'success' ? 'var(--success-color)' : 'var(--danger-color)' }};">
            <div class="d-flex align-items-start gap-3">
                <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
                        background: {{ $resultType === 'success' ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)' }};">
                    <i class="fas {{ $resultType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"
                        style="font-size: 1.3rem; color: {{ $resultType === 'success' ? 'var(--success-color)' : 'var(--danger-color)' }};"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1" style="font-weight: 700; color: var(--text-primary);">
                        {{ $resultType === 'success' ? 'Berhasil!' : 'Gagal' }}
                    </h6>
                    <p class="mb-0" style="color: var(--text-secondary);">{{ $resultMessage }}</p>

                    @if($pesananInfo)
                        <div class="mt-3"
                            style="padding: 0.75rem; background: var(--bg-tertiary); border-radius: 10px; font-size: 0.9rem;">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="color: var(--text-secondary);">ID Pesanan</span>
                                <span class="fw-semibold">#{{ str_pad($pesananInfo['id'], 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span style="color: var(--text-secondary);">Reseller</span>
                                <span class="fw-semibold">{{ $pesananInfo['reseller'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span style="color: var(--text-secondary);">Jumlah Item</span>
                                <span>{{ $pesananInfo['items'] }} produk</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span style="color: var(--text-secondary);">Total</span>
                                <span class="fw-bold" style="color: var(--primary-color);">Rp
                                    {{ number_format($pesananInfo['total'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-end mt-3">
                <button wire:click="resetScan" class="btn btn-sm"
                    style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 500; padding: 0.4rem 1rem;">
                    <i class="fas fa-redo me-1"></i> Scan Lagi
                </button>
            </div>
        </div>
    @endif
</div>

{{-- Html5-QRcode CDN --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode = null;

    function startScanner() {
        const readerDiv = document.getElementById('qr-reader');
        readerDiv.style.display = 'block';
        document.getElementById('start-scan-btn').style.display = 'none';
        document.getElementById('stop-scan-btn').style.display = 'inline-block';

        html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                // Set the Livewire property and trigger confirmation
                @this.set('kodeKonfirmasi', decodedText);
                @this.call('confirmDelivery');
                stopScanner();
            },
            (errorMessage) => {
                // Ignore scan errors (no QR found in frame)
            }
        ).catch(err => {
            console.error("Unable to start scanning:", err);
            alert("Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan.");
            stopScanner();
        });
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
            }).catch(err => console.error("Error stopping scanner:", err));
        }

        document.getElementById('qr-reader').style.display = 'none';
        document.getElementById('start-scan-btn').style.display = 'inline-block';
        document.getElementById('stop-scan-btn').style.display = 'none';
    }
</script>