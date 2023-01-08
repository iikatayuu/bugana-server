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

$limit = 10;
$query = "SELECT
    products.*,
    (SELECT COALESCE(SUM(transactions.quantity), 0) FROM transactions WHERE transactions.product=products.id) AS stocks
  FROM products ORDER BY stocks DESC LIMIT $limit
";
$products_res = $conn->query($query);
$products = [];
$imgpath = __DIR__ . '/../../userdata/products';

while ($product = $products_res->fetch_object()) {
  $id = $product->id;
  if ($id === null || $product->stocks === '0') continue;

  $imgres = glob("$imgpath/$id-*.{jpg,jpeg,png}", GLOB_BRACE);
  $photos = [];

  foreach ($imgres as $img) {
    $basename = pathinfo($img, PATHINFO_BASENAME);
    $photos[] = "/userdata/products/$basename";
  }

  $userid = strval($product->user);
  $users_res = $conn->query("SELECT * FROM users WHERE id=$userid LIMIT 1");
  $userobj = $users_res->num_rows > 0 ? $users_res->fetch_object() : null;
  $product->code = $userobj->code;
  $product->photos = $photos;

  $products[] = $product;
}

$result['success'] = true;
$result['message'] = '';
$result['products'] = $products;

echo json_encode($result);

?>
