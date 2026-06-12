# 🚀 Setup Hướng dẫn chạy dự án Cosmetic Shop

## 📋 Yêu cầu
- XAMPP (PHP, MySQL)
- Node.js v14+
- Laravel 11
- npm

---

## 🔧 Backend Setup (Node.js API)

### 1. Cài đặt dependencies
```bash
cd nodejs-backend
npm install
```

### 2. Cấu hình .env
```
PORT=3000
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=db_my_pham
JWT_SECRET=your_secret_key_here
AI_SERVICE_URL=http://localhost:5000
```

### 3. Khởi động server
```bash
npm run dev
```

**Output expected:**
```
Database connected successfully.
API running on port 3000
```

---

## 🎨 Frontend Setup (Laravel)

### 1. Cài đặt dependencies
```bash
cd laravel-frontend
composer install
npm install
```

### 2. Cấu hình .env
```
APP_NAME=CosmeticShop
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_my_pham
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Khởi động server
```bash
php artisan serve --host=localhost --port=8000
```

---

## ✅ Test API Endpoints

### Categories API
```bash
# Get all categories
curl http://localhost:3000/api/categories

# Get category stats
curl http://localhost:3000/api/categories/stats

# Get category by ID
curl http://localhost:3000/api/categories/1

# Create category
curl -X POST http://localhost:3000/api/categories \
  -H "Content-Type: application/json" \
  -d '{"ten_danh_muc":"Tester","mo_ta":"Test category"}'

# Update category
curl -X PUT http://localhost:3000/api/categories/1 \
  -H "Content-Type: application/json" \
  -d '{"ten_danh_muc":"Updated Name"}'

# Delete category
curl -X DELETE http://localhost:3000/api/categories/1
```

---

## 🌐 Frontend URLs

- **Home**: http://localhost:8000
- **Admin Dashboard**: http://localhost:8000/admin/dashboard
- **Categories**: http://localhost:8000/admin/categories
- **Users**: http://localhost:8000/admin/users

---

## 📊 Database Structure

### danh_muc (Categories)
```
ma_danh_muc (INT, PK, AI)
ten_danh_muc (VARCHAR 100)
mo_ta (TEXT)
ma_danh_muc_cha (INT, FK - Parent category)
thu_tu_hien_thi (INT)
ngay_tao (DATETIME)
```

---

## 🐛 Troubleshooting

### Error: "Cannot GET /api/categories"
- ✅ Backend is not running
- Solution: Run `npm run dev` in nodejs-backend folder

### Error: "Network Error"
- ✅ CORS blocked or wrong API URL
- Solution: Check API_BASE_URL in categories.blade.php

### Error: "Unknown column"
- ✅ Database structure doesn't match model
- Solution: Check danh_muc table columns match DanhMuc.js model

---

## 📝 Features Implemented

✅ Categories Management (CRUD)
✅ Categories API with pagination
✅ Search functionality
✅ Parent-child category support
✅ Display order
✅ Statistics endpoint

---

## 🎯 Next Steps

1. Implement Products (SanPham) API & Frontend
2. Add Orders Management
3. Add User Authentication
4. Setup AI recommendations
5. Add testing suite

---

## 👤 Support
For issues, check the API documentation in `nodejs-backend/docs/`

