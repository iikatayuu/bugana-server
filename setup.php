<?php

require_once 'includes/html.php';
require_once 'includes/database.php';

$headadmin_res = $conn->query("SELECT * FROM users WHERE type='headadmin'");
$admin_res = $conn->query("SELECT * FROM users WHERE type='admin'");

if ($admin_res->num_rows > 0 && $headadmin_res->num_rows > 0) {
  header('Location: /');
  exit(0);
}

out_header('BUGANA Setup Page', ['/css/setup.css'], ['/js/setup.js']);

?>
<form action="/api/setup.php" method="post" id="form-setup">
  <img src="/imgs/logo.png" alt="BUGANA Logo" width="228" height="209" class="setup-logo" />

  <div class="card text-center">
    <h3 class="setup-title">SET UP</h3>
    <input type="hidden" name="type" value="admin" />

    <?php if ($headadmin_res->num_rows === 0) { ?>
    <h6 class="setup-subtitle mt-3 mb-1">Head Administrator Credentials</h6>
    <div class="form-group">
      <input type="text" id="headadmin-user" name="headadmin-username" placeholder="Username" class="form-control" />
    </div>

    <div class="form-group">
      <input type="password" id="headadmin-pass" name="headadmin-password" placeholder="Password" class="form-control" />
    </div>
    <?php } ?>

    <?php if ($admin_res->num_rows === 0) { ?>
    <h6 class="setup-subtitle mt-3 mb-1">Administrator Credentials</h6>
    <div class="form-group">
      <input type="text" id="admin-user" name="admin-username" placeholder="Username" class="form-control" />
    </div>

    <div class="form-group">
      <input type="password" id="admin-pass" name="admin-password" placeholder="Password" class="form-control" />
    </div>
    <?php } ?>

    <p id="form-setup-error" class="text-sm text-danger"></p>
    <button type="submit" class="mt-3 mb-4 btn btn-block btn-primary">SUBMIT</button>
  </div>
</form>
<?php

out_footer();

?>
