const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const verifyToken = require('./middleware/authMiddleware');
require('dotenv').config();

const sequelize = require('./config/database');
require('./models/associations'); // Setup model relationships
const alertRoutes = require('./routes/alerts');
const authRoutes = require('./routes/auth');
const cartRoutes = require('./routes/cart');
const myOrderRoutes = require('./routes/order');
const dashboardRoutes = require('./routes/dashboard');
const userRoutes = require('./routes/users');
const reviewRoutes = require('./routes/review');
const categoryRoutes = require('./routes/categories');
const productRoutes = require('./routes/products');
const orderRoutes = require('./routes/orders');
const statisticsRoutes = require('./routes/statistics');
const uploadRoutes = require('./routes/upload');

const supplierRoutes = require('./routes/suppliers');
const inventoryRoutes = require('./routes/inventory');
const returnRoutes = require('./routes/returns');

const app = express();

app.use(helmet());
app.use(cors({ 
    origin: ['http://localhost:8000', 'http://127.0.0.1:8000'] 
}));
app.use(express.json());

// Routes
app.get('/', (req, res) => {
  res.json({ message: 'Cosmetic Shop API running' });
});

app.use('/api/alerts', alertRoutes);
app.use('/api/auth', authRoutes);
app.use('/api/cart',verifyToken, cartRoutes);
app.use('/api/order',verifyToken, myOrderRoutes);
app.use('/api/dashboard', dashboardRoutes);
app.use('/api/users', userRoutes);
app.use('/api/reviews', reviewRoutes);

app.use('/api/categories', categoryRoutes);
app.use('/api/products', productRoutes);
app.use('/api/orders', orderRoutes);
app.use('/api/statistics', statisticsRoutes);
app.use('/api/upload', uploadRoutes);

app.use('/api/suppliers', supplierRoutes);
app.use('/api/inventory', inventoryRoutes);
app.use('/api/returns', returnRoutes);

app.use('/api/voucher', require('./routes/voucher'));
app.use('/api/notifications', require('./routes/notifications'));
app.use('/api/chat', require('./routes/chat'));

// --- Mock Payment API cho Đồ án (Vượt tường lửa + CORS) ---
app.get('/api/next-order-id', async (req, res) => {
    try {
        const [result] = await sequelize.query('SELECT MAX(ma_don_hang) as maxId FROM don_hang');
        const maxId = result[0].maxId || 0;
        
        const todayStart = new Date();
        todayStart.setHours(0, 0, 0, 0);
        const [countResult] = await sequelize.query(`SELECT COUNT(*) as count FROM don_hang WHERE ngay_dat >= :today`, {
            replacements: { today: todayStart }
        });
        const orderCountToday = (countResult[0].count || 0) + 1;
        
        const year = String(todayStart.getFullYear()).slice(-2);
        const month = String(todayStart.getMonth() + 1).padStart(2, '0');
        const day = String(todayStart.getDate()).padStart(2, '0');
        const nextIdStr = `${day}${month}${year}#${String(orderCountToday).padStart(2, '0')}`;
        
        res.json({ nextId: maxId + 1, nextIdStr: nextIdStr });
    } catch (e) {
        res.json({ nextId: Math.floor(Math.random() * 1000000), nextIdStr: 'UNKNOWN#01' });
    }
});

app.post('/api/create-mock-webhook', async (req, res) => {
    try {
        const response = await fetch('https://webhook.site/token', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                default_content: req.body.html_content,
                default_content_type: "text/html",
                default_status: 200
            })
        });
        const data = await response.json();
        res.json({ success: true, uuid: data.uuid, url: 'https://webhook.site/' + data.uuid });
    } catch (e) {
        res.status(500).json({ success: false, error: e.toString() });
    }
});

app.get('/api/check-mock-webhook/:uuid', async (req, res) => {
    try {
        const response = await fetch('https://webhook.site/token/' + req.params.uuid + '/requests');
        const data = await response.json();
        console.log(`[Webhook Poll] UUID: ${req.params.uuid}, Reqs: ${data.data ? data.data.length : 'undefined'}`);
        res.json({ success: true, requests: data.data || [] });
    } catch (e) {
        console.error('[Webhook Poll Error]', e);
        res.status(500).json({ success: false, error: e.toString() });
    }
});
// ------------------------------------------

