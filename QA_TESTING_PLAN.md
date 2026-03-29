# QA Analysis & Manual Testing Plan - Ayu Bakery

## A. ANALISIS FITUR

#### 1. Daftar & Klasifikasi Fitur

| Nama Fitur | Deskripsi Fungsi | Kategori | Prioritas | Dependensi |
|---|---|---|---|---|
| **Multi-Guard Login & Auth** | Autentikasi terpisah untuk 5 role berbeda dengan *guard* spesifik. | Core Feature | **High** | Dasar dari semua fitur |
| **User Management** | CRUD dan manajemen semua *role* pengguna dalam satu *tabbed interface* terpusat. | Admin / Core | Medium | Auth System |
| **Produk Management** | Master data produk (nama, deskripsi, *upload* gambar, *icon*, harga dasar). | Core Feature | **High** | Tidak ada |
| **Persediaan Management** | Mengatur stok produk dengan logika **Sinkronisasi Dual-Unit** (Unit Besar & Kecil), tingkat konversi, batch expired. | Core Feature | **High** | Produk Management |
| **POS (Point of Sale)** | Sistem kasir. Checkout pesanan dengan pemotongan stok otomatis unit kecil berdasar **logika FEFO**. Cetak struk. | Core Feature | **High** | Persediaan |
| **Katalog & Keranjang (Reseller)** | Tampilan frontend untuk *Reseller*. Pemilihan item dengan input *opsional* unit besar/kecil tersinkronisasi. | Core Feature | **High** | Produk, Persediaan |
| **Pesanan Management** | Validasi status pesanan masuk dari *Reseller*, perubahan status order. | Core Feature | **High** | Katalog, Keranjang |
| **Pengiriman & Scan QR** | Modul *Kurir* melihat daftar pesanan yang harus dikirim dan meng-update status menggunakan *QR Code Scan*. | Supporting | Medium | Pesanan Management |
| **Laporan & Riwayat** | Modul (*Pemilik Toko*, *Kasir*, dll) untuk Riwayat Penjualan, Laporan Persediaan, Laporan Pesanan. | Reporting | Low | Semua data transaksi |

#### 2. Identifikasi Potensi Risiko & Area Rawan Error (*Bug-prone area*)
*   **Perhitungan Sinkronisasi Dual-Unit:** Area *sangat rawan* saat mengubah tipe satuan di input (contoh: 1 unit besar = 10 unit kecil). Hal ini rawan memunculkan nilai *decimal* (pecahan) yang tidak terduga atau tipe data salah (terbaca sebagai `string`).
*   **Race Conditions pada Checkout Kasir/Keranjang:** Jika dua *Reseller* / *Kasir* melakukan *checkout* bersamaan di saat sisa stok produk tinggal 1, memicu saldo stok negatif.
*   **Logika FEFO (First Expired First Out):** Kesalahan penarikan ID Batch atau *sorting timestamp* dapat membuat sistem memotong stok yang masa kedaluwarsanya masih jauh, bukannya yang akan kedaluwarsa lebih dulu.
*   **Kebocoran Sesi (Session Leak) Multi-Guard:** Risiko di mana kredensial pengguna *Reseller* digunakan untuk memaksakan akses (URL Bypass) ke rute `/admintoko/persediaan`.

---

## B. TEST SCENARIO (SKENARIO PENGUJIAN)

Di bawah ini adalah turunan tes mendalam untuk fitur-fitur yang paling kritikal.

#### 1. POS Checkout & FEFO Logic (Kasir)
*   **Tujuan Pengujian:** Memastikan sistem POS dapat merespons pemilihan unit yang benar dan otomatis memotong stok dari batch FEFO tanpa error.
*   **Pre-condition:** Kasir berhasil *login*. Terdapat *Produk A* dengan 2 *batch* sisa persediaan: Batch 1 (Expire: Besok, Stok 5) & Batch 2 (Expire: Lusa, Stok 10).
*   **Test Steps:**
    1. Masuk ke halaman `/kasir/pos`.
    2. Tambahkan *Produk A* ke dalam *cart* sebanyak 7 Unit Kecil.
    3. Pilih metode pembayaran, lalu klik "Proses Pembayaran".
