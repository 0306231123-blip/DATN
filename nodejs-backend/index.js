const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
require('dotenv').config();

const sequelize = require('./config/database');
require('./models/associations'); // Setup model relationships
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

app.use('/api/auth', authRoutes);
app.use('/api/cart', cartRoutes);
app.use('/api/order', myOrderRoutes);
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
        await sequelize.sync({ alter: true });
        console.log('Database schema synchronized (development mode).');
      } catch (err) {
        console.error('Sync schema error:', err);
      }
    }
    
    app.listen(PORT, () => console.log(`API running on port ${PORT} [${NODE_ENV}]`));
  })
  .catch((err) => {
    console.error('Database connection failed:', err.message);
  });