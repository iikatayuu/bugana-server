<?php

require_once '../vendor/autoload.php';
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/utils.php';

use Firebase\JWT\JWT;

header('Content-Type: application/json');
cors();

$result = [
  'success' => false,
  'message' => 'Invalid request',
  'token' => null
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $POST = !empty($_SERVER['CONTENT_TYPE']) && explode(';', $_SERVER['CONTENT_TYPE'])[0] === 'application/json'
    ? parse_jsondata()
    : $_POST;

  $type = $conn->real_escape_string($POST['type']);
  $username = $conn->real_escape_string($POST['username']);
  $password = $POST['password'];
  $query = $type !== 'admin'
    ? "SELECT * FROM users WHERE username='$username' AND type='$type' AND active=1 LIMIT 1"
    : "SELECT * FROM users WHERE username='$username' AND (type='admin' OR type='headadmin') AND active=1 LIMIT 1";
  $user_res = $conn->query($query);

  if ($user_res->num_rows > 0) {
    $user = $user_res->fetch_object();
    $userid = $user->id;
    if (password_verify($password, $user->password)) {
      $conn->query("UPDATE users SET lastlogin=CURRENT_TIMESTAMP() WHERE id=$userid");
      $payload = [
        'iss' => WEBURL,
        'aud' => WEBURL,
        'iat' => time(),
        'type' => $user->type,
        'username' => $username,
        'userid' => $userid,
        'name' => $user->name
      ];

      $token = JWT::encode($payload, JWT_KEY, JWT_ALGO);
      $result['success'] = true;
      $result['message'] = 'Successfully logged in!';
      $result['token'] = $token;
    } else {
      $result['message'] = 'Invalid password';
    }
  } else {
    $result['message'] = 'Username not found.';
  }
}

echo json_encode($result);

?>
