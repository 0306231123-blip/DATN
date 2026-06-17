/**
 * Comprehensive Admin API Test Script
 * Tests all admin pages' backend APIs: Dashboard, Categories, Users, Products, Orders, Statistics
 */

const BASE_URL = 'http://localhost:3000/api';

const results = [];
let passCount = 0;
let failCount = 0;

async function testEndpoint(name, url, options = {}) {
  const method = options.method || 'GET';
  const body = options.body || null;
  const expectedStatus = options.expectedStatus || 200;
  
  try {
    const fetchOptions = {
      method,
      headers: { 'Content-Type': 'application/json' },
    };
    if (body) fetchOptions.body = JSON.stringify(body);
    
    const startTime = Date.now();
    const response = await fetch(url, fetchOptions);
    const elapsed = Date.now() - startTime;
    const data = await response.json();
    
    const statusOk = [expectedStatus].flat().includes(response.status);
    const dataOk = data.status === 'success' || data.success === true || (options.expectError && (data.status === 'error' || data.success === false));
    const passed = statusOk && dataOk;
    
    if (passed) {
      passCount++;
      console.log(`✅ PASS [${method}] ${name} (${elapsed}ms) - Status: ${response.status}`);
      if (data.data && Array.isArray(data.data)) {
        console.log(`   → Returned ${data.data.length} items`);
      }
      if (data.pagination) {
        console.log(`   → Pagination: total=${data.pagination.total}, page=${data.pagination.page}`);
      }
    } else {
      failCount++;
      console.log(`❌ FAIL [${method}] ${name} (${elapsed}ms) - Status: ${response.status}`);
      console.log(`   → Response:`, JSON.stringify(data).substring(0, 200));
    }
    
    results.push({ name, method, url, status: response.status, passed, elapsed, data });
    return { passed, data, status: response.status };
  } catch (error) {
    failCount++;
    console.log(`❌ FAIL [${method}] ${name} - Error: ${error.message}`);
    results.push({ name, method, url, passed: false, error: error.message });
    return { passed: false, error: error.message };
  }
}