*   **Expected Result:** Transaksi berhasil. Stok Produk A berkurang 7. Rincian potongan: Batch 1 stok habis (0), Batch 2 stok sisa 8. Struk tercetak.
*   **Negative Test Case:** Input stok 20 (melebihi jumlah 15). Sistem harus menolak transaksi dan memunculkan *alert* "Stok tidak mencukupi".
*   **Edge Cases:** Memasukkan jumlah negatif (-5) di input POS, menggunakan manipulasi element HTML (`inspect element`). Sistem harus melakukan validasi *backend* `min:1`.

#### 2. Dual-Unit Input Synchronization (Persediaan / Katalog)
*   **Tujuan Pengujian:** Memastikan bahwa input Unit Besar dan Unit Kecil benar-benar tersinkronisasi secara *real-time* berbasis nilai *tingkat_konversi*.
*   **Pre-condition:** Produk B memiliki tingkat konversi 1 Lusin (Unit Besar) = 12 Pcs (Unit Kecil).
*   **Test Steps:**
    1. Buka formulir input persediaan / katalog.
    2. Pada baris *Produk B*, ketik angka "2" di kolom *Unit Besar*.
*   **Expected Result:** Kolom *Unit Kecil* secara reaktif dan otomatis terisi angka "24" oleh Livewire tanpa *page reload*.
*   **Negative Test Case:** Masukkan huruf "abc" di kolom Unit Besar. Harus di-*block* oleh validasi `numeric`.
*   **Edge Cases:** Memasukkan desimal seperti "1.5" Unit Besar (harus terkonversi 18 Unit Kecil) -> periksa apakah aplikasi mengizinkan pecahan pada *Unit Besar*. Diharapkan tidak error.

#### 3. Keamanan Rute Multi-Guard (Authorization)
*   **Tujuan Pengujian:** Menjamin *role* yang tidak berizin tidak dapat membuka modul sensitif.
*   **Pre-condition:** Memiliki 1 akun aktif untuk *Reseller*.
*   **Test Steps:**
    1. Login sebagai *Reseller*.
    2. Paksa ubah URL di address bar menuju `http://domain/admintoko/persediaan`.
*   **Expected Result:** Sistem mengembalikan tampilan *403 Forbidden* atau di-*redirect* otomatis ke *Home/Dashboard Reseller*.
*   **Negative Test Case / Edge Cases:** Melakukan `logout` dengan membuka beberapa tab bersamaan. Pastikan token CSRF tetap terlindungi.

---

## C. TEST CASE TABLE

Tabel referensi kerja untuk QA Manual saat eksekusi.

| ID Test Case | Feature Name | Test Scenario | Steps | Expected Result | Actual Result | Status | Notes |
|:---|:---|:---|:---|:---|:---|:---|:---|
| TC-01 | Auth Multi-Guard | Cek Login Admin Toko sukses | 1. Buka /login<br>2. Input kredensial Admin<br>3. Klik Login | Teralihkan ke route `admintoko.users` atau dashboard Admin, nama & icon Admin tampil di profil | *(Kosong)* | *(Kosong)* | Pastikan menu sidebar role lain tersembunyi |
| TC-02 | Auth Multi-Guard | Bypass session route | 1. Login Reseller<br>2. Buka URL `/kasir/pos` manual | Diarahkan ke Error 403 / Redirect back karena gagal lewat middleware | *(Kosong)* | *(Kosong)* | |
| TC-03 | Persediaan Sync | Validasi Sinkronisasi Unit | 1. Buka manajemen persediaan<br>2. Edit Unit Besar=3 (konversi=10) | Livewire otomatis edit Unit Kecil=30 | *(Kosong)* | *(Kosong)* | Cek *Network Response* Livewire cukup cepat/tidak lag |
| TC-04 | Kasir POS | Pemotongan FEFO Standar | 1. Add produk ke POS<br>2. Bayar | Mengurangi stok urut tanggal Expired terdekat terlebih dahulu | *(Kosong)* | *(Kosong)* | Cek perubahan manual via Database/Admin log |
| TC-05 | Kasir POS | Limit melebihi stok | 1. Add produk jumlah 999<br>2. Checkout | Muncul error flash message stok kurang | *(Kosong)* | *(Kosong)* | |
| TC-06 | Pengiriman Kurir | Akses Scan QR | 1. Kurir Login<br>2. Buka '/kurir/scan'<br>3. Pindai Resi | Status Pesanan update ke "Delivered" atau sejenisnya | *(Kosong)* | *(Kosong)* | Wajib tes menggunakan *real mobile device* |

