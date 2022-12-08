<?php

require_once '../vendor/autoload.php';
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/utils.php';

use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

header('Content-Type: application/json');
cors();

$result = [
  'success' => false,
  'message' => 'Invalid request'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $POST = !empty($_SERVER['CONTENT_TYPE']) && explode(';', $_SERVER['CONTENT_TYPE'])[0] === 'application/json'
    ? parse_jsondata()
    : $_POST;

  $token = $POST['token'];
  $key = $conn->real_escape_string($POST['key']);
  $value = $conn->real_escape_string($POST['value']);

  $decoded = null;
  try {
    $decoded = JWT::decode($token, new Key(JWT_KEY, JWT_ALGO));
  } catch (InvalidArgumentException $e) {
    $result['message'] = 'Invalid token';
  } catch (SignatureInvalidException $e)  {
    $result['message'] = 'Invalid signature';
  } catch (BeforeValidException $e) {
    $result['message'] = 'Token is no longer valid';
  } catch (ExpiredException $e) {
    $result['message'] = 'Token already expired';
  }

  if ($decoded !== null) {
    $userid = $decoded->userid;
    $user_res = $conn->query("SELECT * FROM users WHERE id=$userid LIMIT 1");
    if ($user_res->num_rows > 0) {
      $user = $user_res->fetch_object();
      $valid = true;

      if ($key === 'id' || $key === 'type') $valid = false;
      else if ($key === 'password' && !preg_match('/^([a-zA-Z0-9\-_]{8,32})$/', $value)) $valid = false;
      else if ($key === 'birthday' && !preg_match('/^(\d{4}-\d{2}-\d{2})$/', $value)) $valid = false;

      if ($valid) {
        if ($key === 'password') {
          $value = password_hash($value, PASSWORD_BCRYPT);
          $conn->query("UPDATE users SET $key='$value', temp_password='' WHERE id=$userid");
        } else {
          $conn->query("UPDATE users SET $key='$value' WHERE id=$userid");
        }

        $user->{$key} = $value;
        $payload = [
          'iss' => WEBURL,
          'aud' => WEBURL,
          'iat' => time(),
          'type' => $user->type,
          'username' => $user->username,
          'userid' => $userid,
          'name' => $user->name
        ];
  
        $token = JWT::encode($payload, JWT_KEY, JWT_ALGO);
        $result['success'] = true;
        $result['message'] = '';
        $result['token'] = $token;
      } else {
        $result['message'] = 'Invalid input';
      }
    } else {
      $result['message'] = 'No user found';
    }
  }
}

echo json_encode($result);

?>
