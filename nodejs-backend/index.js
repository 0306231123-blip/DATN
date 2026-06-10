const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
require('dotenv').config();

const sequelize = require('./config/database');
require('./models/associations'); // Setup model relationships
const authRoutes = require('./routes/auth');
const cartRoutes = require('./routes/cart');
const orderRoutes = require('./routes/order');
const dashboardRoutes = require('./routes/dashboard');
const userRoutes = require('./routes/users');
const reviewRoutes = require('./routes/review');

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
app.use('/api/orders', orderRoutes);
app.use('/api/dashboard', dashboardRoutes);
app.use('/api/users', userRoutes);
app.use('/api/reviews', reviewRoutes);


// Kết nối database và khởi động server
const PORT = process.env.PORT || 3000;

sequelize
  .authenticate()
  .then(() => {
    console.log('Database connected successfully.');
    app.listen(PORT, () => console.log(`API running on port ${PORT}`));
  })
  .catch((err) => {
    console.error('Database connection failed:', err.message);
  });