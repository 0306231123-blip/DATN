const express = require('express');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');
const UploadController = require('../controllers/UploadController');

const router = express.Router();
router.use(verifyToken, requireAdmin);

const UPLOAD_DIR = path.resolve(__dirname, '../../laravel-frontend/public/images/products');
if (!fs.existsSync(UPLOAD_DIR)) {
  fs.mkdirSync(UPLOAD_DIR, { recursive: true });
}

const storage = multer.diskStorage({
  destination: (req, file, cb) => cb(null, UPLOAD_DIR),
  filename: (req, file, cb) => {
    const ext = path.extname(file.originalname).toLowerCase();
    const uniqueName = `product_${Date.now()}_${Math.random().toString(36).substring(2, 8)}${ext}`;
    cb(null, uniqueName);
  },
});

const fileFilter = (req, file, cb) => {
  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
  if (allowedTypes.includes(file.mimetype)) {
    cb(null, true);
  } else {
    cb(new Error('Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WebP)'), false);
  }
};

const upload = multer({
  storage,
  fileFilter,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB
});

// POST /api/upload - Upload a single image
router.post('/', (req, res, next) => {
  upload.single('image')(req, res, (err) => {
    if (err instanceof multer.MulterError) {
      if (err.code === 'LIMIT_FILE_SIZE') {
        return res.status(400).json({ status: 'error', message: 'File quá lớn. Giới hạn tối đa 5MB.' });
      }
      return res.status(400).json({ status: 'error', message: err.message });
    }
    if (err) {
      return res.status(400).json({ status: 'error', message: err.message });
    }
    next();
  });
}, UploadController.uploadImage);

// GET /api/upload/images - List all available images
router.get('/images', UploadController.listImages);

module.exports = router;
