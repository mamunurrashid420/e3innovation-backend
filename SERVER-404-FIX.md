# সব Public API 404 দিলে – সার্ভার ফিক্স

যখন **sliders, services, projects, team-members, appearance, footer, social** সব 404 দেয়, তখন সাধারণত সার্ভার **`/api`** রিকোয়েস্ট Laravel পর্যন্ত পৌঁছাচ্ছে না।

---

## ১. প্রথমে টেস্ট করুন

ডিপ্লয়ের পর ব্রাউজার বা curl দিয়ে চেক করুন:

```text
GET https://api.e3bd.com/api/ping
```

- **২০০ + `{"ok":true,...}`** মানে API চালু, সমস্যা অন্য জায়গায়।
- **৪০৪** মানে `/api/*` রিকোয়েস্ট Laravel এ যাচ্ছে না → নিচের সার্ভার কনফিগ চেক করুন।

---

## ২. Nginx কনফিগ (সব রিকোয়েস্ট Laravel এর দিকে)

`api.e3bd.com` এর জন্য যেই সাইট কনফিগ আছে (যেমন `/etc/nginx/sites-available/api.e3bd.com`), সেখানে **root** অবশ্যই Laravel এর **`public`** ফোল্ডার হতে হবে:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name api.e3bd.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.e3bd.com;
    root /home/ebdcom/api.e3bd.com/public;   # Laravel এর public ফোল্ডার

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;  # আপনার PHP ভার্সান অনুযায়ী
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
}
```

জরুরি জিনিস:
- **`root`** = `.../api.e3bd.com/public` (শেষে `/public` থাকতে হবে)
- **`location /`** এ `try_files ... /index.php?$query_string;` থাকতে হবে যাতে সব রিকোয়েস্ট `public/index.php` এ যায়

কনফিগ এডিটের পর:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## ৩. Laravel রাউট ক্লিয়ার (সার্ভারে)

SSH এ ব্যাকএন্ড প্রজেক্ট ফোল্ডারে গিয়ে:

```bash
cd /home/ebdcom/api.e3bd.com
php artisan route:clear
php artisan route:list
```

`route:list` এ অন্তত এগুলো থাকা উচিত:
- `GET api/ping`
- `GET api/public/sliders`
- `GET api/public/settings/{group}`
- `GET api/services`
- ইত্যাদি

যদি এগুলো না থাকে তাহলে কোড/ডিপ্লয় চেক করুন।

---

## ৪. পারমিশন

```bash
cd /home/ebdcom/api.e3bd.com
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

(যদি অন্য ইউজার দিয়ে PHP-FPM চলে তাহলে সেই ইউজার দিয়ে `chown` দিন।)

---

## ৫. আবার টেস্ট

1. **`https://api.e3bd.com/api/ping`** → 200
2. **`https://api.e3bd.com/api/public/sliders`** → 200 + JSON
3. **`https://api.e3bd.com/api/public/settings/appearance`** → 200 + JSON

এগুলো ঠিক থাকলে ফ্রন্ট থেকে আর 404 আসা উচিত না।

---

## সংক্ষেপে

| সমস্যা | করণীয় |
|--------|--------|
| সব API 404 | Nginx `root` = `.../public` এবং `try_files ... /index.php?$query_string` চেক করুন |
| রাউট নেই | `php artisan route:clear` এবং লেটেস্ট কোড ডিপ্লয় |
| পারমিশন | `storage`, `bootstrap/cache` ওনার/পারমিশন ঠিক করুন |
