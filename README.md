# Lumière Skin

Prototipe e-commerce skincare berbasis Laravel 13, Blade, Tailwind CSS, dan MySQL.

## Menjalankan aplikasi

Prasyarat: PHP 8.3+, Composer, Node.js 20+, MySQL 8, serta ekstensi PHP `pdo_mysql`. Ekstensi `pdo_sqlite` diperlukan untuk menjalankan test in-memory.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Buat database MySQL `lumiere`, sesuaikan kredensial `DB_*` di `.env`, lalu jalankan:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`.

- Admin: `admin@lumiere.test` / `password`
- Pembeli: `pembeli@lumiere.test` / `password`

