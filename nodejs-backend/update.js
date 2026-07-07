const { Op } = require('sequelize');
const DonHang = require('./models/DonHang');
const sequelize = require('./config/database');

async function run() {
    try {
        const orders = await DonHang.findAll({ order: [['ngay_dat', 'ASC']] });
        let dateCount = {};
        for (let order of orders) {
            let d = new Date(order.ngay_dat);
            let yearStr = String(d.getFullYear()).slice(-2);
            let dayStr = String(d.getDate()).padStart(2, '0') + String(d.getMonth() + 1).padStart(2, '0') + yearStr;
            if (!dateCount[dayStr]) dateCount[dayStr] = 0;
            dateCount[dayStr]++;
            let customCode = `${dayStr}#${String(dateCount[dayStr]).padStart(2, '0')}`;
            await order.update({ ma_don_hang_custom: customCode });
        }
        console.log('Successfully updated existing orders');
    } catch (e) {
        console.error(e);
    } finally {
        process.exit();
    }
}
run();
