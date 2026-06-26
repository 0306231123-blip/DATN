const express = require('express');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');

const router = express.Router();

router.use(verifyToken, requireAdmin);

// Upload destination: laravel-frontend/public/images/products/
const UPLOAD_DIR = path.resolve(__dirname, '../../laravel-frontend/public/images/products');
// Also scan root images folder
const IMAGES_ROOT = path.resolve(__dirname, '../../laravel-frontend/public/images');

// Ensure upload directory exists
if (!fs.existsSync(UPLOAD_DIR)) {
  fs.mkdirSync(UPLOAD_DIR, { recursive: true });
}

// Multer config
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, UPLOAD_DIR);
  },
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
router.post('/', (req, res) => {
  upload.single('image')(req, res, (err) => {
    if (err instanceof multer.MulterError) {
      if (err.code === 'LIMIT_FILE_SIZE') {
        return res.status(400).json({
          status: 'error',
          message: 'File quá lớn. Giới hạn tối đa 5MB.',
        });
      }
      return res.status(400).json({ status: 'error', message: err.message });
    }
    if (err) {
      return res.status(400).json({ status: 'error', message: err.message });
    }
    if (!req.file) {
      return res.status(400).json({ status: 'error', message: 'Không có file nào được gửi.' });
    }

    const imageUrl = `/images/products/${req.file.filename}`;

    res.json({
      status: 'success',
      message: 'Upload ảnh thành công',
      data: {
        filename: req.file.filename,
        url: imageUrl,
        size: req.file.size,
        mimetype: req.file.mimetype,
      },
    });
  });
});

// GET /api/upload/images - List all available images
router.get('/images', (req, res) => {
  try {
    const images = [];
    const allowedExts = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];

    // Scan root images folder (exclude subdirectories' files listed separately)
    if (fs.existsSync(IMAGES_ROOT)) {
      const rootFiles = fs.readdirSync(IMAGES_ROOT);
      rootFiles.forEach((file) => {
        const filePath = path.join(IMAGES_ROOT, file);
        const stat = fs.statSync(filePath);
        if (stat.isFile() && allowedExts.includes(path.extname(file).toLowerCase())) {
          images.push({
            filename: file,
            url: `/images/${file}`,
            size: stat.size,
            folder: 'images',
            modified: stat.mtime,
          });
        }
      });
    }

    // Scan products subfolder
    if (fs.existsSync(UPLOAD_DIR)) {
      const productFiles = fs.readdirSync(UPLOAD_DIR);
      productFiles.forEach((file) => {
        const filePath = path.join(UPLOAD_DIR, file);
        const stat = fs.statSync(filePath);
        if (stat.isFile() && allowedExts.includes(path.extname(file).toLowerCase())) {
          images.push({
            filename: file,
            url: `/images/products/${file}`,
            size: stat.size,
            folder: 'products',
            modified: stat.mtime,
          });
        }
      });
    }

    // Sort: newest first
    images.sort((a, b) => new Date(b.modified) - new Date(a.modified));

    res.json({
      status: 'success',
      data: images,
      total: images.length,
    });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
});

module.exports = router;
