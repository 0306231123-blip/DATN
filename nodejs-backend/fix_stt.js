const sequelize = require('./config/database');

async function run() {
  try {
    await sequelize.query('ALTER TABLE don_hang DROP INDEX stt').catch(() => {});
    await sequelize.query('ALTER TABLE don_hang MODIFY stt INT');
    await sequelize.query(`
      UPDATE don_hang d
      JOIN (
        SELECT ma_don_hang, ROW_NUMBER() OVER (ORDER BY ngay_dat ASC) as r_num
        FROM don_hang
      ) as ranked ON d.ma_don_hang = ranked.ma_don_hang
      SET d.stt = ranked.r_num
    `);
    await sequelize.query('ALTER TABLE don_hang MODIFY stt INT AUTO_INCREMENT UNIQUE');
    const [results] = await sequelize.query('SELECT stt, ma_don_hang, ngay_dat FROM don_hang ORDER BY stt ASC LIMIT 5');
    console.log(results);
  } catch (e) {
    console.error(e);
  } finally {
    process.exit(0);
  }
}

run();
