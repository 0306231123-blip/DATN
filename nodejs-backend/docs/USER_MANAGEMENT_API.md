# Node.js User Management API Documentation

## Base URL
```
http://localhost:3000/api
```

## Overview
Backend quản lý người dùng cho admin panel, với các tính năng:
- CRUD operations (tạo, đọc, cập nhật, xóa)
- Search theo tên hoặc email
- Filter theo vai trò (khách hàng, admin)
- Phân trang
- Xuất CSV
- Thống kê

## Database Schema

### NguoiDung (Users) Table
```
- ma_nguoi_dung (INT, PRIMARY KEY, AUTO INCREMENT)
- ho_ten (VARCHAR 100, NOT NULL)
- email (VARCHAR 150, NOT NULL, UNIQUE)
- mat_khau (VARCHAR 255, NOT NULL)
- so_dien_thoai (VARCHAR 15)
- dia_chi (VARCHAR 300)
- loai_da (ENUM: da_dau, da_kho, da_hon_hop, da_nhay_cam, da_thuong)
- vai_tro (ENUM: khach_hang, quan_tri_vien, DEFAULT: khach_hang)
- trang_thai (ENUM: hoat_dong, bi_khoa, DEFAULT: hoat_dong)
- ngay_tao (DATETIME, DEFAULT: NOW)
- ngay_cap_nhat (DATETIME)
```

### DonHang (Orders) Table
```
- ma_don_hang (INT, PRIMARY KEY, AUTO INCREMENT)
- ma_nguoi_dung (INT, FOREIGN KEY -> NguoiDung.ma_nguoi_dung)
- so_don_hang (VARCHAR 50, UNIQUE)
- tong_thanh_toan (DECIMAL 10,2)
- trang_thai_don (ENUM: cho_xu_ly, dang_van_chuyen, giao_thanh_cong, huy)
- ghi_chu (TEXT)
- ngay_dat (DATETIME, DEFAULT: NOW)
- ngay_cap_nhat (DATETIME)
```

## API Endpoints

### 1. Get All Users (List with Search, Filter, Pagination)
**Endpoint:** `GET /users`

**Query Parameters:**
- `search` (string, optional): Tìm kiếm theo tên hoặc email
  - Example: `?search=Lan`
- `vai_tro` (string, optional): Lọc theo vai trò
  - Values: `khach_hang`, `quan_tri_vien`, `all`
  - Example: `?vai_tro=khach_hang`
- `per_page` (number, optional): Số bản ghi trên trang (default: 15)
  - Example: `?per_page=10`
- `page` (number, optional): Số trang (default: 1)
  - Example: `?page=2`

