# Spesifikasi Proyek: Prototipe E-Commerce Skincare

## 1. Konteks & Ruang Lingkup

Bangun **prototipe aplikasi e-commerce sederhana** untuk produk **Skincare**.
Aplikasi memiliki **dua peran pengguna**: **Admin** dan **Pembeli**.

---

## 2. Tech Stack

| Komponen | Teknologi |
|---|---|
| Framework backend | **Laravel** (versi stabil terbaru) |
| Frontend / UI | **Tailwind CSS** (via Blade template) |
| Database | **MySQL** |

**Ketentuan teknis:**

- Gunakan fitur bawaan Laravel: migration, seeder, Eloquent ORM, form request validation, dan middleware.
- Autentikasi berbasis peran (*role-based*) menggunakan middleware Laravel, misalnya kolom `role` (`admin` / `pembeli`) pada tabel `users`.
- Proteksi halaman Admin dilakukan di **route/middleware sisi server**, bukan hanya disembunyikan di UI.
- Gunakan **database transaction** dan **row locking** (`lockForUpdate`) saat memproses "Simulasi Bayar Berhasil", agar pengurangan stok aman dan tidak terjadi stok minus.
- Sediakan **seeder** untuk akun Admin, akun Pembeli, serta data dummy produk beserta variannya.
- Sertakan file `.env.example` dan petunjuk setup di `README.md` (instalasi, migrate, seed, menjalankan aplikasi).

---

## 3. Fitur & Aturan Bisnis

### A. Autentikasi & Hak Akses

- Sistem login terpisah atau berbasis peran (*role-based*) untuk membedakan akses Admin dan Pembeli.
- **Keamanan akses:** halaman Admin wajib dilindungi dan **tidak boleh diakses oleh Pembeli**.

### B. Manajemen Produk & Varian (Khusus Admin)

- **Produk Induk**
  - Admin dapat menambah, mengubah, menghapus, dan melihat daftar produk (CRUD).
  - Contoh data dummy: `Cleansing Balm`, `Body Scrub`, `Clay Cleanser`.
- **Varian Produk**
  - Setiap produk induk **wajib** mendukung satu atau lebih varian rasa/aroma.
  - Contoh varian: `Almond`, `Tutti Fruity`.
- **Harga & Stok**
  - Harga dan jumlah stok dikelola **secara independen pada tingkat Varian**, bukan pada produk induk.

### C. Katalog & Keranjang Belanja (Khusus Pembeli)

- Pembeli dapat menjelajahi katalog produk, memilih varian rasa/aroma, dan memasukkannya ke keranjang belanja.
- Pembeli dapat mengelola jumlah (*quantity*) item di keranjang sebelum melanjutkan ke proses berikutnya.

### D. Checkout & Simulasi Pembayaran

- **Pembuatan pesanan**
  - Pembeli melakukan checkout dari keranjang belanja.
  - Pesanan baru dibuat dengan status awal **`Menunggu Pembayaran`**.
- **Simulasi pembayaran** (tanpa payment gateway eksternal)
  - Sediakan dua tombol aksi di halaman detail pesanan:
    1. **"Simulasi Bayar Berhasil"**
    2. **"Simulasi Batalkan Pesanan"**
- **Aturan stok (WAJIB)**
  - Stok pada **varian spesifik** yang dibeli **hanya berkurang** saat tombol **"Simulasi Bayar Berhasil"** ditekan (status pesanan berubah menjadi **`Dibayar`**).
  - Jika pesanan **dibatalkan**, atau **stok varian sudah habis** saat simulasi pembayaran berlangsung, sistem **wajib menolak transaksi** dan menampilkan **pesan error yang informatif**.

---

## 4. Ringkasan Status Pesanan

| Status | Keterangan |
|---|---|
| `Menunggu Pembayaran` | Status awal setelah checkout. Stok **belum** berkurang. |
| `Dibayar` | Setelah "Simulasi Bayar Berhasil". Stok varian **berkurang**. |
| `Dibatalkan` | Setelah "Simulasi Batalkan Pesanan". Stok **tidak** berubah. |

---

## 5. Catatan UI/UX

- Tampilan tidak perlu rumit dan tanpa animasi kompleks.
- Gunakan **Tailwind CSS** sesuai Tech Stack di atas.
- **Prioritas utama:** logika bisnis, integritas data, transaksi, dan kestabilan deployment.

---

## 6. Checklist Penerimaan (Acceptance Criteria)

- [ ] Login berbasis peran berfungsi (Admin vs Pembeli).
- [ ] Pembeli tidak bisa mengakses halaman Admin (diblokir di sisi server/route, bukan hanya disembunyikan di UI).
- [ ] Admin bisa CRUD produk induk.
- [ ] Setiap produk punya minimal satu varian, dengan harga dan stok per varian.
- [ ] Pembeli bisa melihat katalog, memilih varian, dan menambah ke keranjang.
- [ ] Pembeli bisa mengubah quantity di keranjang.
- [ ] Checkout membuat pesanan berstatus `Menunggu Pembayaran`.
- [ ] "Simulasi Bayar Berhasil" mengubah status menjadi `Dibayar` dan mengurangi stok varian terkait.
- [ ] "Simulasi Batalkan Pesanan" mengubah status menjadi `Dibatalkan` tanpa mengubah stok.
- [ ] Jika stok varian tidak cukup saat bayar, transaksi ditolak dengan pesan error yang jelas.
- [ ] Data dummy tersedia (Cleansing Balm, Body Scrub, Clay Cleanser beserta varian).
- [ ] Aplikasi bisa dijalankan dari nol dengan `composer install`, `php artisan migrate --seed`, dan `npm run build`, sesuai petunjuk di README.