const express = require('express');
const VoucherController = require('../controllers/VoucherController');

const router = express.Router();

// API: /api/voucher/check
router.post('/check', VoucherController.checkVoucher);

// API: /api/voucher/active
// Lấy danh sách voucher đang hoạt động, còn lượt và chưa hết hạn
router.get('/active', VoucherController.getActiveVouchers);

module.exports = router;