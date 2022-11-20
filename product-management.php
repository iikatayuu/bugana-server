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

    <div class="d-flex flex-align-center mt-4 mx-3">
      <div class="mr-2">
        <input type="text" id="user-search" name="q" placeholder="Search Farmer Code" class="form-control form-control-white box-shadow table-search" />
      </div>

      <div class="flex-1">
        <select id="products-category-select" class="btn btn-sm btn-tertiary text-md py-1">
          <option value="all" selected>Sort By: All</option>
          <option value="vegetable">Sort By: Vegetable</option>
          <option value="root-crops">Sort By: Root Crops</option>
          <option value="fruits">Sort By: Fruits</option>
        </select>
      </div>

      <a href="/product-new.php" class="btn btn-primary btn-round-sm admin-btns d-none" role="button">Add New Product</a>
    </div>

    <table class="dashboard-table">
      <thead>
        <tr>
          <th>Farmer Code</th>
          <th>
            <div class="d-flex flex-align-center flex-center">
              <span class="mr-1">Product Name</span>
              <div class="dropdown">
                <button type="button" class="btn btn-text" data-dropdown="toggle">
                  <img src="/imgs/down.svg" alt="Sort by product name" width="12" />
                </button>
                <div class="dropdown-content">
                  <button type="button" class="btn btn-text btn-block text-md py-1" id="sort-product-ascending">Sort: A-Z</button>
                  <button type="button" class="btn btn-text btn-block text-md py-1" id="sort-product-descending">Sort: Z-A</button>
                </div>
              </div>
            </div>
          </th>
          <th>Category</th>
          <th>Product Description</th>
          <th>
            <div class="d-flex flex-align-center flex-center">
              <span class="mr-1">Price</span>
              <div class="dropdown">
                <button type="button" class="btn btn-text" data-dropdown="toggle">
                  <img src="/imgs/down.svg" alt="Sort by product name" width="12" />
                </button>
                <div class="dropdown-content">
                  <button type="button" class="btn btn-text btn-block text-md py-1" id="sort-price-descending">Sort: Highest</button>
                  <button type="button" class="btn btn-text btn-block text-md py-1" id="sort-price-ascending">Sort: Lowest</button>
                </div>
              </div>
            </div>
          </th>
          <th>Date added</th>
          <th>Last edited</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody id="products"></tbody>
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

  <template id="temp-page-btn">
    <button type="button" class="btn btn-background-secondary btn-round-sm btn-sm mr-2" data-page=""></button>
  </template>

  <template id="temp-product">
    <tr>
      <td class="product-code"></td>
      <td class="product-name"></td>
      <td class="product-category"></td>
      <td class="product-description"></td>
      <td class="product-price"></td>
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
</main>
<?php

out_footer();

?>
