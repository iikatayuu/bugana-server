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

ini_set('display_errors', '1');
error_reporting(E_ALL);
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
  $items = $POST['items'];
  $shipping = $conn->real_escape_string($POST['shipping']);
  $paymentoption = $conn->real_escape_string($POST['paymentoption']);

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
    $error = false;

    $user_res = $conn->query("SELECT * FROM users WHERE id=$userid");
    if ($user_res->num_rows > 0) {
      $user = $user_res->fetch_object();
      if ($user->verified === '0') {
        $result['message'] = 'You are not verified';
        $error = true;
      }
    } else {
      $result['message'] = 'No user found';
      $error = true;
    }
    foreach ($items as $item) {
      $itemid = $item['id'];
      $productid = $item['product']['id'];
      $quantity = intval($item['quantity']);
      $product_res = $conn->query("SELECT COALESCE(SUM(quantity), 0) AS stocks FROM stocks WHERE product=$productid");

      if ($product_res->num_rows > 0) {
        $product = $product_res->fetch_object();
        if ($product->stocks < $quantity) {
          $result['message'] = 'Out of stocks';
          $error = true;
          break;
        }
      } else {
        $result['message'] = 'Product not found';
        $error = true;
        break;
      }
    }

    if ($error) die(json_encode($result));

    $trans_code = '';
    do {
      $trans_code = random_str();
      $check_res = $conn->query("SELECT * FROM transactions WHERE transaction_code='$trans_code' LIMIT 1");
    } while ($check_res->num_rows > 0);

    foreach ($items as $item) {
      $itemid = $item['id'];
      $productid = $item['product']['id'];
      $quantity = intval($item['quantity']);
      $product_res = $conn->query("SELECT * FROM products WHERE id=$productid LIMIT 1");

      if ($product_res->num_rows > 0) {
        $product = $product_res->fetch_object();
        $price = floatval($product->price);
        $amount = $price * $quantity;

        if ($itemid !== '0') $conn->query("DELETE FROM carts WHERE id=$itemid");

        $total_stock_out = 0;
        while ($total_stock_out < $quantity) {
          $stockin_res = $conn->query("SELECT id, stocks FROM stocks WHERE product=$productid AND stocks > 0 AND quantity > 0 ORDER BY date ASC LIMIT 1");
          if ($stockin_res->num_rows > 0) {
            $stockin = $stockin_res->fetch_object();
            $stockinid = $stockin->id;
            $stocks = $stockin->stocks;
            $stock_out = 0;

            if ($stocks >= $quantity) {
              $stock_out = $quantity - $total_stock_out;
              $stocks -= $stock_out;
            } else {
              $stock_out = $stocks;
              $stocks = 0;
            }

            $total_stock_out += $stock_out;
            $stock_out_amount = $price * $stock_out;
            $conn->query("UPDATE stocks SET stocks=$stocks WHERE id=$stockinid");
            $conn->query("INSERT INTO stocks (product, quantity, stocks, amount, status, transaction_code)
              VALUES ($productid, -$stock_out, $stockinid, $stock_out_amount, 'sold', '$trans_code')");
          } else break;
        }

        $conn->query("INSERT INTO transactions (transaction_code, user, product, quantity, shipping, amount, paymentoption)
          VALUES ('$trans_code', $userid, $productid, $quantity, $shipping, $amount, '$paymentoption')");
      }
    }

    $result['success'] = true;
    $result['message'] = '';
  }
}

echo json_encode($result);

?>