---

## D. PRIORITAS TESTING

Urutan pelaksanaan pengujian agar menemukan *blocker bug* lebih awal (Shift-Left Testing):
1. **[Kritikal] Keamanan & Auth (Multi-Guard):** Jika ini rilis dengan *bug*, user sembarangan bisa masuk ke sistem Admin dan merusak seluruh data.
2. **[Kritikal] Sinkronisasi Unit & Perhitungan Stok:** Karena ini *core calculation* Livewire. Jika konversi berakar *bug*, semua data persediaan di _database_ akan aneh bentuk/valuenya.
3. **[Tinggi] Transaksi Kasir POS (Termasuk FEFO):** Fitur utama pencari uang. Uji keranjang, koneksi database pesanan, perhitungan diskon (jika ada), print PDF receipt, dan logika FEFO persediaan.
4. **[Tinggi] Katalog & Cart Workflow (Reseller):** Transaksi pesanan yang masuk dari sistem self-service oleh Reseller. Uji *end-to-end* sampai pesanan masuk di admin.
5. **[Medium] Master Data (CRUD User, Role, Produk):** Pengujian standar form, gambar, validasi teks.
6. **[Medium] Modul Kurir:** Uji fitur frontend scan QR kamera.
7. **[Rendah] Modul Laporan (Report):** Hanya menampilkan agregat dari data statis, diuji terakhir.

---

## E. CHECKLIST MANUAL TESTING (PRACTICAL GUIDE)

Panduan yang bisa diprint / dicontek *QA tester* (UX/UI & Generic Scenarios):

- [ ] **UI/UX Consistency**: Cek struktur komponen tombol (harus *[icon - label]* sesuai standarisasi).
- [ ] **Validasi Input Form**: Pastikan semua form numerik tidak membiarkan teks masuk (contoh: harga `-50000`). Submit form dalam kondisi kosong memunculkan alert wajib (required).
- [ ] **Navigasi & Sidebar**: Sidebar navigasi diubah tergantung `auth()->guard()`. Pastikan Reseller tidak bisa melihat tombol navigasi menu "Persediaan".
- [ ] **Responsivitas**: Tes halaman POS Kasir di layar komputer/desktop, dan tes modul `kurir/scan` & Laporan (Report) pada resolusi layar *Mobile* Phone (Responsive).
- [ ] **Error Handling / Flash Message**: Ketika terjadi _network error_ atau Gagal Eksekusi/Simpan, ada *toast notification*/"Pesan Merah" alih-alih menampilkan _Laravel Exception Error Page (Whoops!)_.
- [ ] **Performance (Livewire)**: Karena input dual-unit reaktif dengan Livewire (mengirim Ajax ke *server* per-ketikan), pastikan menggunakan perintah `.live.debounce` pada kode `.blade` agar server tidak kelebihan muatan (DDoS) karena request yang terlalu cepat saat _typing_.

---

## F. SARAN PERBAIKAN & PENINGKATAN KUALITAS

1. **Automation Testing Mandatory (Unit Test):**
   *Logika model untuk FEFO dan Konversi Unit WAJIB memiliki *Unit Testing* (`php artisan test`). Menghitung secara logika FEFO sangat kompleks diubah seiring waktu. Jika sewaktu-waktu ada Refactor database, Anda tidak perlu cemas karena automated test langsung memperingatkan bug perhitungan.*

2. **Perbaikan Keamanan Konkurensi (Race-Condition Handling):**
   Sangat disarankan memakai fitur Laravel Database Locks (`lockForUpdate()`) selama _backend logic checkout_ (Kasir POS / Keranjang Reseller). Ini mengatasi momen 2 orang klik "Checkout" barang rebutan di milidetik yang persis sama.

3. **Rate Limiting Login:**
   Sangat disarankan mengimplementasikan *login rate limiting* (Throttle) agar akun-akun vital (Admin Toko / Pemilik) tidak dibobol melalui serangan *Brute Force*.

4. **UX pada POS (Scanning):**
   Jika toko roti melayani penjualan cepat (offline/Kasir), dukungan interaksi tombol *Keyboard Shortcut* di POS (contoh `F2` untuk Bayar, `F4` untuk Scan Barcode Produk) akan meningkatkan kecepatan operasional kasir sebesar 40%.
