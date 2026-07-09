const fs = require('fs');

const files = [
    'resources/views/admin/orders.blade.php',
    'resources/views/page_user/profileuser.blade.php'
];

files.forEach(file => {
    let c = fs.readFileSync(file, 'utf8');
    
    // orders.blade.php
    c = c.replace(/viewOrderDetail\(\$\{order\.ma_don_hang\}\)/g, "viewOrderDetail('${order.ma_don_hang}')");
    c = c.replace(/updateStatus\(\$\{order\.ma_don_hang\},/g, "updateStatus('${order.ma_don_hang}',");
    
    // profileuser.blade.php
    c = c.replace(/updateOrderStatus\(\$\{order\.ma_don_hang\},/g, "updateOrderStatus('${order.ma_don_hang}',");
    c = c.replace(/showOrderDetails\(\$\{order\.ma_don_hang\},/g, "showOrderDetails('${order.ma_don_hang}',");
    
    fs.writeFileSync(file, c);
    console.log('Fixed quotes in ' + file);
});
