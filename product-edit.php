<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css', '/css/product-edit.css'];
$scripts = ['/js/dashboard.js', '/js/product-edit.js'];
out_header('BUGANA Edit Product', $styles, $scripts);

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

    <div class="my-4 mx-3">
      <h1 class="dashboard-title m-3">Product Information</h1>

      <form action="/api/admin/product/edit.php" method="post" id="form-product-edit">
        <div class="card card-secondary card-rect p-3">
          <div class="d-flex mb-2">
            <div class="form-group flex-1 m-1">
              <label for="product-name">Edit Product Name:</label>
              <input type="text" id="product-name" name="name" placeholder="Product Name" class="form-control mt-2" required />
            </div>

            <div class="form-group flex-1 m-1">
              <label for="product-category">Edit Product Category:</label>
              <select id="product-category" name="category" class="form-control mt-2" required>
                <option value="vegetable">Vegetable</option>
                <option value="root-crops">Root Crops</option>
                <option value="fruits">Fruits</option>
              </select>
            </div>
          </div>

          <div class="form-group mb-2">
            <label for="file-product-photos">Edit Product Photo</label>
            <div id="product-photos" class="d-flex my-2"></div>
          </div>
          <input type="file" id="file-product-photos" class="mb-2" multiple />

          <div class="d-flex">
            <div class="form-group flex-1 m-1">
              <label for="product-description">Edit Product Description:</label>
              <textarea id="product-description" name="description" class="form-control mt-2" rows="7" required></textarea>
            </div>

            <div class="form-group flex-1 m-1">
              <label for="product-price">Change Price:</label>
              <input type="text" id="product-price" name="price" placeholder="Price" class="form-control mt-2" required />
            </div>
          </div>

          <div id="form-product-edit-error" class="text-danger mt-2"></div>
        </div>

        <div class="d-flex flex-space-around my-2">
          <button type="submit" class="btn btn-lg btn-primary" disabled>SAVE CHANGES</button>
          <a href="/product-management.php" class="btn btn-lg btn-primary" role="button">CANCEL</a>
        </div>
      </form>
    </div>
  </div>
</main>
<?php

out_footer();

?>
