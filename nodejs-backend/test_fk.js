const sequelize = require('./config/database');
async function run() {
  const [results] = await sequelize.query("DESCRIBE ai_goi_y_lich_su;");
  console.log(JSON.stringify(results, null, 2));
  process.exit(0);
}
run();
