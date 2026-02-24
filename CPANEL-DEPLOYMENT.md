# cPanel এ Laravel API ডিপ্লয় – 404 সমাধান

সব API (`/api/public/sliders`, `/api/services`, ইত্যাদি) 404 দিলে সাধারণত **Document Root** ভুল থাকে। cPanel এ সেটিং একবার ঠিক করলেই হবে।

---

## ১. Document Root অবশ্যই `public` ফোল্ডার

Laravel এ সব রিকোয়েস্ট **`public`** ফোল্ডার দিয়ে ঢোকে। তাই **api.e3bd.com** এর Document Root হতে হবে প্রজেক্টের **`public`** ফোল্ডার, প্রজেক্ট রুট নয়।

### cPanel এ কিভাবে সেট করবেন

1. **cPanel** লগইন করুন।
2. **Domains** বা **Addon Domains** / **Subdomains** এ যান।
3. **api.e3bd.com** (বা আপনার API সাবডোমেইন) সিলেক্ট করুন।
4. **Document Root** ফিল্ড দেখুন।  
   - **ভুল:** `public_html/api.e3bd.com` বা `home/user/e3innovation-backend`  
   - **সঠিক:** `public_html/api.e3bd.com/public` অথবা যেখানে আপনার Laravel প্রজেক্টের **`public`** ফোল্ডার আছে সেই পাথ।

উদাহরণ (পাথ আপনার হোস্টিং অনুযায়ী বদলাবেন):

- যদি API সাবডোমেইন এর রুট হয়: `public_html/api.e3bd.com`  
  এবং Laravel প্রজেক্ট আছে: `public_html/api.e3bd.com/` (যেখানে `public`, `app`, `routes` আছে),  
  তাহলে Document Root করুন: **`public_html/api.e3bd.com/public`**।

5. **Save** করুন।

---

## ২. ফোল্ডার স্ট্রাকচার (উদাহরণ)

cPanel এ সাধারণত এমন থাকবে:

```
home/username/
  public_html/
    api.e3bd.com/          ← সাবডোমেইন রুট (অপশনাল)
      public/              ← Document Root এইটা হতে হবে
        .htaccess
        index.php
      app/
      bootstrap/
      config/
      routes/
      storage/
      .env
      ...
```

**Document Root = `.../api.e3bd.com/public`** (শেষে `/public` থাকতে হবে)।

---

## ৩. `.htaccess` চেক করুন

`public/.htaccess` এ নিচের রাইট রুল থাকা দরকার (Laravel ডিফল্ট):

```apache
RewriteEngine On
# ... অন্যান্য রুল ...
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

এটা থাকলে সব রিকোয়েস্ট Laravel এর `public/index.php` এ যাবে।

---

## ৪. রাউট ক্যাশ ও পারমিশন (সার্ভারে)

SSH বা cPanel Terminal দিয়ে:

```bash
cd /home/username/public_html/api.e3bd.com   # আপনার actual path
php artisan route:clear
php artisan config:clear
php artisan cache:clear
chmod -R 755 storage bootstrap/cache
```

যদি `php` কমান্ড না চলে, ফুল পাথ ব্যবহার করুন, যেমন:

```bash
/usr/local/bin/php artisan route:clear
```

---

## ৫. টেস্ট

ব্রাউজার বা Postman এ:

1. `https://api.e3bd.com/api/ping` → **200** + `{"ok":true,...}`
2. `https://api.e3bd.com/api/public/sliders` → **200** + JSON
3. `https://api.e3bd.com/api/public/settings/appearance` → **200** + JSON

এগুলো ২০০ এলে ফ্রন্ট থেকে আর ৪০৪ আসবে না।

---

## ৬. share-modal.js এরর (addEventListener null)

`share-modal.js:1 ... Cannot read properties of null (reading 'addEventListener')` সাধারণত:

- cPanel বা হোস্টিং থেকে যোগ করা **Share / Social** স্ক্রিপ্ট, অথবা
- কোনো এক্সটেনশন থেকে আসে।

কোড আমাদের প্রজেক্টে নেই। করণীয়:

- cPanel এ **Social Share** / **Share** বা এরকম কোনো প্লাগিন/স্ক্রিপ্ট অফ করুন, অথবা
- ব্রাউজার এক্সটেনশন বন্ধ করে চেক করুন।

এটা API 404 এর সাথে সম্পর্কিত না।

---

## সংক্ষেপে

| সমস্যা | সমাধান |
|--------|--------|
| সব API 404 | cPanel এ Domain/Subdomain এর **Document Root** = Laravel এর **`public`** ফোল্ডার সেট করুন |
| রাউট কাজ করে না | `php artisan route:clear` ও উপরের পারমিশন/ক্যাশ ক্লিয়ার |
| share-modal.js এরর | cPanel/হোস্টিং এর share স্ক্রিপ্ট বন্ধ করুন বা উপেক্ষা করুন |
