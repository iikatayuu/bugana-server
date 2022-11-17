<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css'];
$scripts = ['/js/dashboard.js', '/js/stats.js'];
out_header('BUGANA Admin Dashboard', $styles, $scripts);

?>
<main>
  <div class="sidebar">
    <div class="sidebar-logo mb-5">
      <img src="/imgs/logo-inverse.png" alt="BUGANA Logo" width="64" />
      <span class="sidebar-title">BUGANA</span>
    </div>

    <a href="/dashboard.php" class="sidebar-link active">Dashboard</a>
    <a href="/inventory.php" class="sidebar-link">Inventory</a>
    <a href="/sales-report.php" class="sidebar-link">Sales Report</a>
    <a href="/user-management.php" class="sidebar-link">User Management</a>
    <a href="/product-management.php" class="sidebar-link">Product Management</a>

    <a href="/order-management.php" class="sidebar-link headadmin-btns d-none">Order Management</a>
    <a href="/customer-violation-reports.php" class="sidebar-link headadmin-btns d-none">Customer Violation Reports</a>

    <button type="button" class="sidebar-link sidebar-logout logout text-left">Sign Out</button>
  </div>

  <div class="dashboard-main">
    <header class="dashboard-header">
      <h5>DASHBOARD</h5>
      <img src="" alt="Admin image" class="admin-pp mr-2" width="60" />
      <span class="admin-name"></span>
    </header>

    <h6 class="dashboard-title my-2 mx-3">Overview</h6>
    <div class="dashboard-cards">
      <div class="card card-secondary text-center">
        <div class="mb-2">TOTAL PRODUCTS SOLD</div>
        <div class="total-products-sold">0</div>
      </div>

      <div class="card card-secondary text-center">
        <div class="mb-2">TOTAL ORDERS</div>
        <div class="total-orders">0</div>
      </div>

      <div class="card card-secondary text-center">
        <div class="mb-2">TOTAL USERS</div>
        <div class="total-users">0</div>
      </div>
    </div>

    <h6 class="dashboard-title mt-5 mb-2 mx-3">Weekly Statistics</h6>
    <div className="p-5">
      <canvas id="graph-weekly" width="1400" height="500" class="mx-auto"></canvas>
    </div>
  </div>
</main>
<?php

out_footer();

?>
