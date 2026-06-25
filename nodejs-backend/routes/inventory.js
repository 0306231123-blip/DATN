const express = require('express');
const router = express.Router();
const inventoryController = require('../controllers/InventoryController');

router.get('/logs', inventoryController.getInventoryLogs);
router.post('/import', inventoryController.importInventory);
router.post('/export', inventoryController.exportInventory);

module.exports = router;
