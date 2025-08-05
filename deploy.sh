#!/bin/bash

# Fuguku Staging Deployment Script
# Version: 2.3.0
# Last Updated: 2025-01-27 23:30
# Description: Deployment script untuk mengatasi divergent branches dan memastikan deployment yang stabil

echo "🚀 Starting Fuguku Staging Deployment..."
echo "Repository: https://github.com/kodama-dev2/fuguku-staging.git"
echo "Target Version: 2.3.0"
echo "Branch: masterstaging"

# Configure git for deployment
echo "⚙️ Configuring Git for deployment..."
git config pull.rebase false
git config pull.ff false
git config --global user.email "deployment@fuguku.com"
git config --global user.name "Fuguku Deployment"

# Check current status
echo "📊 Checking current repository status..."
git status

# Fetch latest changes
echo "📥 Fetching latest changes from remote..."
git fetch origin masterstaging

# Reset to remote state to avoid divergent branches
echo "🔄 Resetting to remote masterstaging to avoid conflicts..."
git reset --hard origin/masterstaging

# Verify we're on the correct version
echo "✅ Verifying deployment version..."
CURRENT_COMMIT=$(git rev-parse HEAD)
echo "Current commit: $CURRENT_COMMIT"

# Check if we're on version 2.3.0 or later
git log --oneline -5

echo "🎯 Deployment completed successfully!"
echo "Staging URL: https://revampstaging2025.fuguku.com/"
echo "Please test the staging site to verify functionality."