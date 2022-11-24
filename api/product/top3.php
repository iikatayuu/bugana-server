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
  'message' => 'Invalid request',
  'products' => null
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
    $products_res = $conn->query("SELECT
      products.*,
      (
        SELECT COALESCE(SUM(transactions.amount), 0)
        FROM transactions WHERE transactions.product = products.id
      ) AS sales
    FROM products
    WHERE products.user=$userid ORDER BY sales DESC LIMIT 3");
    $products = [];

    $imgpath = __DIR__ . '/../../userdata/products';
    while ($product = $products_res->fetch_object()) {
      $id = $product->id;
      $sales = $product->sales;
      if ($sales == 0) break;

      $imgres = glob("$imgpath/$id-*.{jpg,jpeg,png}", GLOB_BRACE);
      $photos = [];

      foreach ($imgres as $img) {
        $basename = pathinfo($img, PATHINFO_BASENAME);
        $photos[] = "/userdata/products/$basename";
      }

      $userid = strval($product->user);
      while (strlen($userid) < 2) $userid = "0$userid";
      $product->code = "F$userid";
      $product->photos = $photos;

      $products[] = $product;
    }

    $result['success'] = true;
    $result['message'] = '';
    $result['products'] = $products;
  } else {
    $result['message'] = 'Invalid user or product';
  }
}

echo json_encode($result);

?>
