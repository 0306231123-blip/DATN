# 🏗️ Cosmetic Shop - Project Architecture & Development Guide

## 📊 Project Structure

```
cosmetic-shop/
├── nodejs-backend/           # Node.js/Express API
│   ├── config/              # Database config
│   ├── controllers/         # Business logic
│   ├── models/              # Sequelize models
│   ├── routes/              # API routes
│   ├── middleware/          # Validation, auth
│   ├── utils/               # Utilities
│   ├── docs/                # API documentation
│   └── index.js             # Server entry point
│
├── laravel-frontend/        # Laravel/Blade views
│   ├── resources/views/admin/    # Admin pages
│   └── routes/web.php            # Web routes
│
└── python-ai/               # AI services (future)
```

---

## 🔧 Backend Development Pattern

### **1. Tạo Model** (`models/`)
```javascript
const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const ModelName = sequelize.define('table_name', {
  id_field: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  other_field: {
    type: DataTypes.STRING(255),
    allowNull: false,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});

module.exports = ModelName;
```

### **2. Tạo Controller** (`controllers/`)
```javascript
const ModelName = require('../models/ModelName');
const { Op } = require('sequelize');

class ControllerName {
  async getAll(req, res) {
    try {
      const { page = 1, limit = 10, search = '' } = req.query;
      const offset = (page - 1) * limit;
      const where = search ? { field: { [Op.like]: `%${search}%` } } : {};

      const { count, rows } = await ModelName.findAndCountAll({
        where,
        limit: parseInt(limit),
        offset,
        order: [['created_at', 'DESC']],
      });

      res.json({
        status: 'success',
        data: rows,
        pagination: {
          total: count,
          page: parseInt(page),
          limit: parseInt(limit),
          pages: Math.ceil(count / limit),
        },
      });
    } catch (error) {
      res.status(500).json({ status: 'error', message: error.message });
    }
  }

  async getById(req, res) { /* ... */ }
  async create(req, res) { /* ... */ }
  async update(req, res) { /* ... */ }
  async delete(req, res) { /* ... */ }
}

module.exports = new ControllerName();
```

**Response Format (IMPORTANT):**
```json
{
  "status": "success",
  "data": [...],
  "pagination": { "total": 10, "page": 1, "limit": 10, "pages": 1 }
}
```

### **3. Tạo Routes** (`routes/`)
```javascript
const express = require('express');
const controller = require('../controllers/ControllerName');

const router = express.Router();

// IMPORTANT: Specific routes (static) BEFORE generic (/:id)
router.get('/stats', controller.getStats);
router.get('/', controller.getAll);
router.get('/:id', controller.getById);
router.post('/', controller.create);
router.put('/:id', controller.update);
router.delete('/:id', controller.delete);

module.exports = router;
```

### **4. Register Route** (`index.js`)
```javascript
const modelRoutes = require('./routes/model');
app.use('/api/model', modelRoutes);
```

---

## 🎨 Frontend Development Pattern

### **Structure**
```blade
@extends('layouts.admin')

@section('title', 'Page Title')
@section('page-title', 'Page Title')
@section('page-subtitle', '<span id="summary-id">Loading...</span>')

@section('content')
<!-- HTML content with id="table-id" for rendering -->
<table id="items-table">
  <tbody id="items-tbody">
    <!-- JS renders rows here -->
  </tbody>
</table>
@endsection

@section('scripts')
<script>
const API_BASE_URL = 'http://localhost:3000/api';
let items = [];

async function loadItems() {
  const response = await fetch(`${API_BASE_URL}/items`);
  const result = await response.json();
  
  if (result.status === 'success' || result.success) {
    items = result.data;
    renderTable();
  }
}

function renderTable() {
  const tbody = document.getElementById('items-tbody');
  tbody.innerHTML = items.map(item => `
    <tr>
      <td>${escapeHtml(item.field)}</td>
      <td>
        <button onclick="editItem(${item.id})">Edit</button>
        <button onclick="deleteItem(${item.id})">Delete</button>
      </td>
    </tr>
  `).join('');
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', loadItems);
</script>
@endsection
```

---

## 🎯 Database Schema

### **Table Naming Convention**
- Snake case: `danh_muc`, `nguoi_dung`, `san_pham`
- Primary key: `ma_*` (e.g., `ma_danh_muc`)
- Foreign key: `ma_*_cha` (parent), `ma_*` reference
- Timestamps: `ngay_tao` (created), `ngay_cap_nhat` (updated)
- Status: `trang_thai` ENUM('hoat_dong', 'bi_khoa')
- Role: `vai_tro` ENUM('khach_hang', 'quan_tri_vien')

