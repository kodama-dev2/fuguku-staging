# Fuguku Staging Deployment Guide

## Version: 2.3.0
## Last Updated: 2025-01-27 23:30

### 🚨 DEPLOYMENT ISSUE FIX - Divergent Branches

#### Problem:
```
pull: hint: You have divergent branches and need to specify how to reconcile them.
fatal: Need to specify how to reconcile divergent branches.
Deployment failed
```

#### Solution:

**Option 1: Auto Deployment Script**
1. Use the provided `deploy.sh` script:
```bash
chmod +x deploy.sh
./deploy.sh
```

**Option 2: Manual Git Configuration**
1. Configure git on the staging server:
```bash
git config pull.rebase false
git config pull.ff false
```

**Option 3: Reset to Remote (Recommended for Staging)**
1. Reset staging to match remote exactly:
```bash
git fetch origin masterstaging
git reset --hard origin/masterstaging
```

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
**Current Version**: 2.3.0
**Staging URL**: https://revampstaging2025.fuguku.com/

**Latest Commits:**
- 50671ff5: v2.3.0: Update project rules after reset
- 55d04383: v2.3.0: Cloned layout 4 structure for revamp

### 🚀 Quick Fix Commands

**For Hostinger Deployment Console:**
```bash
# Quick fix for divergent branches
git config pull.rebase false
git fetch origin masterstaging
git reset --hard origin/masterstaging
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