**Example Requests:**
```
GET /users
GET /users?search=Lan&vai_tro=khach_hang
GET /users?search=test@gmail.com&per_page=20&page=1
```

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "ma_nguoi_dung": 1,
      "ho_ten": "Lan Anh",
      "email": "lananh@gmail.com",
      "so_dien_thoai": "0912345678",
      "dia_chi": "123 Nguyen Hue, HCMC",
      "loai_da": "da_thuong",
      "vai_tro": "khach_hang",
      "trang_thai": "hoat_dong",
      "ngay_tao": "2024-06-01T10:30:00.000Z",
      "tong_don": 12,
      "tong_chi": 4200000
    },
    {
      "ma_nguoi_dung": 2,
      "ho_ten": "Minh Chau",
      "email": "mchau@gmail.com",
      "so_dien_thoai": "0987654321",
      "dia_chi": "456 Le Loi, HCMC",
      "loai_da": "da_kho",
      "vai_tro": "khach_hang",
      "trang_thai": "hoat_dong",
      "ngay_tao": "2024-06-02T11:45:00.000Z",
      "tong_don": 5,
      "tong_chi": 1600000
    }
  ],
  "pagination": {
    "total": 284,
    "per_page": 15,
    "current_page": 1,
    "last_page": 19
  }
}
```

### 2. Get User by ID
**Endpoint:** `GET /users/:id`

**Path Parameters:**
- `id` (number, required): Mã người dùng

**Example Request:**
```
GET /users/1
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "ma_nguoi_dung": 1,
    "ho_ten": "Lan Anh",
    "email": "lananh@gmail.com",
    "so_dien_thoai": "0912345678",
    "dia_chi": "123 Nguyen Hue, HCMC",
    "loai_da": "da_thuong",
    "vai_tro": "khach_hang",
    "trang_thai": "hoat_dong",
    "ngay_tao": "2024-06-01T10:30:00.000Z",
    "ngay_cap_nhat": null,
    "tong_don": 12,
    "tong_chi": 4200000
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Người dùng không tồn tại."
}
```

### 3. Create New User
**Endpoint:** `POST /users`

**Request Body (JSON):**
```json
{
  "ho_ten": "Nguyen Van A",
  "email": "nguyenvana@example.com",
  "so_dien_thoai": "0987654321",
  "dia_chi": "789 Tran Hung Dao, HCMC",
  "vai_tro": "khach_hang",
  "password": "SecurePass123"
}
```

**Required Fields:**
- `ho_ten` (string): Họ tên (tối đa 100 ký tự)
- `email` (string): Email (phải là định dạng email hợp lệ, unique)
- `password` (string): Mật khẩu (tối thiểu 6 ký tự)

**Optional Fields:**
- `so_dien_thoai` (string): Số điện thoại (10-15 chữ số)
- `dia_chi` (string): Địa chỉ
- `vai_tro` (string): Vai trò - `khach_hang` hoặc `quan_tri_vien` (default: khach_hang)

**Success Response (201):**
```json
{
  "success": true,
  "message": "Người dùng được tạo thành công.",
  "data": {
    "ma_nguoi_dung": 285,
    "ho_ten": "Nguyen Van A",
    "email": "nguyenvana@example.com",
    "vai_tro": "khach_hang"
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Vui lòng nhập đầy đủ họ tên, email và mật khẩu."
}
```

**Error Response (409):**
```json
{
  "success": false,
  "message": "Email này đã tồn tại trong hệ thống."
}
```

### 4. Update User
**Endpoint:** `PUT /users/:id`

**Path Parameters:**
- `id` (number, required): Mã người dùng

**Request Body (JSON):**
```json
{
  "ho_ten": "Nguyen Van A Updated",
  "email": "nguyenvana_updated@example.com",
  "so_dien_thoai": "0912345679",
  "dia_chi": "999 Pasteur, HCMC",
  "vai_tro": "quan_tri_vien",
  "trang_thai": "hoat_dong",
  "password": "NewSecurePass123"
}
```

**Optional Fields (update only what you need):**
- `ho_ten` (string): Họ tên
- `email` (string): Email
- `so_dien_thoai` (string): Số điện thoại
- `dia_chi` (string): Địa chỉ
- `vai_tro` (string): Vai trò
- `trang_thai` (string): Trạng thái
- `password` (string): Mật khẩu mới (nếu muốn thay đổi)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cập nhật người dùng thành công.",
  "data": {
    "ma_nguoi_dung": 1,
    "ho_ten": "Nguyen Van A Updated",
    "email": "nguyenvana_updated@example.com",
    "vai_tro": "quan_tri_vien"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Người dùng không tồn tại."
}
```

### 5. Delete User
**Endpoint:** `DELETE /users/:id`

**Path Parameters:**
- `id` (number, required): Mã người dùng

**Example Request:**
```
DELETE /users/1
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Xóa người dùng thành công."
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Người dùng không tồn tại."
}
```

### 6. Export Users to CSV
**Endpoint:** `GET /users/export/csv`

**Query Parameters:**
- `search` (string, optional): Tìm kiếm theo tên hoặc email
- `vai_tro` (string, optional): Lọc theo vai trò

**Example Request:**
```
GET /users/export/csv?vai_tro=khach_hang&search=Lan
```

**Response:** CSV file download with filename `users_[timestamp].csv`

**CSV Format:**
```
Mã,Tên,Email,Điện thoại,Địa chỉ,Vai trò,Tổng đơn,Tổng chi,Ngày tạo
1,"Lan Anh","lananh@gmail.com","0912345678","123 Nguyen Hue HCMC","khach_hang","12","4200000.00","2024-06-01T10:30:00.000Z"
2,"Minh Chau","mchau@gmail.com","0987654321","456 Le Loi HCMC","khach_hang","5","1600000.00","2024-06-02T11:45:00.000Z"
```

### 7. Get User Statistics
**Endpoint:** `GET /users/statistics/overview`

**Example Request:**
```
GET /users/statistics/overview
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "tong_nguoi_dung": 284,
    "khach_hang": 280,
    "quan_tri_vien": 4,
    "nguoi_dung_thang_nay": 67
  }
}
```

## Validation Rules

### Create User Validation
- `ho_ten` (required): String, tối đa 100 ký tự
- `email` (required): Email format, unique
- `password` (required): Tối thiểu 6 ký tự
- `so_dien_thoai` (optional): 10-15 chữ số
- `dia_chi` (optional): String
- `vai_tro` (optional): `khach_hang` hoặc `quan_tri_vien`

### Update User Validation
- `ho_ten` (optional): String, tối đa 100 ký tự
- `email` (optional): Email format, unique (excluding current user)
- `password` (optional): Tối thiểu 6 ký tự nếu được cung cấp
- `so_dien_thoai` (optional): 10-15 chữ số
- `dia_chi` (optional): String
- `vai_tro` (optional): `khach_hang` hoặc `quan_tri_vien`
- `trang_thai` (optional): `hoat_dong` hoặc `bi_khoa`

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Vui lòng nhập đầy đủ họ tên, email và mật khẩu."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Người dùng không tồn tại."
}
```

### 409 Conflict (Email exists)
```json
{
  "success": false,
  "message": "Email này đã tồn tại trong hệ thống."
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "Lỗi khi lấy danh sách người dùng.",
  "error": "error message details"
}
```

## Testing with cURL

### List all users
```bash
curl -X GET "http://localhost:3000/api/users"
```

### Search and filter users
```bash
curl -X GET "http://localhost:3000/api/users?search=Lan&vai_tro=khach_hang&per_page=10"
```

### Get user by ID
```bash
curl -X GET "http://localhost:3000/api/users/1"
```

### Create new user
```bash
curl -X POST "http://localhost:3000/api/users" \
  -H "Content-Type: application/json" \
  -d '{
    "ho_ten": "Nguyen Van B",
    "email": "nguyenvanb@example.com",
    "so_dien_thoai": "0912345678",
    "dia_chi": "123 Le Loi, HCMC",
    "vai_tro": "khach_hang",
    "password": "SecurePass123"
  }'
