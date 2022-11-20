<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css', '/css/stats.css'];
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
      <div class="card card-tertiary text-center">
        <div class="mb-2">TOTAL PRODUCTS SOLD</div>
        <div class="total-products-sold">0</div>
      </div>

      <div class="card card-tertiary text-center">
        <div class="mb-2">TOTAL ORDERS</div>
        <div class="total-orders">0</div>
      </div>

      <div class="card card-tertiary text-center">
        <div class="mb-2">TOTAL USERS</div>
        <div class="total-users">0</div>
      </div>

      <div class="card card-tertiary text-center">
        <div class="mb-2">TOTAL UNSOLD PRODUCTS</div>
        <div class="total-products-unsold">0</div>
      </div>
    </div>

    <div class="d-flex flex-align-start px-3 mt-5">
      <div class="flex-1 pr-3">
        <div class="dashboard-statistics d-flex">
          <h6 class="dashboard-title flex-1 mb-1">Statistics</h6>
          <button type="button" class="btn btn-text text-sm active" data-graph="weekly">Weekly</button>
          <button type="button" class="btn btn-text text-sm" data-graph="monthly">Monthly</button>
          <button type="button" class="btn btn-text text-sm" data-graph="yearly">Yearly</button>
        </div>

        <div>
          <canvas id="graph" width="600" height="300"></canvas>
        </div>
      </div>

      <div class="new-customers card card-background-secondary card-rect p-0">
        <header class="card-title card-title-tertiary text-lg">New Customers</header>
        <div id="users-new" class="px-2 pt-2"></div>
      </div>
    </div>

    <div class="card card-background-secondary card-rect p-0 m-3">
      <header class="card-title card-title-tertiary">Farmer Top Sales</header>
      <div class="px-2 pt-2"></div>
    </div>

    <template id="temp-user-new">
      <div class="d-flex flex-align-center mb-1">
        <img src="" alt="" width="32" class="user-img mr-2" />
        <span class="user-name"></span>
      </div>
    </template>
  </div>
</main>
<?php

out_footer();

?>
