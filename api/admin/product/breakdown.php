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
  'message' => 'Invalid request',
  'breakdown' => []
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
    if ($usertype === 'headadmin' || $usertype === 'admin') {
      $product_name = !empty($_POST['product']) ? $conn->real_escape_string($_POST['product']) : null;
      if (!$product_name) {
        $response['message'] = 'Invalid params';
        die(json_encode($response));
      }

      $query = "SELECT
          products.*,
          users.name AS username,
          (SELECT COALESCE(SUM(transactions.quantity), 0) FROM transactions WHERE transactions.product=products.id) AS quantity,
          (SELECT COALESCE(SUM(transactions.amount), 0) FROM transactions WHERE transactions.product=products.id) AS amount
        FROM products
        JOIN users ON users.id=products.user
        WHERE products.name='$product_name'";

      $product_res = $conn->query($query);
      $breakdown = [];
      
      while ($detail = $product_res->fetch_object()) {
        $username = $detail->username;
        $detail_item = [
          'name' => $detail->username,
          'price' => $detail->price,
          'quantity' => $detail->quantity,
          'totalAmount' => $detail->amount
        ];

        $breakdown[] = $detail_item;
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['breakdown'] = $breakdown;
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
