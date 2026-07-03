const path = require('path');
const fs = require('fs');

const UPLOAD_DIR = path.resolve(__dirname, '../../laravel-frontend/public/images/products');
const IMAGES_ROOT = path.resolve(__dirname, '../../laravel-frontend/public/images');

class UploadController {
  // Handle image upload from multer
  uploadImage(req, res) {
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
  }

  // GET /api/upload/images - List all available images
  listImages(req, res) {
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
  }
}

module.exports = new UploadController();
