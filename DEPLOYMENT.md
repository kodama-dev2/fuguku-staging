# Fuguku Staging — Deployment

**Repo:** https://github.com/kodama-dev2/fuguku-staging.git  
**Branch deploy:** `masterstaging`  
**Staging:** https://revampstaging2025.fuguku.com/  
**Path server (contoh):** `~/domains/fuguku.com/public_html/revampstaging2025`

---

## Aturan dari sekarang (supaya jarang SSH)

1. **Disk staging harus selalu mirror GitHub** — tidak ada commit hanya di server; semua perubahan lewat push lalu deploy.
2. **Jangan mengandalkan `git pull` mentah** di panel jika sering force-push: pakai **`git fetch` + `git reset --hard origin/masterstaging`** (sudah dibungkus skrip di repo ini).
3. **Setelah history server pernah divergen**, deploy panel bisa gagal sampai server di-reset sekali (lihat bawah).

---

## Setup sekali di server (SSH)

Masuk ke **folder repo** staging (bukan `~` saja), lalu:

```bash
git config pull.ff only
git config --get pull.ff
# harus menampilkan: only
```

`pull.ff only` = `git pull` hanya jika bisa fast-forward; kalau tidak bisa, Git **menolak** — lebih aman daripada merge otomatis yang kotor. Untuk staging yang harus identik GitHub, deploy yang benar tetap **`fetch` + `reset --hard`** (bukan sekadar pull).

---

## Deploy rutin: dua opsi

### Opsi A — Hostinger Git: custom command (disarankan)

Di **hPanel → Git**, jika ada field **Deploy script** / **Custom command** / **Post-pull command**, isi **satu baris** (sesuaikan path):

```bash
cd ~/domains/fuguku.com/public_html/revampstaging2025 && ./deploy-staging.sh
```

Skrip **`deploy-staging.sh`** ada di root repo: isinya `git fetch` + `git reset --hard origin/masterstaging`.

Kalau panel **tidak** punya custom command dan hanya menjalankan `git pull`, minta ke Hostinger atau pakai **Opsi B** untuk deploy manual paling aman.

### Opsi B — Tanpa ubah panel: trigger dari developer

1. Push ke `masterstaging` di GitHub.  
2. Satu kali lewat SSH (atau ketika panel gagal):

```bash
cd ~/domains/fuguku.com/public_html/revampstaging2025
./deploy-staging.sh
```

Itu menggantikan mengetik `git fetch` / `reset` manual setiap kali.

---

## Alur developer (hari biasa)

1. Kerja di branch `masterstaging` (atau merge PR ke sana).  
2. `git push origin masterstaging`.  
3. Deploy jalan (panel **atau** `./deploy-staging.sh` di server).  
4. Cek situs staging + cache plugin kalau pakai.

**Tidak perlu SSH** jika panel memanggil `./deploy-staging.sh` atau setara, dan tidak ada divergen baru.

---

## Kenapa dulu sering disuruh SSH?

Hostinger menjalankan **`git pull`**. Setelah **force-push** atau file berubah di server, branch **divergen** → Git modern meminta strategi merge/rebase → **deploy gagal**.  
Solusi sekali: `git fetch` + `git reset --hard origin/masterstaging` (sama isinya dengan `deploy-staging.sh`).

---

## Masalah: divergent branches / deploy failed

**Gejala:** `Need to specify how to reconcile divergent branches` atau pull ditolak.

**Perbaikan (di folder repo di server):**

```bash
git fetch origin
git reset --hard origin/masterstaging
git log -1 --oneline
```

Lalu atur panel supaya ke depannya memakai **`deploy-staging.sh`** (lihat atas), bukan hanya `git pull`.

---

## Cek cepat setelah deploy

```bash
git log -1 --oneline
git status
```

Harus **clean** dan commit sama dengan GitHub `masterstaging`.

---

## Catatan

- Jangan edit file langsung di server lalu expect `git pull` — itu sumber divergen.  
- `wp-config.php` dan secret tidak di-commit; jangan dihapus saat reset.  
- Cache: kosongkan plugin cache / Hostinger setelah deploy besar.
