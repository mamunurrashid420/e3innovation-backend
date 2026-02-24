# 🚀 CPanel Deployment - Final Fix Commands

## ✅ Problem Fixed!

Your frontend was calling:
- ❌ `/api/public/sliders`
- ❌ `/api/public/settings/appearance`
- ❌ `/api/public/settings/footer`
- ❌ `/api/public/settings/social`

I've added these routes to the backend!

---

## 📋 Deploy to CPanel

### Step 1: Upload Updated Code

Upload the updated `routes/api.php` file to your server.

**Via SSH:**
```bash
cd /home/ebdcom/api.e3bd.com
# Upload your updated routes/api.php file here
```

**Or via CPanel File Manager:**
1. Go to File Manager
2. Navigate to `/home/ebdcom/api.e3bd.com/routes/`
3. Upload the new `api.php` file

---

### Step 2: Clear All Caches

SSH into your server and run:

```bash
cd /home/ebdcom/api.e3bd.com

# Clear all Laravel caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Verify new routes are registered
php artisan route:list --path=api/public
```

**Expected output:**
```
GET|HEAD  api/public/sliders
GET|HEAD  api/public/services
GET|HEAD  api/public/projects
GET|HEAD  api/public/team-members
GET|HEAD  api/public/settings/{group}
GET|HEAD  api/public/settings/appearance
GET|HEAD  api/public/settings/footer
GET|HEAD  api/public/settings/social
GET|HEAD  api/public/stats
```

---

### Step 3: Test All Endpoints

```bash
# Test sliders
curl https://api.e3bd.com/api/public/sliders

# Test appearance
curl https://api.e3bd.com/api/public/settings/appearance

# Test footer
curl https://api.e3bd.com/api/public/settings/footer

# Test social
curl https://api.e3bd.com/api/public/settings/social

# Test services
curl https://api.e3bd.com/api/public/services

# Test projects
curl https://api.e3bd.com/api/public/projects

# Test team members
curl https://api.e3bd.com/api/public/team-members
```

All should return JSON data!

---

### Step 4: Test from Browser

Open these URLs in your browser:

1. **https://api.e3bd.com/api/public/sliders**
2. **https://api.e3bd.com/api/public/settings/appearance**
3. **https://api.e3bd.com/api/public/settings/footer**
4. **https://api.e3bd.com/api/public/settings/social**

All should show JSON data!

---

### Step 5: Test Frontend

1. Open **https://e3bd.com**
2. Open browser console (F12)
3. Check Network tab
4. All API calls should now return **200 OK** instead of 404!

---

## 🎯 All Working Endpoints

### Without /public prefix:
```
✅ GET /api/sliders
✅ GET /api/appearance
✅ GET /api/footer
✅ GET /api/social
✅ GET /api/services
✅ GET /api/projects
✅ GET /api/team-members
```

### With /public prefix:
```
✅ GET /api/public/sliders
✅ GET /api/public/settings/appearance
✅ GET /api/public/settings/footer
✅ GET /api/public/settings/social
✅ GET /api/public/services
✅ GET /api/public/projects
✅ GET /api/public/team-members
```

Both work now! Your frontend can use either format!

---

## 📁 Files Modified

- ✅ `routes/api.php` - Added `/public/settings/*` routes

---

## 🔄 Quick Deploy Script

Save this as `deploy.sh` and run it after uploading code:

```bash
#!/bin/bash
cd /home/ebdcom/api.e3bd.com

echo "Clearing caches..."
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Testing routes..."
php artisan route:list --path=api/public

echo "Testing API..."
curl https://api.e3bd.com/api/ping

echo "Done! ✅"
```

Make it executable:
```bash
chmod +x deploy.sh
./deploy.sh
```

---

## ✅ Success Checklist

- [ ] Updated `routes/api.php` uploaded to server
- [ ] All caches cleared (`php artisan route:clear`)
- [ ] Routes verified (`php artisan route:list --path=api/public`)
- [ ] Endpoints tested with curl (all return JSON)
- [ ] Browser test successful (all URLs return JSON)
- [ ] Frontend loads without 404 errors
- [ ] Images display correctly

---

**After deploying, your frontend will work perfectly!** 🎉

