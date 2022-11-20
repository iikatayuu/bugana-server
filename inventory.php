<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css', '/css/inventory.css'];
$scripts = ['/js/dashboard.js', '/js/inventory.js'];
out_header('BUGANA Inventory', $styles, $scripts);

?>
<main>
  <div class="sidebar">
    <div class="sidebar-logo mb-5">
      <img src="/imgs/logo-inverse.png" alt="BUGANA Logo" width="64" />
      <span class="sidebar-title">BUGANA</span>
    </div>

    <a href="/dashboard.php" class="sidebar-link">Dashboard</a>
    <a href="/inventory.php" class="sidebar-link active">Inventory</a>
    <a href="/sales-report.php" class="sidebar-link">Sales Report</a>
    <a href="/user-management.php" class="sidebar-link">User Management</a>
    <a href="/product-management.php" class="sidebar-link">Product Management</a>

    <a href="/order-management.php" class="sidebar-link headadmin-btns d-none">Order Management</a>
    <a href="/customer-violation-reports.php" class="sidebar-link headadmin-btns d-none">Customer Violation Reports</a>

    <button type="button" class="sidebar-link sidebar-logout logout text-left">Sign Out</button>
  </div>

  <div class="dashboard-main">
    <header class="dashboard-header">
      <h5>INVENTORY</h5>
      <img src="" alt="Admin image" class="admin-pp mr-2" width="60" />
      <span class="admin-name"></span>
    </header>

    <div class="d-flex flex-space-between flex-align-center mt-4 mx-3">
      <div>
        <input type="text" id="farmer-search" name="q" placeholder="Search Farmer Code" class="form-control form-control-white box-shadow table-search" />
      </div>

      <div>
        <select id="products-category-select" class="btn btn-sm btn-tertiary text-md py-1">
          <option value="all" selected>Sort By: All</option>
          <option value="vegetable">Sort By: Vegetable</option>
          <option value="root-crops">Sort By: Root Crops</option>
          <option value="fruits">Sort By: Fruits</option>
        </select>
      </div>
    </div>

    <table class="dashboard-table">
      <thead>
        <tr>
          <th>Farmer Code</th>
          <th>Category</th>
          <th>Product Name</th>
          <th>Stock In</th>
          <th>Stock In Date</th>
          <th>Stock Out</th>
          <th>Stock Out Date</th>
          <th>Stock Quantity</th>
        </tr>
      </thead>

      <tbody id="inventory"></tbody>
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
  <div id="modal-stock-in" class="modal">
    <div class="card card-rect text-center p-4">
      <h3 class="card-title mb-2 text-center">Stock In History</h3>
      <table class="dashboard-table">
        <thead>
          <tr>
            <th class="text-sm">Date</th>
            <th class="text-sm">Quantity (KG)</th>
          </tr>
        </thead>

        <tbody id="table-stock-in"></tbody>
      </table>

      <button type="button" class="btn btn-primary btn-sm" data-modal="#modal-stock-add">Add Stock</button>
    </div>
  </div>

  <div id="modal-stock-out" class="modal">
    <div class="card card-rect p-4">
      <h3 class="card-title mb-2 text-center">Stock Out History</h3>
      <table class="dashboard-table">
        <thead>
          <tr>
            <th class="text-sm">Date</th>
            <th class="text-sm">Quantity (KG)</th>
          </tr>
        </thead>

        <tbody id="table-stock-out"></tbody>
      </table>
    </div>
  </div>

  <div id="modal-stock-add" class="modal">
    <form action="/api/admin/stock/add.php" method="post" id="form-stock-add" class="card card-rect card-tertiary text-center p-3">
      <h3 class="mb-2 text-center">QUANTITY</h3>
      <div class="form-group">
        <input type="number" id="stock-add-quantity" name="quantity" placeholder="Quantity" class="form-control" required />
      </div>
      <div id="form-stock-add-error" class="text-danger"></div>
      <button type="submit" class="btn btn-primary btn-sm">CONFIRM</button>
    </form>
  </div>

  <template id="temp-page-btn">
    <button type="button" class="btn btn-background-secondary btn-round-sm btn-sm mr-2" data-page=""></button>
  </template>

  <template id="temp-item">
    <tr>
      <td class="item-farmer-code"></td>
      <td class="item-category"></td>
      <td class="item-product-name"></td>
      <td class="item-stock-in"></td>
      <td class="item-stock-in-date"></td>
      <td class="item-stock-out"></td>
      <td class="item-stock-out-date"></td>
      <td class="item-stock"></td>
    </tr>
  </template>

  <template id="temp-stock">
    <tr>
      <td class="stock-date"></td>
      <td class="stock-quantity"></td>
    </tr>
  </template>
</main>
<?php

out_footer();

?>
