# WP Engine Deployment Guide

This guide will help you deploy your analytics dashboard to WP Engine staging environment.

## 🚀 Quick Setup

### 1. Get Your WP Engine Git Push URL

1. Log into your WP Engine User Portal
2. Go to your site's dashboard
3. Click on "Git push" in the left sidebar
4. Copy the Git push URL (format: `git@git.wpengine.com:production/your-site-name.git`)

### 2. Add WP Engine Remote

```bash
# Add WP Engine as a remote repository
git remote add wpengine git@git.wpengine.com:production/your-site-name.git

# For staging environment (if you have staging)
git remote add wpengine-staging git@git.wpengine.com:staging/your-site-name.git
```

### 3. Push to Staging

```bash
# Push to staging environment
git push wpengine-staging master

# Or push to production (be careful!)
git push wpengine master
```

## 📋 Step-by-Step Instructions

### Step 1: Get Your Git Push URL

1. **Login to WP Engine User Portal**
   - Go to: https://my.wpengine.com/
   - Login with your credentials

2. **Navigate to Your Site**
   - Find your site in the dashboard
   - Click on the site name

3. **Access Git Push**
   - In the left sidebar, click "Git push"
   - You'll see your Git push URL

### Step 2: Configure Git Remote

Replace `your-site-name` with your actual WP Engine site name:

```bash
# Add the remote (run this in your theme directory)
git remote add wpengine git@git.wpengine.com:production/your-site-name.git
git remote add wpengine-staging git@git.wpengine.com:staging/your-site-name.git

# Verify remotes
git remote -v
```

### Step 3: Deploy to Staging

```bash
# Push to staging first (recommended)
git push wpengine-staging master

# Check deployment status
# Go to your WP Engine dashboard and check the staging environment
```

### Step 4: Test on Staging

1. **Access Your Staging Site**
   - Go to your staging URL (usually `your-site-name.staging.wpengine.com`)
   - Login to WordPress admin

2. **Test Analytics Dashboard**
   - Navigate to Analytics in the admin menu
   - Verify all features are working
   - Check that sample data displays correctly

### Step 5: Deploy to Production

```bash
# Only after testing on staging
git push wpengine master
```

## 🔧 Configuration for Production

### 1. Update Google Analytics Configuration

Once deployed to production, update your Google Analytics credentials:

1. **Edit the config file:**
   ```bash
   # Edit the analytics config
   nano js/analytics-config.js
   ```

2. **Update with real credentials:**
   ```javascript
   window.GA_CONFIG = {
       apiKey: 'YOUR_REAL_API_KEY',
       clientId: 'YOUR_REAL_CLIENT_ID',
       viewId: 'YOUR_REAL_VIEW_ID',
       // ... rest of config
   };
   ```

3. **Add your production domain to Google Cloud Console:**
   - Go to Google Cloud Console
   - Add your production domain to authorized origins
   - Example: `https://yourdomain.com`

### 2. Commit and Push Changes

```bash
# Add the updated config
git add js/analytics-config.js
git commit -m "Update Google Analytics config for production"

# Push to staging first
git push wpengine-staging master

# Test on staging, then push to production
git push wpengine master
```

## 🛠️ Troubleshooting

### Common Issues

1. **Permission Denied**
   ```bash
   # Make sure you have SSH access to WP Engine
   ssh git@git.wpengine.com
   ```

2. **Push Rejected**
   ```bash
   # Force push (use with caution)
   git push wpengine-staging master --force
   ```

3. **Files Not Updating**
   - Check that you're in the correct theme directory
   - Verify files are committed
   - Check WP Engine deployment logs

### Useful Commands

```bash
# Check current status
git status

# Check remote repositories
git remote -v

# View commit history
git log --oneline

# Check which files are tracked
git ls-files

# Push to specific branch
git push wpengine-staging master:master
```

## 📊 Post-Deployment Checklist

- [ ] Analytics dashboard loads correctly
- [ ] All tabs and features work
- [ ] Sample data displays properly
- [ ] Charts render correctly
- [ ] Real-time indicators work
- [ ] Mobile responsiveness tested
- [ ] Google Analytics connection tested (if configured)

## 🔄 Continuous Deployment

For ongoing development:

```bash
# Make changes to your theme
# Then commit and push
git add .
git commit -m "Description of changes"
git push wpengine-staging master

# Test on staging, then push to production
git push wpengine master
```

## 📞 Support

If you encounter issues:

1. Check WP Engine deployment logs
2. Verify Git remote URLs
3. Ensure SSH keys are configured
4. Contact WP Engine support if needed

## 🎯 Best Practices

1. **Always test on staging first**
2. **Use descriptive commit messages**
3. **Keep your local repository clean**
4. **Backup before major changes**
5. **Monitor deployment logs**

---

**Note:** Replace `your-site-name` with your actual WP Engine site name throughout this guide. 