// Kết nối database và khởi động server
const PORT = process.env.PORT || 3000;
const NODE_ENV = process.env.NODE_ENV || 'development';

sequelize
  .authenticate()
  .then(async () => {
    console.log('Database connected successfully.');
    
    // Chỉ tự động sync schema trong môi trường development
    // Production: phải dùng migration thủ công để tránh mất dữ liệu
    if (NODE_ENV !== 'production') {
      try {
        await sequelize.sync(); // Bỏ alter: true để tránh lỗi 64 keys index
        console.log('Database schema synchronized (development mode).');
        
        // --- TEMPORARY MIGRATIONS ---
        try { await sequelize.query('ALTER TABLE nguoi_dung ADD COLUMN so_lan_dang_nhap_sai INT DEFAULT 0'); console.log('Added so_lan_dang_nhap_sai'); } catch(e) {}
        try { await sequelize.query('ALTER TABLE nguoi_dung ADD COLUMN thoi_gian_sai_cuoi DATETIME NULL'); console.log('Added thoi_gian_sai_cuoi'); } catch(e) {}
        try { await sequelize.query('ALTER TABLE nguoi_dung ADD COLUMN thoi_gian_khoa_tam_thoi DATETIME NULL'); console.log('Added thoi_gian_khoa_tam_thoi'); } catch(e) {}
        try { await sequelize.query('ALTER TABLE nguoi_dung ADD COLUMN khoa_mua_hang_den DATETIME NULL'); console.log('Added khoa_mua_hang_den'); } catch(e) {}
        try { 
            await sequelize.query(`
                CREATE TABLE IF NOT EXISTS canh_bao_he_thong (
                    ma_canh_bao INT AUTO_INCREMENT PRIMARY KEY,
                    ma_nguoi_dung INT NULL,
                    loai_canh_bao VARCHAR(50),
                    noi_dung TEXT,
                    da_doc BOOLEAN DEFAULT FALSE,
                    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            `); 
            console.log('Created canh_bao_he_thong'); 
        } catch(e) {}
        try { await sequelize.query('ALTER TABLE khuyen_mai ADD COLUMN ma_san_pham INT NULL AFTER gia_tri'); console.log('Added ma_san_pham'); } catch(e) {}
        try { await sequelize.query('ALTER TABLE khuyen_mai ADD COLUMN ma_danh_muc INT NULL AFTER ma_san_pham'); console.log('Added ma_danh_muc'); } catch(e) {}
        try { await sequelize.query('ALTER TABLE khuyen_mai ADD CONSTRAINT fk_km_sp FOREIGN KEY (ma_san_pham) REFERENCES san_pham(ma_san_pham) ON DELETE CASCADE'); console.log('Added fk_km_sp'); } catch(e) {}
        try { await sequelize.query('ALTER TABLE khuyen_mai ADD CONSTRAINT fk_km_dm FOREIGN KEY (ma_danh_muc) REFERENCES danh_muc(ma_danh_muc) ON DELETE CASCADE'); console.log('Added fk_km_dm'); } catch(e) {}
        try { await sequelize.query('ALTER TABLE san_pham ADD COLUMN gia_nhap DECIMAL(12,2) NULL'); console.log('Added gia_nhap to san_pham'); } catch(e) {}
        try { await sequelize.query('ALTER TABLE bien_the_san_pham ADD COLUMN gia_nhap DECIMAL(12,2) NULL'); console.log('Added gia_nhap to bien_the_san_pham'); } catch(e) {}
        // --- END TEMPORARY MIGRATIONS ---

      } catch (err) {
        console.error('Sync schema error:', err);
      }
    }
    
    const server = app.listen(PORT, () => console.log(`API running on port ${PORT} [${NODE_ENV}]`));
    server.on('error', (err) => {
        console.error('Server error:', err);
    });
  })
  .catch((err) => {
    console.error('Database connection failed:', err.message);
  });