async function runTests() {
  console.log('='.repeat(70));
  console.log('🧪 COSMETIC SHOP - ADMIN API TEST SUITE');
  console.log(`📅 ${new Date().toLocaleString()}`);
  console.log('='.repeat(70));

  // ==========================================
  // 1. DASHBOARD API
  // ==========================================
  console.log('\n📊 ── 1. DASHBOARD ──────────────────────────────────────');
  
  await testEndpoint('Dashboard Stats', `${BASE_URL}/dashboard/stats`);

  // ==========================================
  // 2. CATEGORIES API
  // ==========================================
  console.log('\n📁 ── 2. CATEGORIES ─────────────────────────────────────');
  
  await testEndpoint('Categories - List All', `${BASE_URL}/categories`);
  await testEndpoint('Categories - Stats', `${BASE_URL}/categories/stats`);
  await testEndpoint('Categories - Get By ID (1)', `${BASE_URL}/categories/1`);
  await testEndpoint('Categories - Get Non-existent (99999)', `${BASE_URL}/categories/99999`, { expectedStatus: 404, expectError: true });
  
  // Test Create
  const createCatResult = await testEndpoint('Categories - Create', `${BASE_URL}/categories`, {
    method: 'POST',
    body: { ten_danh_muc: `__TEST_CAT_${Date.now()}`, mo_ta: 'Test category', thu_tu_hien_thi: 999 },
    expectedStatus: [200, 201],
  });
  
  let testCatId = null;
  if (createCatResult.passed && createCatResult.data?.data) {
    testCatId = createCatResult.data.data.ma_danh_muc;
    console.log(`   → Created category ID: ${testCatId}`);
    
    // Test Update
    await testEndpoint('Categories - Update', `${BASE_URL}/categories/${testCatId}`, {
      method: 'PUT',
      body: { ten_danh_muc: `__TEST_CAT_UPDATED_${Date.now()}`, mo_ta: 'Updated test category' },
    });
    
    // Test Delete
    await testEndpoint('Categories - Delete', `${BASE_URL}/categories/${testCatId}`, {
      method: 'DELETE',
    });
  }

  // ==========================================
  // 3. USERS API
  // ==========================================
  console.log('\n👤 ── 3. USERS ──────────────────────────────────────────');
  
  await testEndpoint('Users - List All', `${BASE_URL}/users`);
  await testEndpoint('Users - Stats', `${BASE_URL}/users/stats`);
  await testEndpoint('Users - Stats (overview)', `${BASE_URL}/users/statistics/overview`);
  await testEndpoint('Users - Search', `${BASE_URL}/users?search=admin`);
  await testEndpoint('Users - Filter by Role', `${BASE_URL}/users?vai_tro=khach_hang`);
  await testEndpoint('Users - Pagination', `${BASE_URL}/users?page=1&limit=5`);
  await testEndpoint('Users - Get By ID (1)', `${BASE_URL}/users/1`);
  
  // Test Create User
  const createUserResult = await testEndpoint('Users - Create', `${BASE_URL}/users`, {
    method: 'POST',
    expectedStatus: [200, 201],
    body: {
      ho_ten: 'Test User API',
      email: `test_${Date.now()}@example.com`,
      password: 'Test@12345',
      so_dien_thoai: '0123456789',
      dia_chi: '123 Test Street',
      vai_tro: 'khach_hang',
    },
  });
  
  let testUserId = null;
  if (createUserResult.passed && createUserResult.data?.data) {
    testUserId = createUserResult.data.data.ma_nguoi_dung;
    console.log(`   → Created user ID: ${testUserId}`);
    
    // Test Update User
    await testEndpoint('Users - Update', `${BASE_URL}/users/${testUserId}`, {
      method: 'PUT',
      body: { ho_ten: 'Test User Updated', so_dien_thoai: '0987654321' },
    });
    
    // Test Delete User
    await testEndpoint('Users - Delete', `${BASE_URL}/users/${testUserId}`, {
      method: 'DELETE',
    });
  }

  // ==========================================
  // 4. PRODUCTS API
  // ==========================================
  console.log('\n🛍️ ── 4. PRODUCTS ──────────────────────────────────────');
  
  await testEndpoint('Products - List All', `${BASE_URL}/products`);
  await testEndpoint('Products - Stats', `${BASE_URL}/products/stats`);
  await testEndpoint('Products - Brands', `${BASE_URL}/products/brands`);
  await testEndpoint('Products - Search', `${BASE_URL}/products?search=son`);
  await testEndpoint('Products - Filter by Category', `${BASE_URL}/products?ma_danh_muc=1`);
  await testEndpoint('Products - Pagination', `${BASE_URL}/products?page=1&limit=5`);
  await testEndpoint('Products - Get By ID (1)', `${BASE_URL}/products/1`);

  // ==========================================
  // 5. ORDERS API
  // ==========================================
  console.log('\n📦 ── 5. ORDERS ─────────────────────────────────────────');
  
  await testEndpoint('Orders - List All', `${BASE_URL}/orders`);
  await testEndpoint('Orders - Stats', `${BASE_URL}/orders/stats`);
  await testEndpoint('Orders - Search', `${BASE_URL}/orders?search=test`);
  await testEndpoint('Orders - Filter by Status', `${BASE_URL}/orders?trang_thai_don=cho_xac_nhan`);
  await testEndpoint('Orders - Pagination', `${BASE_URL}/orders?page=1&limit=5`);
  await testEndpoint('Orders - Get By ID (1)', `${BASE_URL}/orders/1`);

  // ==========================================
  // 6. STATISTICS API
  // ==========================================
  console.log('\n📈 ── 6. STATISTICS ─────────────────────────────────────');
  
  await testEndpoint('Statistics - Overview', `${BASE_URL}/statistics/overview`);

  // ==========================================
  // SUMMARY
  // ==========================================
  console.log('\n' + '='.repeat(70));
  console.log('📋 TEST SUMMARY');
  console.log('='.repeat(70));
  console.log(`Total Tests : ${passCount + failCount}`);
  console.log(`✅ Passed   : ${passCount}`);
  console.log(`❌ Failed   : ${failCount}`);
  console.log(`Success Rate: ${((passCount / (passCount + failCount)) * 100).toFixed(1)}%`);
  console.log('='.repeat(70));
  
  // Detail failed tests
  const failedTests = results.filter(r => !r.passed);
  if (failedTests.length > 0) {
    console.log('\n🔴 FAILED TESTS DETAIL:');
    failedTests.forEach((t, i) => {
      console.log(`\n${i + 1}. ${t.name}`);
      console.log(`   URL: ${t.url}`);
      console.log(`   Error: ${t.error || `HTTP ${t.status}`}`);
      if (t.data) console.log(`   Response: ${JSON.stringify(t.data).substring(0, 300)}`);
    });
  }
}

runTests().catch(err => {
  console.error('Test suite crashed:', err);
  process.exit(1);
});
