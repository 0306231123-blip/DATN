const fs = require('fs');
const glob = require('glob');
const pattern = 'resources/views/**/*.blade.php';

glob(pattern, (err, files) => {
    files.forEach(file => {
        let c = fs.readFileSync(file, 'utf8');
        
        let n = c;
        // order.ma_don_hang_custom || '#' + order.ma_don_hang
        n = n.replace(/order\.ma_don_hang_custom\s*\|\|\s*'\#'\s*\+\s*order\.ma_don_hang/g, "order.ma_don_hang");
        // order.ma_don_hang_custom || '#DH' + String(order.ma_don_hang).padStart(4, '0')
        n = n.replace(/order\.ma_don_hang_custom\s*\|\|\s*'#DH'\s*\+\s*String\(order\.ma_don_hang\)\.padStart\(4,\s*'0'\)/g, "order.ma_don_hang");
        // !empty($dh['ma_don_hang_custom']) ? '#' . $dh['ma_don_hang_custom'] : '#DH' . str_pad($dh['ma_don_hang'] ?? 0, 4, '0', STR_PAD_LEFT)
        n = n.replace(/!\s*empty\(\$dh\['ma_don_hang_custom'\]\)\s*\?\s*'#'\s*\.\s*\$dh\['ma_don_hang_custom'\]\s*:\s*'#DH'\s*\.\s*str_pad\(\$dh\['ma_don_hang'\]\s*\?\?\s*0,\s*4,\s*'0',\s*STR_PAD_LEFT\)/g, "$dh['ma_don_hang']");
        
        if (c !== n) {
            fs.writeFileSync(file, n);
            console.log('Updated ' + file);
        }
    });
});
