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
  'message' => 'Invalid request',
  'profile' => null
];

if (!empty($_GET['token'])) {
  $decoded = null;
  try {
    $decoded = JWT::decode($_GET['token'], new Key(JWT_KEY, JWT_ALGO));
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
    $usertype = $decoded->type;
    $user_res = $conn->query("SELECT * FROM users WHERE id=$userid AND type='$usertype' AND active=1 LIMIT 1");

    if ($user_res->num_rows > 0) {
      $user = $user_res->fetch_object();
      $profile = [];
      $expose = [
        'id', 'code', 'username', 'email', 'mobile', 'name', 'gender', 'birthday',
        'addressstreet', 'addresspurok', 'addressbrgy', 'type', 'created', 'lastlogin'
      ];

      foreach ($expose as $prop) {
        $profile[$prop] = $user->{$prop};
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['profile'] = $profile;
    } else {
      $result['message'] = 'No user found';
    }
  }
}

echo json_encode($result);

?>
