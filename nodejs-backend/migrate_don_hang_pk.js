const sequelize = require('./config/database');

async function migrate() {
    try {
        console.log('Starting migration...');

        // 1. Drop foreign keys (ignore errors if already dropped)
        console.log('Dropping foreign keys...');
        await sequelize.query(`ALTER TABLE ai_goi_y_lich_su DROP FOREIGN KEY ai_goi_y_lich_su_ibfk_1`).catch(e => {});
        await sequelize.query(`ALTER TABLE chi_tiet_don_hang DROP FOREIGN KEY chi_tiet_don_hang_ibfk_141`).catch(e => {});
        await sequelize.query(`ALTER TABLE yeu_cau_tra_hang DROP FOREIGN KEY yeu_cau_tra_hang_ibfk_1`).catch(e => {});
        
        // Ensure temp columns exist
        console.log('Adding temp columns to child tables...');
        await sequelize.query(`ALTER TABLE chi_tiet_don_hang ADD COLUMN IF NOT EXISTS ma_don_hang_new VARCHAR(20)`).catch(e => {});
        await sequelize.query(`ALTER TABLE yeu_cau_tra_hang ADD COLUMN IF NOT EXISTS ma_don_hang_new VARCHAR(20)`).catch(e => {});
        await sequelize.query(`ALTER TABLE ai_goi_y_lich_su ADD COLUMN IF NOT EXISTS ma_don_hang_new VARCHAR(20)`).catch(e => {});
        await sequelize.query(`ALTER TABLE danh_gia ADD COLUMN IF NOT EXISTS ma_don_hang_new VARCHAR(20)`).catch(e => {});

        console.log('Updating child tables with ma_don_hang_custom...');
        await sequelize.query(`UPDATE chi_tiet_don_hang c JOIN don_hang d ON c.ma_don_hang = d.ma_don_hang SET c.ma_don_hang_new = d.ma_don_hang_custom`);
        await sequelize.query(`UPDATE yeu_cau_tra_hang y JOIN don_hang d ON y.ma_don_hang = d.ma_don_hang SET y.ma_don_hang_new = d.ma_don_hang_custom`);
        await sequelize.query(`UPDATE ai_goi_y_lich_su a JOIN don_hang d ON a.ma_don_hang = d.ma_don_hang SET a.ma_don_hang_new = d.ma_don_hang_custom`);
        await sequelize.query(`UPDATE danh_gia dg JOIN don_hang d ON dg.ma_don_hang = d.ma_don_hang SET dg.ma_don_hang_new = d.ma_don_hang_custom`);

        // Modify don_hang
        console.log('Modifying don_hang...');
        // Change auto_increment to normal int first so we can drop PK
        await sequelize.query(`ALTER TABLE don_hang MODIFY ma_don_hang INT NOT NULL`).catch(e => {});
        // Drop primary key
        await sequelize.query(`ALTER TABLE don_hang DROP PRIMARY KEY`).catch(e => {});
        
        await sequelize.query(`ALTER TABLE don_hang ADD COLUMN IF NOT EXISTS ma_don_hang_new VARCHAR(20)`).catch(e => {});
        await sequelize.query(`UPDATE don_hang SET ma_don_hang_new = ma_don_hang_custom`);

        // Now drop old columns and rename new columns
        console.log('Dropping old columns and renaming...');
        
        // chi_tiet_don_hang: PK is ma_chi_tiet, ma_don_hang is just a column.
        await sequelize.query(`ALTER TABLE chi_tiet_don_hang DROP COLUMN ma_don_hang`).catch(e => {});
        await sequelize.query(`ALTER TABLE chi_tiet_don_hang CHANGE ma_don_hang_new ma_don_hang VARCHAR(20) NOT NULL`).catch(e => {});

        // ai_goi_y_lich_su: primary key is ma_don_hang. Drop PK
        await sequelize.query(`ALTER TABLE ai_goi_y_lich_su DROP PRIMARY KEY`).catch(e => {});
        await sequelize.query(`ALTER TABLE ai_goi_y_lich_su DROP COLUMN ma_don_hang`).catch(e => {});
        await sequelize.query(`ALTER TABLE ai_goi_y_lich_su CHANGE ma_don_hang_new ma_don_hang VARCHAR(20) NOT NULL`).catch(e => {});
        await sequelize.query(`ALTER TABLE ai_goi_y_lich_su ADD PRIMARY KEY (ma_don_hang)`).catch(e => {});

        // yeu_cau_tra_hang: primary key is ma_yeu_cau (auto inc).
        await sequelize.query(`ALTER TABLE yeu_cau_tra_hang DROP COLUMN ma_don_hang`).catch(e => {});
        await sequelize.query(`ALTER TABLE yeu_cau_tra_hang CHANGE ma_don_hang_new ma_don_hang VARCHAR(20) NOT NULL`).catch(e => {});

        // danh_gia: unique key uk_danh_gia (ma_don_hang, ma_san_pham). Drop unique key, rename, add unique key
        await sequelize.query(`ALTER TABLE danh_gia DROP INDEX uk_danh_gia`).catch(e => {});
        await sequelize.query(`ALTER TABLE danh_gia DROP COLUMN ma_don_hang`).catch(e => {});
        await sequelize.query(`ALTER TABLE danh_gia CHANGE ma_don_hang_new ma_don_hang VARCHAR(20)`).catch(e => {});
        await sequelize.query(`ALTER TABLE danh_gia ADD UNIQUE INDEX uk_danh_gia (ma_don_hang, ma_san_pham)`).catch(e => {});

        // don_hang:
        await sequelize.query(`ALTER TABLE don_hang DROP COLUMN ma_don_hang`).catch(e => {});
        await sequelize.query(`ALTER TABLE don_hang DROP COLUMN ma_don_hang_custom`).catch(e => {});
        await sequelize.query(`ALTER TABLE don_hang CHANGE ma_don_hang_new ma_don_hang VARCHAR(20) NOT NULL`).catch(e => {});
        await sequelize.query(`ALTER TABLE don_hang ADD PRIMARY KEY (ma_don_hang)`).catch(e => {});

        // 3. Re-add foreign keys
        console.log('Re-adding foreign keys...');
        await sequelize.query(`ALTER TABLE ai_goi_y_lich_su ADD CONSTRAINT ai_goi_y_lich_su_ibfk_1 FOREIGN KEY (ma_don_hang) REFERENCES don_hang(ma_don_hang)`).catch(e => console.log('fk err 1', e.message));
        await sequelize.query(`ALTER TABLE chi_tiet_don_hang ADD CONSTRAINT chi_tiet_don_hang_ibfk_141 FOREIGN KEY (ma_don_hang) REFERENCES don_hang(ma_don_hang)`).catch(e => console.log('fk err 2', e.message));
        await sequelize.query(`ALTER TABLE yeu_cau_tra_hang ADD CONSTRAINT yeu_cau_tra_hang_ibfk_1 FOREIGN KEY (ma_don_hang) REFERENCES don_hang(ma_don_hang)`).catch(e => console.log('fk err 3', e.message));
        
        console.log('Migration successful!');
    } catch (e) {
        console.error('Migration failed:', e);
    } finally {
        process.exit();
    }
}

migrate();
