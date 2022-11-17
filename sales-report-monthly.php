<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css', '/css/sales-report.css'];
$scripts = ['/js/dashboard.js', '/js/sales-report-monthly.js'];
out_header('BUGANA Sales Monthly Report', $styles, $scripts);

?>
<main>
  <div class="sidebar">
    <div class="sidebar-logo mb-5">
      <img src="/imgs/logo-inverse.png" alt="BUGANA Logo" width="64" />
      <span class="sidebar-title">BUGANA</span>
    </div>

    <a href="/dashboard.php" class="sidebar-link">Dashboard</a>
    <a href="/inventory.php" class="sidebar-link">Inventory</a>
    <a href="/sales-report.php" class="sidebar-link active">Sales Report</a>
    <nav class="sales-report">
      <a href="/sales-report.php">Weekly</a>
      <a href="/sales-report-monthly.php" class="active">Monthly</a>
      <a href="/sales-report-farmers.php">Farmers</a>
    </nav>
    <a href="/user-management.php" class="sidebar-link">User Management</a>
    <a href="/product-management.php" class="sidebar-link">Product Management</a>

    <a href="/order-management.php" class="sidebar-link headadmin-btns d-none">Order Management</a>
    <a href="/customer-violation-reports.php" class="sidebar-link headadmin-btns d-none">Customer Violation Reports</a>

    <button type="button" class="sidebar-link sidebar-logout logout text-left">Sign Out</button>
  </div>

  <div class="dashboard-main">
    <header class="dashboard-header">
      <h5>SALES REPORT</h5>
      <img src="" alt="Admin image" class="admin-pp mr-2" width="60" />
      <span class="admin-name"></span>
    </header>

    <div class="d-flex">
      <h6 class="dashboard-title flex-1 my-2 mx-3">This Monthly Sales</h6>
    </div>

    <table class="dashboard-table dashboard-table-borderless">
      <thead>
        <tr>
          <th>Product Name</th>
          <th>Product Price</th>
          <th>Quantity Sold</th>
          <th>Product Revenue</th>
        </tr>
      </thead>

      <tbody id="sales">
      </tbody>
    </table>

    <template id="temp-sale">
      <tr>
        <td class="product-name"></td>
        <td class="product-price"></td>
        <td class="quantity-sold"></td>
        <td class="product-revenue"></td>
      </tr>
    </template>

    <template id="temp-total">
      <tr class="sales-report-total">
        <td colspan="2"></td>
        <td>TOTAL</td>
        <td class="sales-report-total-amount"></td>
      </tr>
    </template>
  </div>
</main>
<?php

out_footer();

?>
