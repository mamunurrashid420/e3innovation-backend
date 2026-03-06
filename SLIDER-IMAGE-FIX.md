# স্লাইডার ইমেজ দেখানোর সম্পূর্ণ সমাধান

## ১. Backend এ এই তিনটা চেক করুন

### Step 1: APP_URL সেট করুন (জরুরি)

Backend এর `.env` ফাইলে (প্রডাকশন সার্ভারে) নিশ্চিত করুন:

```env
APP_URL=https://api.e3bd.com
```

**খেয়াল:** `https` থাকতে হবে। সেভ করে config ক্যাশ ক্লিয়ার করুন:

```bash
cd /path/to/e3innovation-backend
php artisan config:clear
```

### Step 2: Storage Link তৈরি করুন

ইমেজ ফাইলগুলো `storage/app/public/sliders/` এ সেভ হয়, কিন্তু ব্রাউজার এক্সেস করে `https://api.e3bd.com/storage/sliders/...` দিয়ে। এটা কাজ করার জন্য **symbolic link** দরকার।

সার্ভারে একবার চালান:

```bash
cd /path/to/e3innovation-backend
php artisan storage:link
```

এটা `public/storage` ফোল্ডার তৈরি করবে যেটা `storage/app/public` এর দিকে পয়েন্ট করে।  
নাহলে `https://api.e3bd.com/storage/sliders/xyz.jpg` 404 দেবে।

### Step 3: ইমেজ ফাইল আছে কিনা দেখুন

সার্ভারে চেক করুন যে স্লাইডার ইমেজ সত্যিই সেভ হচ্ছে:

```bash
ls -la storage/app/public/sliders/
```

এখানে আপনার আপলোড করা ছবির ফাইল থাকা উচিত।

---

## ২. টেস্ট করুন

### ব্রাউজারে সরাসরি ইমেজ URL

1. Admin থেকে একটা স্লাইডার এড করুন (ইমেজসহ)।
2. API থেকে স্লাইডার লিস্ট দেখুন:  
   `https://api.e3bd.com/api/sliders/active`  
   প্রতিটি স্লাইডারের `image` ফিল্ডে এমন URL থাকবে:  
   `https://api.e3bd.com/storage/sliders/xxxxx.jpg`
3. সেই URL টা ব্রাউজারের অ্যাড্রেস বারে paste করে ওপেন করুন।  
   - **ইমেজ দেখা গেলে** = Backend ঠিক আছে। সাইটে না দেখালে ফ্রন্ট ক্যাশ বা বিল্ড চেক করুন।  
   - **404 এলে** = `php artisan storage:link` করুন এবং আবার চেক করুন।

### সাইটে

হোম পেজ রিফ্রেশ (F5) করুন। নতুন স্লাইডার অ্যাড করার পর একবার রিফ্রেশ দিলে নতুন ইমেজ লোড হয়।

---

## ৩. যা কোডে ঠিক করা হয়েছে

- **Backend:** স্লাইডার ইমেজের জন্য এখন Laravel এর `Storage::disk('public')->url()` ব্যবহার করা হয়। তাই ইমেজ URL সবসময় `APP_URL` দিয়ে বানছে (যেমন `https://api.e3bd.com/storage/sliders/xxx.jpg`)।
- **Frontend:** স্লাইডার ইমেজ URL এবং ফেইল হলে ডিফল্ট ইমেজ দেখানোর লজিক শক্তিশালী করা হয়েছে।

---

## চেকলিস্ট

- [ ] Backend `.env` এ `APP_URL=https://api.e3bd.com`
- [ ] `php artisan config:clear` রান করা
- [ ] `php artisan storage:link` রান করা
- [ ] `storage/app/public/sliders/` এ ফাইল আছে
- [ ] ব্রাউজারে `https://api.e3bd.com/storage/sliders/ফাইলনাম.jpg` ওপেন করে ইমেজ দেখা যাচ্ছে
- [ ] সাইটে হোম পেজ রিফ্রেশ দিয়ে স্লাইডার ইমেজ চেক করা
