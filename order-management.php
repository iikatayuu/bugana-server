<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css', '/css/order-management.css'];
$scripts = ['/js/dashboard.js', '/js/order-management.js'];
out_header('BUGANA Order Management', $styles, $scripts);

$sort = !empty($_GET['sort']) ? $_GET['sort'] : 'all';

?>
<main>
  <div class="sidebar">
    <div class="sidebar-logo mb-5">
      <img src="/imgs/logo-inverse.png" alt="BUGANA Logo" width="64" />
      <span class="sidebar-title">BUGANA</span>
    </div>

    <a href="/dashboard.php" class="sidebar-link">Dashboard</a>
    <a href="/inventory.php" class="sidebar-link">Inventory</a>
    <a href="/sales-report.php" class="sidebar-link">Sales Report</a>
    <a href="/user-management.php" class="sidebar-link">User Management</a>
    <a href="/product-management.php" class="sidebar-link">Product Management</a>

    <a href="/order-management.php" class="sidebar-link headadmin-btns active d-none">Order Management</a>
    <a href="/customer-violation-reports.php" class="sidebar-link headadmin-btns d-none">Customer Violation Reports</a>

    <button type="button" class="sidebar-link sidebar-logout logout text-left">Sign Out</button>
  </div>

  <div class="dashboard-main">
    <header class="dashboard-header">
      <h5>Order Management</h5>
      <img src="" alt="Admin image" class="admin-pp mr-2" width="60" />
      <span class="admin-name"></span>
    </header>

    <div class="d-flex flex-align-center mt-4 mx-3">
      <div class="flex-1">
        <input type="text" id="transaction-search" name="q" placeholder="Search Transaction ID" class="form-control form-control-white box-shadow table-search" />
      </div>

      <div>
        <select id="transactions-category-select" class="btn btn-sm btn-tertiary text-md py-1">
          <option value="all" selected>Sort By: All</option>
          <option value="success">Sort By: Done</option>
          <option value="pending">Sort By: Undone</option>
        </select>
      </div>
    </div>

    <table class="dashboard-table">
      <thead>
        <tr>
          <th>Transaction ID</th>
          <th>Transaction Date</th>
          <th>Customer Code</th>
          <th>Address</th>
          <th>Total Amount</th>
          <th>Order Type</th>
          <th>Order Status</th>
        </tr>
      </thead>

      <tbody id="transactions"></tbody>
    </table>

    <div class="d-flex flex-space-between flex-align-center mx-5 mb-2">
      <div>
        <span>Page Limit: </span>
        <input type="number" id="limit-page" value="10" />
      </div>

      <div class="pagination d-flex flex-align-center flex-center">
        <button type="button" class="btn btn-text mr-2" data-page="" data-prev>
          <img src="/imgs/prev.svg" alt="Previous" width="18" />
        </button>

        <button type="button" class="btn btn-background-secondary btn-round-sm btn-sm mr-2" data-page="1">1</button>
        <div id="pages" class="d-content"></div>

        <button type="button" class="btn btn-text mr-2" data-page="" data-next>
          <img src="/imgs/next.svg" alt="Next" width="18" />
        </button>
      </div>
    </div>
  </div>

  <div class="modal-container d-none"></div>
  <div id="modal-order" class="modal">
    <div class="card card-background p-4">
      <h3 class="card-title mb-2 text-center">ORDER DETAILS</h3>
      <table class="table-details">
        <tr>
          <td>Customer Name:</td>
          <td id="order-customer-name"></td>
        </tr>

        <tr>
          <td>Transaction ID:</td>
          <td id="transaction-id"></td>
        </tr>

        <tr>
          <td>Transaction Date:</td>
          <td id="transaction-date"></td>
        </tr>

        <tr>
          <td>Customer Code:</td>
          <td id="order-customer-code"></td>
        </tr>

        <tr>
          <td>Address:</td>
          <td id="order-customer-address"></td>
        </tr>

        <tr>
          <td>Order Type:</td>
          <td id="order-type"></td>
        </tr>
      </table>

      <table class="table-products">
        <thead>
          <th class="px-2 text-center">Farmer Code</th>
          <th class="px-2 text-center">Product Name</th>
          <th class="px-2 text-center">Quantity</th>
          <th class="px-2 text-center">Price</th>
          <th class="px-2 text-center">Total Amount</th>
        </thead>

        <tbody id="orders"></tbody>
      </table>

      <div class="text-center">
        <button type="button" class="btn btn-primary px-5" data-modal="#modal-order">OK</button>
      </div>
    </div>
  </div>

  <template id="temp-page-btn">
    <button type="button" class="btn btn-background-secondary btn-round-sm btn-sm mr-2" data-page=""></button>
  </template>

  <template id="temp-transaction">
    <tr class="transaction-action">
      <td class="transaction-id"></td>
      <td class="transaction-date"></td>
      <td class="customer-code"></td>
      <td class="customer-address"></td>
      <td class="total-amount"></td>
      <td class="order-type"></td>
      <td>
        <img class="order-status" alt="" width="18" />
      </td>
    </tr>
  </template>

  <template id="temp-transaction-details">
    <tr>
      <td class="farmer-code text-center"></td>
      <td class="product-name text-center"></td>
      <td class="order-quantity text-center"></td>
      <td class="order-price text-center"></td>
      <td class="order-amount text-center"></td>
    </tr>
  </template>

  <template id="temp-details-total">
    <tr>
      <td colspan="2" class="text-center"></td>
      <td colspan="2" class="total-name text-right"></td>
      <td class="total-value text-center text-bold"></td>
    </tr>
  </template>
</main>
<?php

out_footer();

?>
