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

if (!empty($_POST['token'])) {
  $decoded = null;
  $quantity = $conn->real_escape_string($_POST['quantity']);
  $productid = $conn->real_escape_string($_POST['product']);

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
    $userid = $decoded->userid;
    $user_res = $conn->query("SELECT * FROM users WHERE id=$userid LIMIT 1");
    $product_res = $conn->query("SELECT * FROM products WHERE id=$productid LIMIT 1");

    if ($user_res->num_rows > 0 && $product_res->num_rows > 0) {
      $exist_res = $conn->query("SELECT * FROM carts WHERE user=$userid AND product=$productid LIMIT 1");
      if ($exist_res->num_rows > 0) {
        $exist = $exist_res->fetch_object();
        $cartid = $exist->id;
        $conn->query("UPDATE carts SET quantity=quantity+$quantity WHERE id=$cartid");
      } else {
        $conn->query("INSERT INTO carts (user, product, quantity) VALUES ($userid, $productid, $quantity)");
      }

      $result['success'] = true;
      $result['message'] = '';
    } else {
      $result['message'] = 'Invalid user or product';
    }
  }
}

echo json_encode($result);

?>
