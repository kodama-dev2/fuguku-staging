# ServBay Setup Guide untuk Fuguku WordPress

## ✅ Smart Config Sudah Aktif

wp-config.php sekarang otomatis mendeteksi environment:
- **Local (ServBay)**: Pakai `wp-config-local.php` (git-ignored)
- **Staging/Production**: Pakai credentials Hostinger

**Tidak perlu ganti config saat push ke staging!** 🎉

---

## 🚀 Langkah Setup di ServBay

### 1. Buka ServBay App
Klik icon ServBay di menu bar (atas kanan Mac)

### 2. Setup MySQL Database

**a. Ke tab "MySQL"** di ServBay
- ServBay sudah jalankan MySQL otomatis saat app aktif
- Cek "Connection Details" untuk:
  - Host (biasanya: `127.0.0.1`)
  - Port (biasanya: `3306` atau `13306`)
  - User (default: `root`)
  - Password (bisa kosong atau ada)

**b. Buat Database Baru**
- Klik "Databases" atau buka terminal dan jalankan:
  ```bash
  mysql -u root -h 127.0.0.1 -P 3306 -e "CREATE DATABASE fuguku_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  ```
- Atau pakai GUI: ServBay → MySQL → "Create Database"
- Nama database: `fuguku_local` (sesuai wp-config-local.php)

**c. Import Database**
- Jika punya backup SQL:
  ```bash
  mysql -u root -h 127.0.0.1 -P 3306 fuguku_local < fuguku_staging_20250828_165940.sql.gz
  ```
- Atau gunakan ServBay Database Manager (GUI built-in)

### 3. Update wp-config-local.php

Edit file `wp-config-local.php` sesuai ServBay settings Anda:

```php
define('DB_NAME', 'fuguku_local');        // Nama database yang dibuat
define('DB_USER', 'root');                // User MySQL ServBay (cek di ServBay → MySQL)
define('DB_PASSWORD', '');                // Password (kosong atau sesuai ServBay)
define('DB_HOST', '127.0.0.1:3306');      // Host:Port dari ServBay
```

**Cara cek MySQL port di ServBay:**
- ServBay → MySQL tab → Connection Details
- Atau jalankan: `lsof -i :3306` untuk cek port 3306

### 4. Setup Host/Site di ServBay

**a. Ke tab "Hosts"** di ServBay

**b. Add New Host**
- Host Name: `fuguku.test` (atau nama custom Anda)
- Document Root: `/Users/gemadipada/cursor-repos/wp/revampstaging2025`
- PHP Version: Pilih PHP 8.1 atau 8.2 (recommended)
- Enable: Centang untuk aktifkan

**c. ServBay akan otomatis:**
- Setup Nginx config
- Setup PHP-FPM
- Add entry ke `/etc/hosts`
- Generate SSL certificate (HTTPS otomatis)

### 5. Update Database URLs

Jalankan MySQL query untuk update site URLs:

```bash
mysql -u root -h 127.0.0.1 -P 3306 fuguku_local -e "UPDATE wp_options SET option_value = 'http://fuguku.test' WHERE option_name IN ('siteurl', 'home');"
```

Atau jika pakai HTTPS:
```bash
mysql -u root -h 127.0.0.1 -P 3306 fuguku_local -e "UPDATE wp_options SET option_value = 'https://fuguku.test' WHERE option_name IN ('siteurl', 'home');"
```

### 6. Akses Website

Buka browser dan akses:
- **HTTP**: http://fuguku.test
- **HTTPS**: https://fuguku.test (otomatis dengan self-signed cert)

---

## 📋 Checklist Setup

- [ ] ServBay app installed dan running
- [ ] MySQL database `fuguku_local` dibuat
- [ ] Database di-import (opsional jika ada backup)
- [ ] `wp-config-local.php` sesuai dengan ServBay MySQL settings
- [ ] Host `fuguku.test` ditambahkan di ServBay
- [ ] Database URLs diupdate ke `fuguku.test`
- [ ] Website bisa diakses di browser

---

## 🔧 Troubleshooting

### Error: "Error establishing a database connection"
- Cek ServBay MySQL service running (ServBay → MySQL → hijau/running)
- Verify credentials di `wp-config-local.php`
- Test koneksi: `mysql -u root -h 127.0.0.1 -P 3306`

### Error: "Too many redirects"
- Update database URLs dengan command di step 5
- Clear browser cache/cookies
- Atau edit `wp_options` table manual

### Site lambat
- ServBay biasanya cepat, cek:
  - PHP version yang dipilih (8.1+ recommended)
  - Memory limit di ServBay → PHP Settings
  - OPcache enabled (ServBay enable by default)

### Cannot access fuguku.test
- Cek ServBay Hosts tab, pastikan host enabled (hijau)
- Restart ServBay app
- Flush DNS: `sudo dscacheutil -flushcache`

---

## 🎯 Keuntungan Setup Ini

✅ **No config changes saat push** - wp-config.php smart detect environment  
✅ **ServBay all-in-one** - Nginx, PHP, MySQL dalam 1 app  
✅ **Fast performance** - Production-grade server stack  
✅ **Easy management** - GUI untuk semua services  
✅ **Auto HTTPS** - Self-signed cert otomatis  
✅ **Multiple PHP versions** - Switch PHP version per site  

---

## 🚀 Deploy ke Staging

Saat siap push ke staging:

```bash
git add .
git commit -m "Your changes"
git push origin masterstaging
```

**wp-config-local.php tidak akan ke-commit** (sudah di .gitignore)  
Staging otomatis pakai Hostinger credentials dari wp-config.php

Tidak perlu ubah apapun! 🎉

