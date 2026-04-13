#!/usr/bin/env bash
# Deploy staging: samakan disk dengan GitHub (mirror branch).
# Pakai ini sebagai "Custom deploy command" di Hostinger Git ATAU jalankan manual dari SSH.
#
# Usage (dari dalam folder repo di server):
#   ./deploy-staging.sh
#
# Atau:
#   DEPLOY_BRANCH=masterstaging /path/to/deploy-staging.sh
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

BRANCH="${DEPLOY_BRANCH:-masterstaging}"
REMOTE="${DEPLOY_REMOTE:-origin}"

echo "Deploy: fetch ${REMOTE} && reset --hard ${REMOTE}/${BRANCH}"
git fetch "${REMOTE}"
git reset --hard "${REMOTE}/${BRANCH}"
echo "HEAD: $(git log -1 --oneline)"
git status -sb