```

### Update user
```bash
curl -X PUT "http://localhost:3000/api/users/1" \
  -H "Content-Type: application/json" \
  -d '{
    "ho_ten": "Lan Anh Updated",
    "email": "lananh_updated@gmail.com",
    "vai_tro": "quan_tri_vien"
  }'
```

### Delete user
```bash
curl -X DELETE "http://localhost:3000/api/users/1"
```

### Export users to CSV
```bash
curl -X GET "http://localhost:3000/api/users/export/csv?vai_tro=khach_hang" -o users.csv
```

### Get statistics
```bash
curl -X GET "http://localhost:3000/api/users/statistics/overview"
```

## Usage Examples

### JavaScript/Axios
```javascript
// List users
const getUsers = async () => {
  const response = await axios.get('http://localhost:3000/api/users?per_page=10&page=1');
  console.log(response.data);
};

// Create user
const createUser = async () => {
  const response = await axios.post('http://localhost:3000/api/users', {
    ho_ten: 'Tran Thi C',
    email: 'tranthic@example.com',
    password: 'SecurePass123',
    vai_tro: 'khach_hang'
  });
  console.log(response.data);
};

// Update user
const updateUser = async (userId) => {
  const response = await axios.put(`http://localhost:3000/api/users/${userId}`, {
    ho_ten: 'Updated Name',
    email: 'updatedemail@example.com'
  });
  console.log(response.data);
};

// Delete user
const deleteUser = async (userId) => {
  const response = await axios.delete(`http://localhost:3000/api/users/${userId}`);
  console.log(response.data);
};
```

## Setup Instructions

### 1. Install Dependencies
```bash
npm install
```

### 2. Configure Environment Variables (.env)
```
PORT=3000
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=db_my_pham
JWT_SECRET=your_secret_key_here
```

### 3. Run the Server
```bash
npm start
# Or for development with auto-reload:
npm run dev
```

## File Structure
```
nodejs-backend/
├── index.js                      # Main entry point
├── package.json
├── .env
├── config/
│   └── database.js              # Sequelize configuration
├── models/
│   ├── NguoiDung.js            # User model
│   └── DonHang.js              # Order model
├── controllers/
│   └── UserController.js         # User business logic
├── routes/
│   ├── auth.js                 # Authentication routes
│   ├── dashboard.js            # Dashboard routes
│   └── users.js                # User management routes
└── middleware/
    └── userValidation.js        # User validation middleware
```

## Notes
- Tất cả password được hash sử dụng bcryptjs
- Email phải là unique trong hệ thống
- Role mặc định cho người dùng mới là 'khach_hang'
- Trạng thái mặc định cho người dùng mới là 'hoat_dong'
- Khi xóa một người dùng, tất cả đơn hàng của người dùng đó cũng sẽ bị xóa (do foreign key constraint)
