<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css', '/css/sales-report.css'];
$scripts = ['/js/dashboard.js', '/js/sales-report-farmers.js'];
out_header('BUGANA Individual Farmers Sales Report', $styles, $scripts);

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
      <a href="/sales-report-monthly.php">Monthly</a>
      <a href="/sales-report-farmers.php" class="active">Farmers</a>
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

    <h6 class="dashboard-title flex-1 my-2 mx-3">Individual Farmers Sales Report</h6>

    <div class="d-flex flex-space-between flex-align-center mx-5">
      <div>
        <input type="text" id="farmer-search" name="q" placeholder="Search Farmer Code" class="form-control form-control-white box-shadow table-search" />
      </div>

      <div class="pagination d-flex flex-align-center flex-center">
        <button type="button" class="btn btn-text mr-2" data-page="" data-prev>
          <img src="/imgs/prev.svg" alt="Previous" width="18" />
        </button>

        <button type="button" class="btn btn-background-secondary btn-round-sm btn-xs mr-2" data-page="1">1</button>
        <div class="pages d-content"></div>

        <button type="button" class="btn btn-text mr-2" data-page="" data-next>
          <img src="/imgs/next.svg" alt="Next" width="18" />
        </button>
      </div>
    </div>

    <table class="dashboard-table dashboard-table-borderless">
      <thead>
        <tr>
          <th>Farmer Code</th>
          <th>
            <div class="d-flex flex-align-center flex-center">
              <span class="mr-1">Farmer Name</span>
              <div class="dropdown">
                <button type="button" class="btn btn-text" data-dropdown="toggle">
                  <img src="/imgs/down.svg" alt="Sort by farmer name" width="12" />
                </button>
                <div class="dropdown-content">
                  <button type="button" class="btn btn-text btn-block text-md py-1" id="sort-farmer-ascending">Sort: A-Z</button>
                  <button type="button" class="btn btn-text btn-block text-md py-1" id="sort-farmer-descending">Sort: Z-A</button>
                </div>
              </div>
            </div>
          </th>
          <th>
            <div class="d-flex flex-align-center flex-center">
              <span class="mr-1">Total Sales</span>
              <div class="dropdown">
                <button type="button" class="btn btn-text" data-dropdown="toggle">
                  <img src="/imgs/down.svg" alt="Sort by sales" width="12" />
                </button>
                <div class="dropdown-content">
                  <button type="button" class="btn btn-text btn-block text-md py-1" id="sort-sales-descending">Sort: Highest</button>
                  <button type="button" class="btn btn-text btn-block text-md py-1" id="sort-sales-ascending">Sort: Lowest</button>
                </div>
              </div>
            </th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody id="farmers">
      </tbody>
    </table>

    <div class="d-flex flex-space-between flex-align-center mx-5">
      <div class="pagination d-flex flex-align-center flex-center ml-auto">
        <button type="button" class="btn btn-text mr-2" data-page="" data-prev>
          <img src="/imgs/prev.svg" alt="Previous" width="18" />
        </button>

        <button type="button" class="btn btn-background-secondary btn-round-sm btn-xs mr-2" data-page="1">1</button>
        <div class="pages d-content"></div>

        <button type="button" class="btn btn-text mr-2" data-page="" data-next>
          <img src="/imgs/next.svg" alt="Next" width="18" />
        </button>
      </div>
    </div>
  </div>

  <template id="temp-page-btn">
    <button type="button" class="btn btn-background-secondary btn-round-sm btn-xs mr-2" data-page=""></button>
  </template>

  <template id="temp-farmer">
    <tr>
      <td class="farmer-code"></td>
      <td class="farmer-name"></td>
      <td class="total-sales"></td>
      <td class="action">
        <a href="#">View Details</a>
      </td>
    </tr>
  </template>
</main>
<?php

out_footer();

?>
