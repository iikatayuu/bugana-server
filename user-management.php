<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css', '/css/user-management.css'];
$scripts = ['/js/dashboard.js', '/js/user-management.js'];
out_header('BUGANA User Management', $styles, $scripts);

$view = !empty($_GET['view']) ? $_GET['view'] : 'all';

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
    <a href="/user-management.php" class="sidebar-link active">User Management</a>
    <a href="/product-management.php" class="sidebar-link">Product Management</a>

    <a href="/order-management.php" class="sidebar-link headadmin-btns d-none">Order Management</a>
    <a href="/customer-violation-reports.php" class="sidebar-link headadmin-btns d-none">Customer Violation Reports</a>

    <button type="button" class="sidebar-link sidebar-logout logout text-left">Sign Out</button>
  </div>

  <div class="dashboard-main">
    <header class="dashboard-header">
      <h5>User Management</h5>
      <img src="" alt="Admin image" class="admin-pp mr-2" width="60" />
      <span class="admin-name"></span>
    </header>

    <div class="users-views mt-4 mx-2">
      <h6 class="dashboard-title my-2 mx-3">Users</h6>
      <div class="headadmin-btns d-none">
        <button type="button" class="btn btn-sm mr-2 text-md <?= $view !== 'customers' ? 'd-none' : '' ?>" data-modal="#modal-register">Register Customer</button>

        <a href="/user-management.php?view=all" class="mr-2 text-none <?= $view === 'all' ? 'd-none' : '' ?>">
          <button type="button" class="btn btn-sm btn-tertiary">View All</button>
        </a>
        <a href="/user-management.php?view=customers" class="mr-2 text-none <?= $view === 'customers' ? 'd-none' : '' ?>">
          <button type="button" class="btn btn-sm btn-tertiary">Customers</button>
        </a>
        <a href="/user-management.php?view=farmers" class="mr-2 text-none <?= $view === 'farmers' ? 'd-none' : '' ?>">
          <button type="button" class="btn btn-sm btn-tertiary">Farmers</button>
        </a>
      </div>

      <div class="admin-btns mr-3 d-none">
        <button type="button" class="btn btn-primary" data-modal="#modal-register">Add Farmer</button>
      </div>
    </div>

    <table class="dashboard-table">
      <thead>
        <tr>
          <th>CODE</th>
          <th>Full Name</th>
          <th>Email Address</th>
          <th>Date Created</th>
          <th>Last Login</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody id="users">
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

    <div class="modal-container d-none"></div>
    <div id="modal-register" class="modal">
      <form action="/api/register.php" method="post" id="form-register" class="card card-secondary p-4">
        <h3 class="card-title mb-2 text-center">
          REGISTER
          <span class="headadmin-btns d-none">CUSTOMER</span>
          <span class="admin-btns d-none">FARMER</span>
        </h3>

        <div class="d-flex">
          <div class="mx-1">
            <div class="form-group mb-2">
              <label for="register-name">Name:</label>
              <input type="text" id="register-name" name="name" placeholder="Name" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="register-gender">Gender:</label>
              <select id="register-gender" name="gender" class="form-control mt-1" required />
                <option value="" selected>Select gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="others">Others</option>
              </select>
            </div>

            <div class="form-group mb-2">
              <label for="register-birthday">Birthday:</label>
              <input type="text" id="register-birthday" name="birthday" placeholder="YYYY-MM-DD" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="register-email">Email:</label>
              <input type="email" id="register-email" name="email" placeholder="Email" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="register-mobile">Mobile Number:</label>
              <input type="text" id="register-mobile" name="mobile" placeholder="Mobile Number" class="form-control mt-1" required />
            </div>
          </div>

          <div class="d-flex flex-column mx-1">
            <div class="form-group mb-2">
              <label for="register-username">Username:</label>
              <input type="text" id="register-username" name="username" placeholder="Username" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="register-password">Password:</label>
              <input type="password" id="register-password" name="password" placeholder="Password" class="form-control mt-1" required />
            </div>
           <div class="form-group mb-2">
              <label for="register-address-street">Purok/Street:</label>
              <input type="text" id="register-address-street" name="address-street" placeholder="Purok/Street" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="register-address-brgy">Barangay:</label>
              <input type="text" id="register-address-brgy" name="address-brgy" placeholder="Barangay" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="register-address-city">City:</label>
              <input type="text" id="register-address-city" name="address-city" placeholder="City" class="form-control mt-1" required />
            </div>

            <div class="align-self-end mt-2">
              <div id="form-register-error" class="text-danger mb-1"></div>
              <button type="submit" class="btn btn-primary">Register Account</button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div id="modal-edit" class="modal">
      <form action="/api/admin/edit.php" method="post" id="form-edit" class="card card-secondary p-4">
        <h3 class="card-title mb-2 text-center">Edit <span id="edit-type-text"></span> Details</h3>

        <div class="d-flex">
          <div class="mx-1">
            <input type="hidden" id="edit-id" name="id" value="" required />

            <div class="form-group mb-2">
              <label for="edit-name">Name:</label>
              <input type="text" id="edit-name" name="name" placeholder="Name" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="edit-gender">Gender:</label>
              <select id="edit-gender" name="gender" class="form-control mt-1" required />
                <option value="" selected>Select gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="others">Others</option>
              </select>
            </div>

            <div class="form-group mb-2">
              <label for="edit-birthday">Birthday:</label>
              <input type="text" id="edit-birthday" name="birthday" placeholder="YYYY-MM-DD" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="edit-email">Email:</label>
              <input type="email" id="edit-email" name="email" placeholder="Email" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="edit-mobile">Mobile Number:</label>
              <input type="text" id="edit-mobile" name="mobile" placeholder="Mobile Number" class="form-control mt-1" required />
            </div>
          </div>

          <div class="d-flex flex-column mx-1">
            <div class="form-group mb-2">
              <label for="edit-username">Username:</label>
              <input type="text" id="edit-username" name="username" placeholder="Username" class="form-control mt-1" readonly />
            </div>

            <div class="form-group mb-2">
              <label for="edit-password">New Password:</label>
              <input type="password" id="edit-password" name="password" placeholder="Password" class="form-control mt-1" />
            </div>
           <div class="form-group mb-2">
              <label for="edit-address-street">Purok/Street:</label>
              <input type="text" id="edit-address-street" name="address-street" placeholder="Purok/Street" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="edit-address-brgy">Barangay:</label>
              <input type="text" id="edit-address-brgy" name="address-brgy" placeholder="Barangay" class="form-control mt-1" required />
            </div>

            <div class="form-group mb-2">
              <label for="edit-address-city">City:</label>
              <input type="text" id="edit-address-city" name="address-city" placeholder="City" class="form-control mt-1" required />
            </div>

            <div class="align-self-end mt-2">
              <div id="form-edit-error" class="text-danger mb-1"></div>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <template id="temp-user">
      <tr>
        <td class="user-code"></td>
        <td class="user-name"></td>
        <td class="user-email"></td>
        <td class="user-created"></td>
        <td class="user-lastlogin"></td>
        <td class="user-actions">none</td>
      </tr>
    </template>

    <template id="temp-user-actions">
      <button type="button" class="btn btn-text user-action-edit">
        <img src="/imgs/edit.svg" alt="Edit button" />
      </button>
      <button type="button" class="btn btn-text user-action-archive">
        <img src="/imgs/archive.svg" alt="Archive button" />
      </button>
    </template>
  </div>
</main>
<?php

out_footer();

?>
