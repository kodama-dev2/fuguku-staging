# Fuguku Staging Deployment Guide

## Version: 2.4.0
## Last Updated: 2025-01-28 01:00

### 🚨 DEPLOYMENT ISSUE FIX - Divergent Branches (Hostinger Git)

#### Problem:
Hostinger menjalankan `git pull`. Setelah **force-push** atau commit lokal di server, branch **divergen**; Git meminta cara reconcile (`pull.rebase` / merge / ff-only) dan deploy **gagal** dengan pesan seperti:
`Need to specify how to reconcile divergent branches`.

#### Penyebab:
- History di disk server **tidak sama** dengan `origin` (bukan karena repo GitHub rusak).
- `git pull` saja tidak cukup tanpa strategi, dan untuk staging biasanya Anda **ingin disk = GitHub**, bukan merge commit di server.

#### Solusi yang benar untuk staging (satu kali lewat SSH Hostinger):

1. Buka **SSH** Hostinger (hPanel → Advanced → SSH), login.
2. Masuk ke folder **yang sama** dengan repo deploy (contoh: `domains/fuguku.com/public_html/revampstaging2025` — sesuaikan path Anda).
3. Jalankan perintah di file ini bagian **Quick Fix Commands** di bawah: **`git fetch origin` lalu `git reset --hard origin/masterstaging`**.
4. Jalankan lagi **Deploy** dari panel Hostinger (atau push kosong untuk trigger), atau biarkan cron deploy berikutnya.

Setelah server sudah **reset ke `origin/masterstaging`**, pull berikutnya sering sudah bisa **fast-forward**. Jika masih error, set default pull di repo server: `git config pull.ff only` (hanya terima fast-forward) **atau** ubah skrip deploy Hostinger agar tidak memakai `pull` mentah, melainkan `fetch` + `reset --hard` (mirror deploy).

#### Opsi lain (kurang disarankan untuk “mirror” staging):

- `git config pull.rebase false` lalu `git pull` — bisa membuat merge commit di server dan kotor.
- Script `deploy.sh` di repo — hanya jika Hostinger memang menjalankan script itu (banyak panel tidak).

### 🔧 Deployment Configuration

#### Git Configuration (.gitconfig-deployment)
Copy the `.gitconfig-deployment` file to the staging server and apply:
```bash
cp .gitconfig-deployment ~/.gitconfig
```

#### Deployment Process
1. **Pre-deployment**: Configure git settings
2. **Pull Strategy**: Use merge (not rebase) for staging
3. **Conflict Resolution**: Reset to remote state
4. **Verification**: Check commit hash matches expected version

### 📋 Deployment Checklist

#### Before Deployment:
- [ ] Verify local repository is on version 2.3.0
- [ ] Confirm all changes are pushed to remote
- [ ] Check staging server git configuration

#### During Deployment:
- [ ] Configure git pull strategy
- [ ] Fetch latest changes
- [ ] Reset to remote state if conflicts
- [ ] Verify deployment version

#### After Deployment:
- [ ] Test staging site functionality
- [ ] Verify plugin version (2.3.0)
- [ ] Check product revamp layout
- [ ] Confirm no broken features

### 🎯 Current Status

**Repository**: https://github.com/kodama-dev2/fuguku-staging.git
**Branch**: masterstaging
**Current Version**: 2.4.0
**Staging URL**: https://revampstaging2025.fuguku.com/

**Latest Commits:**
- 1189e9b5: KDM-PL: Add KODAMA ADMIN plugin with clean white background, minimal buttons, minimal font, and purple icons
- 50671ff5: v2.3.0: Update project rules after reset
- 55d04383: v2.3.0: Cloned layout 4 structure for revamp

### 🚀 Quick Fix Commands (SSH — salin setelah `cd` ke folder repo di server)

```bash
git fetch origin
git reset --hard origin/masterstaging
git status
```

Opsional agar `git pull` tidak error di Git baru (setelah history sudah rapi):

```bash
git config pull.ff only
```

**Verify Deployment:**
```bash
git log --oneline -3
git status
```

### 📞 Support

If deployment still fails, the issue is likely:
1. Server-side git configuration
2. File permissions
3. Network connectivity to GitHub

**Resolution**: Contact hosting support or apply manual git configuration on the staging server.