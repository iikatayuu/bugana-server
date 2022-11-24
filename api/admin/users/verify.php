<?php

require_once '../../../vendor/autoload.php';
require_once '../../../includes/config.php';
require_once '../../../includes/database.php';
require_once '../../../includes/utils.php';

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

if (!empty($_POST['token'])) {
  $decoded = null;
  try {
    $decoded = JWT::decode($_POST['token'], new Key(JWT_KEY, JWT_ALGO));
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
    $usertype = $decoded->type;
    if ($usertype === 'admin' || $usertype === 'headadmin') {
      $id = $conn->real_escape_string($_POST['id']);
      $conn->query("UPDATE users SET verified=1 WHERE id=$id");

      $result['success'] = true;
      $result['message'] = '';
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
