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

## Deployment dengan Docker

Server hanya memerlukan Git, Docker Engine, Docker Compose, dan OpenSSL. Clone repository, lalu siapkan konfigurasi production:

```bash
cp .env.docker.example .env.docker
chmod 600 .env.docker
mkdir -p docker/secrets
printf 'base64:%s\n' "$(openssl rand -base64 32)" > docker/secrets/app_key
openssl rand -hex 32 > docker/secrets/db_password
openssl rand -hex 32 > docker/secrets/mysql_root_password
chmod 600 docker/secrets/*
```

Sesuaikan `APP_URL`, `APP_PORT`, dan konfigurasi non-rahasia lain dalam `.env.docker`. Jangan commit `.env.docker` atau isi `docker/secrets/`.

Build dan jalankan aplikasi:

```bash
docker compose --env-file .env.docker up -d --build
docker compose --env-file .env.docker ps
```

Migration dijalankan otomatis ketika container aplikasi dimulai. Untuk deployment pertama, masukkan akun demo dan produk awal secara manual:

```bash
docker compose --env-file .env.docker exec app php artisan db:seed --force
```

Deployment berikutnya tidak perlu menjalankan seeder:

```bash
git pull
docker compose --env-file .env.docker up -d --build
```

Melihat log:

```bash
docker compose --env-file .env.docker logs -f app web db
```

Membuat backup MySQL:

```bash
docker compose --env-file .env.docker exec -T db sh -c 'exec mysqldump -u root --password="$(cat /run/secrets/mysql_root_password)" --single-transaction --routines --triggers "$MYSQL_DATABASE"' > backup.sql
```

Menghentikan service tanpa menghapus data:

```bash
docker compose --env-file .env.docker down
```

Jangan menjalankan `docker compose --env-file .env.docker down -v` pada production karena opsi `-v` menghapus volume database.

