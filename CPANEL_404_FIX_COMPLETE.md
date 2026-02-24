# 🔧 CPanel 404 Fix - Complete Solution

## 🎯 Problem

- ✅ Local e sob kaj kore (localhost:8000)
- ❌ CPanel e 404 error ashe
- Frontend: `e3bd.com`
- Backend API: `api.e3bd.com`

---

## ✅ Solution Steps

### Step 1: Check Document Root (MOST IMPORTANT!)

CPanel e **Document Root** must point to `public` folder!

#### For `api.e3bd.com`:

1. CPanel → **Domains** → Click on `api.e3bd.com`
2. **Document Root** should be:
   ```
   /home/username/api.e3bd.com/public
   ```
   **NOT:**
   ```
   /home/username/api.e3bd.com  ❌ WRONG!
   ```

3. If wrong, change it to `/home/username/api.e3bd.com/public`
4. Click **Save**

---

### Step 2: Check `.htaccess` File

Make sure `public/.htaccess` exists and has correct content:

#### SSH into your server:
```bash
cd /home/username/api.e3bd.com/public
cat .htaccess
```

#### The `.htaccess` should contain:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

If missing or wrong, create/update it with the above content.

---

### Step 3: Clear Laravel Caches

SSH into your server and run:

```bash
cd /home/username/api.e3bd.com

# Clear all caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Check routes are registered
php artisan route:list --path=api
```

You should see:
```
GET|HEAD  api/sliders
GET|HEAD  api/appearance
GET|HEAD  api/footer
GET|HEAD  api/social
GET|HEAD  api/services
GET|HEAD  api/projects
GET|HEAD  api/team-members
```

---

### Step 4: Check File Permissions

```bash
cd /home/username/api.e3bd.com

# Set correct permissions
chmod -R 755 storage bootstrap/cache
chown -R username:username storage bootstrap/cache

# Check if files exist
ls -la public/.htaccess
ls -la public/index.php
```

---

### Step 5: Update `.env` File

Make sure your `.env` file has correct settings:

```bash
cd /home/username/api.e3bd.com
nano .env
```

Update these values:

```env
APP_NAME="E3 Innovation API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.e3bd.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# CORS - Allow frontend domain
SANCTUM_STATEFUL_DOMAINS=e3bd.com,www.e3bd.com
SESSION_DOMAIN=.e3bd.com
```

After editing, save and run:
```bash
php artisan config:clear
```

---

### Step 6: Test API Endpoints

Test directly from SSH:

```bash
# Test ping
curl https://api.e3bd.com/api/ping

# Test sliders
curl https://api.e3bd.com/api/sliders

# Test appearance
curl https://api.e3bd.com/api/appearance
```

All should return JSON data, not 404!

---

### Step 7: Update Frontend API URL

In your frontend (`e3bd.com`), update the API base URL:

**Next.js (.env.production):**
```env
NEXT_PUBLIC_API_URL=https://api.e3bd.com/api
```

**React (.env.production):**
```env
REACT_APP_API_URL=https://api.e3bd.com/api
```

**Vue/Nuxt:**
```env
NUXT_PUBLIC_API_BASE=https://api.e3bd.com/api
```

---

### Step 8: Check CORS Settings

Make sure `config/cors.php` allows your frontend domain:

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['https://e3bd.com', 'https://www.e3bd.com'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

Or allow all origins (for testing):
```php
'allowed_origins' => ['*'],
```

After changing, run:
```bash
php artisan config:clear
```

---

## 🧪 Testing Checklist

### Test from Browser:

1. **Test API directly:**
   ```
   https://api.e3bd.com/api/ping
   https://api.e3bd.com/api/sliders
   https://api.e3bd.com/api/appearance
   ```
   Should return JSON, not 404!

2. **Test from Frontend:**
   Open browser console on `https://e3bd.com` and run:
   ```javascript
   fetch('https://api.e3bd.com/api/sliders')
     .then(res => res.json())
     .then(data => console.log('✅ Success:', data))
     .catch(err => console.error('❌ Error:', err));
   ```

---

## 🔍 Common Issues & Solutions

### Issue 1: Still Getting 404

**Cause:** Document root not pointing to `public` folder

**Solution:**
1. CPanel → Domains → api.e3bd.com
2. Change Document Root to: `/home/username/api.e3bd.com/public`
3. Save and wait 2-3 minutes

### Issue 2: CORS Error

**Cause:** Frontend domain not allowed

**Solution:**
Update `config/cors.php`:
```php
'allowed_origins' => ['https://e3bd.com', 'https://www.e3bd.com'],
```

### Issue 3: 500 Internal Server Error

**Cause:** File permissions or missing .env

**Solution:**
```bash
chmod -R 755 storage bootstrap/cache
cp .env.example .env
php artisan key:generate
```

### Issue 4: Routes Not Found

**Cause:** Route cache issue

**Solution:**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

## 📋 Quick Command Summary

```bash
# SSH into server
ssh username@yourdomain.com

# Go to project directory
cd /home/username/api.e3bd.com

# Clear all caches
php artisan route:clear
php artisan config:clear  
php artisan cache:clear

# Fix permissions
chmod -R 755 storage bootstrap/cache

# Test routes
php artisan route:list --path=api

# Test API
curl https://api.e3bd.com/api/ping
curl https://api.e3bd.com/api/sliders
```

---

## 🎯 Final Checklist

- [ ] Document Root = `/home/username/api.e3bd.com/public`
- [ ] `.htaccess` file exists in `public/` folder
- [ ] All caches cleared
- [ ] File permissions: `755` for storage and bootstrap/cache
- [ ] `.env` file configured correctly
- [ ] CORS allows frontend domain
- [ ] Routes registered (check with `route:list`)
- [ ] API endpoints return JSON (not 404)
- [ ] Frontend API URL updated to `https://api.e3bd.com/api`

---

**After following these steps, your API should work on CPanel!** 🚀

