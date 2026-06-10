const express = require('express');
const categoryController = require('../controllers/CategoryController');

const router = express.Router();

router.get('/', categoryController.getAll);
router.get('/stats', categoryController.getStats);
router.get('/:id', categoryController.getById);
router.post('/', categoryController.create);
router.put('/:id', categoryController.update);
router.delete('/:id', categoryController.delete);

module.exports = router;