### **Existing Tables**
```sql
-- danh_muc (Categories)
ma_danh_muc (PK, AI)
ten_danh_muc (VARCHAR 100, UNIQUE)
mo_ta (TEXT)
ma_danh_muc_cha (INT, FK - parent category)
thu_tu_hien_thi (INT)
ngay_tao (DATETIME)

-- nguoi_dung (Users)
ma_nguoi_dung (PK, AI)
ho_ten (VARCHAR 100)
email (VARCHAR 150, UNIQUE)
mat_khau (VARCHAR 255, hashed)
so_dien_thoai (VARCHAR 15)
dia_chi (VARCHAR 300)
vai_tro (ENUM: 'khach_hang', 'quan_tri_vien')
trang_thai (ENUM: 'hoat_dong', 'bi_khoa')
ngay_tao (DATETIME)
ngay_cap_nhat (DATETIME)

-- san_pham (Products) - TODO
-- don_hang (Orders) - TODO
```

---

## 🔐 Important Conventions

### **Response Format**
```javascript
// SUCCESS
{ "status": "success", "data": {...}, "message": "..." }

// ERROR
{ "status": "error", "message": "Error description" }

// LIST (with pagination)
{ "status": "success", "data": [...], "pagination": {...} }
```

### **HTTP Methods**
- `GET /api/resource` - List with pagination
- `GET /api/resource/:id` - Get single item
- `GET /api/resource/stats` - Statistics (BEFORE /:id route!)
- `POST /api/resource` - Create
- `PUT /api/resource/:id` - Update
- `DELETE /api/resource/:id` - Delete

### **Validation**
- Required fields: Check in controller
- Email: Regex in middleware
- Unique fields: Check before create/update
- Business logic: In controller, not model

### **Error Handling**
- 400: Bad request (validation)
- 404: Not found
- 409: Conflict (duplicate email)
- 500: Server error

---

## 🚀 Frontend Components Already Built

### **Categories** ✅
- Location: `laravel-frontend/resources/views/admin/categories.blade.php`
- Features:
  - Fetch from `/api/categories`
  - Search, pagination
  - Add/Edit/Delete via modal
  - Thống kê (stats)
  - Parent-child categories support

### **Users** ✅
- Location: `laravel-frontend/resources/views/admin/users.blade.php`
- Features:
  - Fetch from `/api/users`
  - Filter by role (khach_hang/quan_tri_vien)
  - Search by name/email
  - Add/Edit/Delete via modal
  - Stats: total, customers, admins

---

## 📋 Development Checklist for New CRUD Module

When creating new feature (e.g., Products):

- [ ] 1. Create `models/SanPham.js` with correct field names from DB
- [ ] 2. Create `controllers/ProductController.js` with CRUD methods
- [ ] 3. Create `routes/products.js` with route definitions (specific routes first!)
- [ ] 4. Add route to `index.js`: `app.use('/api/products', productRoutes)`
- [ ] 5. Create frontend `resources/views/admin/products.blade.php`
- [ ] 6. Test API endpoints first with curl
- [ ] 7. Test frontend integration
- [ ] 8. Add to navigation menu

---

## ⚡ Quick Commands

```bash
# Backend
cd nodejs-backend
npm install              # Install dependencies
npm run dev             # Start with nodemon
npm start               # Production run

# Frontend
cd laravel-frontend
composer install        # Install PHP dependencies
npm install            # Install npm dependencies
php artisan serve      # Start dev server

# API Testing
curl http://localhost:3000/api/categories
curl -X POST http://localhost:3000/api/categories \
  -H "Content-Type: application/json" \
  -d '{"ten_danh_muc":"Test"}'
```

---

## 🐛 Common Issues

### Issue: Route matches `:id` instead of specific route
**Solution:** Define specific routes BEFORE generic `:id` route

### Issue: Response format mismatch
**Solution:** All backends should return `{ status: 'success'/'error', data: ..., message: ... }`

### Issue: Node process won't restart
**Solution:** Kill all node processes: `ps aux | grep node | awk '{print $2}' | xargs kill -9`

### Issue: Database column not found
**Solution:** Verify table structure: check phpMyAdmin → table columns must match Model definition

---

## 📞 Setup Guide
See `SETUP_GUIDE.md` for complete setup and test instructions
