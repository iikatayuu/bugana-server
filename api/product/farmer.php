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
  'farmer' => null
];

$id = $conn->real_escape_string($_GET['id']);
$products_res = $conn->query("SELECT * FROM products WHERE id=$id LIMIT 1");

if ($products_res->num_rows > 0) {
  $product = $products_res->fetch_object();
  $userid = $product->user;
  $user_res = $conn->query(
    "SELECT
      users.*,
      (SELECT COUNT(products.id) FROM products WHERE products.user=users.id) AS products,
      (
        SELECT COUNT(DISTINCT(transactions.transaction_code))
        FROM transactions
        JOIN products ON transactions.product=products.id AND products.user=users.id
        WHERE transactions.status='success'
      ) AS transactions
    FROM users
    WHERE users.id=$userid LIMIT 1"
  );

  $user = $user_res->fetch_object();
  $farmer = [];
  $expose = [
    'id', 'username', 'email', 'mobile', 'name', 'gender', 'birthday',
    'addressstreet', 'addresspurok', 'addressbrgy', 'type', 'created', 'lastlogin',
    'products', 'transactions'
  ];

  foreach ($expose as $prop) {
    $farmer[$prop] = $user->{$prop};
  }

  $result['success'] = true;
  $result['message'] = '';
  $result['farmer'] = $farmer;
} else {
  $result['message'] = 'Product not found';
}

echo json_encode($result);

?>
