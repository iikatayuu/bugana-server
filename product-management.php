<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css', '/css/product-management.css'];
$scripts = ['/js/dashboard.js', '/js/product-management.js'];
out_header('BUGANA Product Management', $styles, $scripts);

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
    <a href="/product-management.php" class="sidebar-link active">Product Management</a>

    <a href="/order-management.php" class="sidebar-link headadmin-btns d-none">Order Management</a>
    <a href="/customer-violation-reports.php" class="sidebar-link headadmin-btns d-none">Customer Violation Reports</a>

    <button type="button" class="sidebar-link sidebar-logout logout text-left">Sign Out</button>
  </div>

  <div class="dashboard-main">
    <header class="dashboard-header">
      <h5>Product Management</h5>
      <img src="" alt="Admin image" class="admin-pp mr-2" width="60" />
      <span class="admin-name"></span>
    </header>

    <div class="products-views mt-4 mx-3">
      <div class="flex-1">
        <select id="products-category-select" class="btn btn-sm btn-secondary text-md py-1">
          <option value="all" selected>Sort By: All</option>
          <option value="vegetable">Sort By: Vegetable</option>
          <option value="root-crops">Sort By: Root Crops</option>
          <option value="fruits">Sort By: Fruits</option>
        </select>
      </div>

      <a href="/product-new.php" class="btn btn-primary btn-round-sm" role="button">Add New Product</a>
    </div>

    <table class="dashboard-table">
      <thead>
        <tr>
          <th>Farmer Code</th>
          <th>Product Name</th>
          <th>Category</th>
          <th>Product Description</th>
          <th>Date added</th>
          <th>Last edited</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody id="products">
      </tbody>
    </table>

    <div class="d-flex flex-space-between mx-5 mb-2">
      <button type="button" class="btn btn-text" data-prev>
        <img src="/imgs/prev.svg" alt="Previous" />
      </button>
      <button type="button" class="btn btn-text" data-next>
        <img src="/imgs/next.svg" alt="Next" />
      </button>
    </div>

    <template id="temp-product">
      <tr>
        <td class="product-code"></td>
        <td class="product-name"></td>
        <td class="product-category"></td>
        <td class="product-description"></td>
        <td class="product-added"></td>
        <td class="product-edited"></td>
        <td class="product-actions">
          <a href="/product-edit.php?id=" class="btn btn-text product-action-edit" role="button">
            <img src="/imgs/edit.svg" alt="Edit button" />
          </a>
          <button type="button" class="btn btn-text product-action-archive">
            <img src="/imgs/archive.svg" alt="Archive button" />
          </button>
        </td>
      </tr>
    </template>
  </div>
</main>
<?php

out_footer();

?>
