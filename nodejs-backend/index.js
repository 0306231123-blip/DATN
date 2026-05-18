const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
require('dotenv').config();

const app = express();

app.use(helmet());
app.use(cors({ origin: 'http://localhost:8000' }));
app.use(express.json());

app.get('/', (req, res) => {
  res.json({ message: 'Cosmetic Shop API running' });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`API running on port ${PORT}`));