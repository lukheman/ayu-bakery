<div>
    {{-- Hero Section --}}
    <section
        style="min-height: 100vh; display: flex; align-items: center; position: relative; overflow: hidden; padding-top: 80px; background: linear-gradient(135deg, #fffbeb 0%, #fff7ed 50%, #fff1f2 100%);">
        {{-- Decorative shapes --}}
        <div
            style="position: absolute; top: -120px; right: -120px; width: 500px; height: 500px; border-radius: 50%; background: linear-gradient(135deg, rgba(225,29,72,0.06), rgba(251,113,133,0.08)); animation: heroFloat 20s ease-in-out infinite;">
        </div>
        <div
            style="position: absolute; bottom: -80px; left: -80px; width: 400px; height: 400px; border-radius: 50%; background: linear-gradient(135deg, rgba(245,158,11,0.06), rgba(251,191,36,0.08)); animation: heroFloat 20s ease-in-out infinite; animation-delay: -7s;">
        </div>
        <div
            style="position: absolute; top: 40%; left: 55%; width: 250px; height: 250px; border-radius: 50%; background: linear-gradient(135deg, rgba(124,58,237,0.04), rgba(139,92,246,0.06)); animation: heroFloat 20s ease-in-out infinite; animation-delay: -14s;">
        </div>

        <div class="container" style="position: relative; z-index: 1;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
                <div>
                    <div
                        style="display: inline-flex; align-items: center; gap: 8px; background: rgba(225,29,72,0.08); color: #e11d48; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600; margin-bottom: 1.5rem; border: 1px solid rgba(225,29,72,0.12);">
                        <i class="fas fa-star"></i>
                        Roti & Kue Berkualitas Premium
                    </div>
                    <h1
                        style="font-size: 3.25rem; font-weight: 800; line-height: 1.15; margin-bottom: 1.5rem; color: #1e293b;">
                        Nikmati Kelezatan
                        <span
                            style="background: linear-gradient(135deg, #e11d48, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Ayu
                            Bakery</span>
                        Setiap Hari
                    </h1>
                    <p
                        style="font-size: 1.15rem; color: #64748b; margin-bottom: 2rem; line-height: 1.8; max-width: 520px;">
                        Kami menyajikan aneka roti dan kue dengan bahan-bahan berkualitas tinggi, diproduksi fresh
                        setiap hari untuk memastikan kelezatan di setiap gigitan.
                    </p>
                    <div style="display: flex; gap: 1rem; margin-bottom: 3rem; flex-wrap: wrap;">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-bag"></i>
                            Pesan Sekarang
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline btn-lg">
                            <i class="fas fa-handshake"></i>
                            Daftar Reseller
                        </a>
                    </div>
                    <div style="display: flex; align-items: center; gap: 2.5rem;">
                        <div style="text-align: center;">
                            <span
                                style="display: block; font-size: 1.75rem; font-weight: 800; color: #e11d48;">50+</span>
                            <span style="font-size: 0.85rem; color: #94a3b8;">Varian Produk</span>
                        </div>
                        <div style="width: 1px; height: 45px; background: #e2e8f0;"></div>
                        <div style="text-align: center;">
                            <span
                                style="display: block; font-size: 1.75rem; font-weight: 800; color: #f59e0b;">Fresh</span>
                            <span style="font-size: 0.85rem; color: #94a3b8;">Setiap Hari</span>
                        </div>
                        <div style="width: 1px; height: 45px; background: #e2e8f0;"></div>
                        <div style="text-align: center;">
                            <span
                                style="display: block; font-size: 1.75rem; font-weight: 800; color: #10b981;">100%</span>
                            <span style="font-size: 0.85rem; color: #94a3b8;">Halal</span>
                        </div>
                    </div>
                </div>

                {{-- Hero visual: bakery illustration using CSS --}}
                <div style="display: flex; justify-content: center;">
                    <div
                        style="width: 420px; height: 420px; background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-radius: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 25px 60px rgba(225,29,72,0.1); border: 1px solid rgba(225,29,72,0.08); position: relative; overflow: hidden;">
                        <div
                            style="position: absolute; top: 20px; right: 20px; width: 80px; height: 80px; border-radius: 50%; background: rgba(245,158,11,0.1);">
                        </div>
                        <div
                            style="position: absolute; bottom: 30px; left: 20px; width: 60px; height: 60px; border-radius: 50%; background: rgba(225,29,72,0.08);">
                        </div>
                        <div style="text-align: center;">
                            <i class="fas fa-birthday-cake"
                                style="font-size: 6rem; color: #e11d48; margin-bottom: 1rem; display: block; filter: drop-shadow(0 4px 12px rgba(225,29,72,0.3));"></i>
                            <div style="font-size: 2rem; font-weight: 800; color: #e11d48;">Ayu Bakery</div>
                            <div style="font-size: 0.95rem; color: #64748b; margin-top: 0.5rem;">Roti & Kue Premium
                            </div>
                            <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1.5rem;">
                                <div
                                    style="width: 50px; height: 50px; background: #fecdd3; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-bread-slice" style="color: #e11d48;"></i>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-cookie" style="color: #f59e0b;"></i>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: #d1fae5; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-ice-cream" style="color: #10b981;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Tentang Kami --}}
    <section class="section" id="tentang" style="background: white;">
        <div class="container">
            <div style="text-align: center; max-width: 700px; margin: 0 auto 3.5rem;">
                <div
                    style="display: inline-block; background: rgba(225,29,72,0.08); color: #e11d48; padding: 0.5rem 1.25rem; border-radius: 50px; font-size: 0.82rem; font-weight: 600; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 1px;">
                    Tentang Kami</div>
                <h2 style="font-size: 2.25rem; font-weight: 700; margin-bottom: 1rem; color: #1e293b;">Ayu Bakery, Toko
                    Roti Pilihan Anda</h2>
                <p style="font-size: 1.1rem; color: #64748b; line-height: 1.8;">
                    Ayu Bakery hadir untuk memenuhi kebutuhan roti dan kue berkualitas dengan harga terjangkau. Dengan
                    pengalaman dan dedikasi dalam dunia bakery, kami berkomitmen menghadirkan produk yang lezat dan
                    higienis untuk setiap pelanggan.
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;">
                <div
                    style="text-align: center; padding: 2rem; background: #fffbeb; border-radius: 20px; border: 1px solid rgba(245,158,11,0.12); transition: transform 0.3s;">
                    <div
                        style="width: 70px; height: 70px; background: linear-gradient(135deg, #f59e0b, #fbbf24); border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                        <i class="fas fa-store" style="font-size: 1.5rem; color: white;"></i>
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.75rem;">Penjualan Langsung</h3>
                    <p style="color: #64748b; font-size: 0.92rem; line-height: 1.7;">Belanja langsung di toko kami dan
                        dapatkan roti segar yang baru diproduksi setiap hari.</p>
                </div>
                <div
                    style="text-align: center; padding: 2rem; background: #fff1f2; border-radius: 20px; border: 1px solid rgba(225,29,72,0.08); transition: transform 0.3s;">
                    <div
                        style="width: 70px; height: 70px; background: linear-gradient(135deg, #e11d48, #fb7185); border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                        <i class="fas fa-users" style="font-size: 1.5rem; color: white;"></i>
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.75rem;">Jaringan Reseller</h3>
                    <p style="color: #64748b; font-size: 0.92rem; line-height: 1.7;">Bergabung sebagai reseller dan
                        dapatkan harga khusus untuk menjual produk kami.</p>
                </div>
                <div
                    style="text-align: center; padding: 2rem; background: #eff6ff; border-radius: 20px; border: 1px solid rgba(59,130,246,0.08); transition: transform 0.3s;">
                    <div
                        style="width: 70px; height: 70px; background: linear-gradient(135deg, #3b82f6, #60a5fa); border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                        <i class="fas fa-truck" style="font-size: 1.5rem; color: white;"></i>
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.75rem;">Layanan Pengiriman</h3>
                    <p style="color: #64748b; font-size: 0.92rem; line-height: 1.7;">Pesanan diantar langsung oleh kurir
                        kami ke lokasi Anda dengan cepat dan aman.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Keunggulan --}}
    <section class="section" id="keunggulan" style="background: #fafafa;">
        <div class="container">
            <div style="text-align: center; max-width: 700px; margin: 0 auto 3.5rem;">
                <div
                    style="display: inline-block; background: rgba(245,158,11,0.1); color: #f59e0b; padding: 0.5rem 1.25rem; border-radius: 50px; font-size: 0.82rem; font-weight: 600; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 1px;">
                    Keunggulan</div>
                <h2 style="font-size: 2.25rem; font-weight: 700; margin-bottom: 1rem; color: #1e293b;">Mengapa Memilih
                    Ayu Bakery?</h2>
                <p style="font-size: 1.1rem; color: #64748b;">Kami selalu menjaga standar kualitas tinggi di setiap
                    produk yang kami hasilkan.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.75rem;">
                @php
                    $keunggulan = [
                        ['icon' => 'fas fa-wheat-awn', 'title' => 'Bahan Berkualitas', 'desc' => 'Menggunakan bahan-bahan premium pilihan untuk setiap produk', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
                        ['icon' => 'fas fa-clock', 'title' => 'Fresh Setiap Hari', 'desc' => 'Semua produk dibuat dan diproduksi fresh setiap harinya', 'color' => '#10b981', 'bg' => '#ecfdf5'],
                        ['icon' => 'fas fa-truck-fast', 'title' => 'Pengiriman Cepat', 'desc' => 'Layanan antar yang cepat dan dapat diandalkan', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
                        ['icon' => 'fas fa-tags', 'title' => 'Harga Terjangkau', 'desc' => 'Kualitas terbaik dengan harga yang ramah di kantong', 'color' => '#e11d48', 'bg' => '#fff1f2'],
                    ];
                @endphp

                @foreach($keunggulan as $item)
                    <div
                        style="background: white; padding: 2rem 1.5rem; border-radius: 18px; border: 1px solid #e2e8f0; text-align: center; transition: all 0.3s;">
                        <div
                            style="width: 60px; height: 60px; background: {{ $item['bg'] }}; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                            <i class="{{ $item['icon'] }}" style="font-size: 1.4rem; color: {{ $item['color'] }};"></i>
                        </div>
                        <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $item['title'] }}</h3>
                        <p style="color: #64748b; font-size: 0.88rem; line-height: 1.7;">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA: Bergabung --}}
    <section class="section" id="bergabung" style="background: white;">
        <div class="container">
            <div
                style="background: linear-gradient(135deg, #e11d48, #be123c); border-radius: 24px; padding: 4rem; position: relative; overflow: hidden;">
                <div
                    style="position: absolute; top: -60px; right: -60px; width: 300px; height: 300px; border-radius: 50%; background: rgba(255,255,255,0.08);">
                </div>
                <div
                    style="position: absolute; bottom: -40px; left: -40px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.05);">
                </div>

                <div style="position: relative; z-index: 1; text-align: center; max-width: 650px; margin: 0 auto;">
                    <h2 style="font-size: 2.25rem; font-weight: 700; color: white; margin-bottom: 1rem;">Bergabung
                        Bersama Kami!</h2>
                    <p
                        style="font-size: 1.1rem; color: rgba(255,255,255,0.85); margin-bottom: 2.5rem; line-height: 1.7;">
                        Jadilah bagian dari Ayu Bakery. Daftarkan diri Anda sebagai <strong>Reseller</strong> untuk
                        menjual produk kami, atau sebagai <strong>Kurir</strong> untuk membantu pengiriman.
                    </p>
                    <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                        <a href="{{ route('register') }}" class="btn btn-lg"
                            style="background: white; color: #e11d48; font-weight: 700; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                            <i class="fas fa-user-plus"></i>
                            Daftar Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-lg"
                            style="background: transparent; border: 2px solid rgba(255,255,255,0.35); color: white;">
                            <i class="fas fa-sign-in-alt"></i>
                            Sudah Punya Akun? Masuk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-slot:styles>
        <style>
            @keyframes heroFloat {

                0%,
                100% {
                    transform: translateY(0) rotate(0deg);
                }

                33% {
                    transform: translateY(-25px) rotate(6deg);
                }

                66% {
                    transform: translateY(15px) rotate(-4deg);
                }
            }

            @media (max-width: 1024px) {
                section:first-child .container>div {
                    grid-template-columns: 1fr !important;
                    text-align: center;
                }

                section:first-child .container>div>div:last-child {
                    display: none !important;
                }
            }

            @media (max-width: 768px) {
                section:first-child h1 {
                    font-size: 2.25rem !important;
                }

                #tentang>div>div:last-child,
                #keunggulan>div>div:last-child {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
    </x-slot:styles>
</div>