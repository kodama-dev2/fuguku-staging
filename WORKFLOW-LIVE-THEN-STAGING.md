# Fuguku — kerja live dulu, lalu update git/staging

Keputusan terkunci 31 Agustus 2026. Jangan dibalik ke “push staging lalu live ikut.”

## Dua tempat, dua peran

| Tempat | URL | Folder server | Cara kerja |
|--------|-----|---------------|------------|
| **Live** | https://fuguku.com/ | `~/domains/fuguku.com/public_html` | Edit di Cursor **Remote-SSH**. Save = langsung live. |
| **Staging** | https://revampstaging2025.fuguku.com/ | `~/domains/fuguku.com/public_html/revampstaging2025` | Hanya lewat Git Hostinger, branch `masterstaging`. |

Repo Git: https://github.com/kodama-dev2/fuguku-staging.git  
Branch: `masterstaging`  
Panel Hostinger Git sudah terpasang ke path `revampstaging2025` (dan `test-git`). **Jangan** buat baris Git baru ke `public_html` / Direktori kosong.

## SSH (Cursor Remote)

- Host: `145.223.110.206`
- Port: `65002`
- User: `u719516980`
- Key di hPanel: `cursor` (27 Apr 2026)

Di Cursor: Connect via SSH → buka **`public_html`** (live).  
Window folder lokal `revampstaging2025` di laptop = repo Git staging, **bukan** file live.

## Urutan wajib

1. Kerja dan tes sampai beres di **live** (SSH ke `public_html`, atau file yang sama sudah di-copy ke repo lokal).
2. User cek live. Kalau user bilang **sudah benar** / **oke sudah benar** / setara: agent **langsung commit + push** `masterstaging` tanpa ditanya lagi.
3. Push → Hostinger deploy path `revampstaging2025` → staging ikut.

Bukan: commit di laptop lalu harap live berubah.  
Bukan: satu deploy Hostinger ke `revampstaging2025` mengisi live.

### Trigger “sudah benar” (wajib untuk agent)

Kalau user mengonfirmasi hasil live sudah benar:

1. Commit **hanya file kerja** (lihat daftar boleh/dilarang di bawah). Jangan `git add -A` (vendor Elementor/Woo lokal sering kotor).
2. `git push origin masterstaging`.
3. Laporkan hash commit. Jangan klaim staging sudah tampil tanpa bukti deploy Hostinger.

Sinonim yang sama artinya: “sudah benar”, “oke sudah benar”, “benar, commit staging”, “commit ke staging”.

## Yang boleh di-commit ke Git

Hanya kode kerja, contoh:

- `wp-content/plugins/fuguku-gifts-post-type/`
- `wp-content/themes/claue-child/`
- `wp-content/mu-plugins/` (yang kita tulis)
- skrip/docs deploy yang relevan

## Yang dilarang di-commit / dilarang di-dump ke repo

- Seluruh isi `public_html` live
- `wp-config.php`, `.htaccess`
- `wp-content/uploads/`
- database / dump SQL
- cache, log
- core WP / plugin vendor utuh dari live kalau tidak memang sedang diseragamkan dengan sadar

Alasan: deploy staging = `reset` ke Git. Dump live penuh menimpa staging dengan toko live (config, ID Elementor, versi plugin).

## ID Elementor / konten

Live dan staging **beda database**. ID popup/template di mu-plugin staging (contoh 10504, 11104) **tidak otomatis sama** di live. Saat porting kode, cek ID di masing-masing situs.

## Ingat untuk agent

- Window ini lokal = staging repo. Window SSH `public_html` = live.
- Setelah job live selesai, sync file custom ke Git lalu user deploy Hostinger ke `revampstaging2025`.
- Jangan `git reset --hard` di `public_html` dari `masterstaging`.
