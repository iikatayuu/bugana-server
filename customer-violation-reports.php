<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css', '/css/customer-violation-reports.css'];
$scripts = ['/js/dashboard.js', '/js/customer-violation-reports.js'];
out_header('BUGANA Customer Violation Reports', $styles, $scripts);

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

    <a href="/order-management.php" class="sidebar-link headadmin-btns d-none">Order Management</a>
    <a href="/customer-violation-reports.php" class="sidebar-link headadmin-btns active d-none">Customer Violation Reports</a>

    <button type="button" class="sidebar-link sidebar-logout logout text-left">Sign Out</button>
  </div>

  <div class="dashboard-main">
    <header class="dashboard-header">
      <h5>Customer Violation Reports</h5>
      <img src="" alt="Admin image" class="admin-pp mr-2" width="60" />
      <span class="admin-name"></span>
    </header>

    <div class="d-flex my-2 mx-3">
      <div class="ml-auto">
        <button type="button" class="btn btn-primary btn-sm" data-modal="#modal-add">Add Violation</button>
      </div>
    </div>

    <table class="dashboard-table">
      <thead>
        <th>CODE</th>
        <th>Full Name</th>
        <th>Address</th>
        <th>Contact Number</th>
        <th>Email Address</th>
        <th>Transaction ID</th>
        <th>Counts</th>
        <th>Actions</th>
      </thead>

      <tbody id="users"></tbody>
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
  <div id="modal-add" class="modal">
    <form action="/api/admin/violations/add.php" method="post" id="form-add" class="card card-background px-4 py-3">
      <h3 class="text-center">Violation Report</h3>

      <div class="d-flex my-5">
        <label for="transaction-id" class="mr-2">Transaction ID:</label>
        <input type="text" id="transaction-id" name="transactionid" />
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-primary px-5 text-bold">Add Violation</button>
      </div>
    </form>
  </div>

  <template id="temp-page-btn">
    <button type="button" class="btn btn-background-secondary btn-round-sm btn-sm mr-2" data-page=""></button>
  </template>

  <template id="temp-user">
    <tr>
      <td class="user-code"></td>
      <td class="user-name"></td>
      <td class="user-address"></td>
      <td class="user-contact"></td>
      <td class="user-email"></td>
      <td class="user-transaction"></td>
      <td class="counts"></td>
      <td>
        <button type="button" class="user-actions">BAN</button>
      </td>
    </tr>
  </template>
</main>
<?php

out_footer();

?>
