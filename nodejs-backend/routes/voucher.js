const express = require('express');
const VoucherController = require('../controllers/VoucherController');

const router = express.Router();

// Specific routes first
router.post('/check', VoucherController.checkVoucher);
router.get('/active', VoucherController.getActiveVouchers);

// CRUD routes
router.get('/', VoucherController.getAll);
router.get('/:id', VoucherController.getById);
router.post('/', VoucherController.create);
router.put('/:id', VoucherController.update);
router.delete('/:id', VoucherController.delete);

module.exports = router;