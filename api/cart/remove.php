<?php

require_once '../../vendor/autoload.php';
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/utils.php';

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

$POST = !empty($_SERVER['CONTENT_TYPE']) && explode(';', $_SERVER['CONTENT_TYPE'])[0] === 'application/json'
  ? parse_jsondata()
  : $_POST;

if (!empty($POST['token'])) {
  $decoded = null;
  $cartid = $conn->real_escape_string($POST['id']);

  try {
    $decoded = JWT::decode($POST['token'], new Key(JWT_KEY, JWT_ALGO));
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
    $cart_res = $conn->query("SELECT * FROM carts WHERE id=$cartid AND user=$userid");

    if ($cart_res->num_rows > 0) {
      $conn->query("DELETE FROM carts WHERE id=$cartid");

      $result['success'] = true;
      $result['message'] = '';
    } else {
      $result['message'] = 'No cart item was found';
    }
  }
}

echo json_encode($result);

?>
