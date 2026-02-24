# Fix 404 on GET /api/public/sliders

## ১. সার্ভারে লেটেস্ট কোড আছে কিনা চেক করুন

নিশ্চিত করুন এই ফাইলগুলো প্রডাকশনে আপডেট করা আছে:
- `routes/api.php` – যেখানে `Route::prefix('public')->group(...)` এর ভেতরে `Route::get('/sliders', [SliderController::class, 'indexPublic']);` আছে
- `app/Http/Controllers/SliderController.php` – যেখানে `indexPublic()` মেথড আছে

## ২. রাউট ক্যাশ ক্লিয়ার ও লিস্ট চেক

SSH দিয়ে সার্ভারে লগইন করে ব্যাকএন্ড প্রজেক্ট ফোল্ডারে চলে যান, তারপর:

```bash
cd /home/ebdcom/api.e3bd.com   # আপনার actual path দিন

# রাউট ক্যাশ সরান
php artisan route:clear

# পাবলিক রাউটগুলো দেখুন (public/sliders থাকা উচিত)
php artisan route:list --path=public
```

আউটপুটে `GET|HEAD api/public/sliders ... indexPublic` এরকম একটা লাইন দেখতে পাবেন।

## ৩. আবার রাউট ক্যাশ (যদি ব্যবহার করেন)

প্রডাকশনে রাউট ক্যাশ চালু থাকলে ডিপ্লয়ের পর একবার:

```bash
php artisan route:cache
```

## ৪. ওয়েব সার্ভার রিস্টার্ট (প্রয়োজন হলে)

Nginx ব্যবহার করলে:

```bash
sudo systemctl reload nginx
```

Apache ব্যবহার করলে:

```bash
sudo systemctl reload apache2
```

## ৫. ব্রাউজার/পোস্টম্যানে টেস্ট

- URL: `https://api.e3bd.com/api/public/sliders`
- Method: GET
- ২০০ ও JSON ডেটা আসা উচিত।

এতেও ৪০৪ আসলে: `php artisan route:list` এর পুরো আউটপুট দেখে নিন `api/public` related রাউটগুলো ঠিক রেজিস্টার হয়েছে কিনা।
