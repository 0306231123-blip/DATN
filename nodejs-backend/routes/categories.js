const express = require('express');
const categoryController = require('../controllers/CategoryController');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');

const router = express.Router();

router.get('/stats', verifyToken, requireAdmin, categoryController.getStats);
router.get('/', categoryController.getAll);
router.get('/:id', categoryController.getById);
router.post('/', verifyToken, requireAdmin, categoryController.create);
router.put('/:id', verifyToken, requireAdmin, categoryController.update);
router.delete('/:id', verifyToken, requireAdmin, categoryController.delete);

module.exports = router;
