<?php

require_once 'includes/html.php';

out_header('BUGANA Admin Page', ['/css/login.css'], ['/js/login.js']);

?>
<form action="/api/login.php" method="post" id="form-login">
  <img src="/imgs/logo.png" alt="BUGANA Logo" class="login-logo" />

  <div class="card text-center">
    <h6 class="login-title mb-4">BUGANA</h6>
    <input type="hidden" name="type" value="admin" />

    <div class="form-group">
      <input type="text" name="username" placeholder="Username" class="form-control" required />
    </div>

    <div class="form-group">
      <input type="password" name="password" placeholder="Password" class="form-control" required />
    </div>

    <p id="form-login-error" class="text-sm text-danger"></p>
    <button type="submit" class="mt-3 mb-4 btn btn-block btn-primary">LOG IN</button>
  </div>
</form>
<?php

out_footer();

?>
