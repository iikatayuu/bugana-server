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
  'stocks' => [],
  'next' => false,
  'prev' => false,
  'pages' => 0
];

$page = !empty($_GET['page']) ? $_GET['page'] : '1';
$limit = !empty($_GET['limit']) ? $conn->real_escape_string($_GET['limit']) : '10';
$category = !empty($_GET['category']) ? $conn->real_escape_string($_GET['category']) : 'all';
$search = !empty($_GET['search']) ? $conn->real_escape_string($_GET['search']) : null;
$productsort = !empty($_GET['product_sort']) ? $conn->real_escape_string($_GET['product_sort']) : null;
$farmersort = !empty($_GET['farmer_sort']) ? $conn->real_escape_string($_GET['farmer_sort']) : null;
$stockoutsort = !empty($_GET['stockout_sort']) ? $conn->real_escape_string($_GET['stockout_sort']) : null;

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
    $usertype = $decoded->type;
    if ($usertype === 'admin' || $usertype === 'headadmin') {
      if (!preg_match('/^(\d+)$/', $page)) {
        $result['message'] = 'Invalid page';
        die(json_encode($result));
      }

      $products = [];
      if (!empty($search)) {
        $users_res = $conn->query("SELECT id FROM users WHERE name LIKE '%$search%'");
        while ($user = $users_res->fetch_object()) {
          $userid = $user->id;
          $products_res = $conn->query("SELECT id FROM products WHERE user=$userid");
          while ($product = $products_res->fetch_object()) {
            $products[] = $product->id;
          }
        }

        $products_res = $conn->query("SELECT id FROM products WHERE name LIKE '%$search%'");
        while ($product = $products_res->fetch_object()) {
          if (!in_array($product->id, $products)) $products[] = $product->id;
        }
      }

      if (!empty($category) && $category !== 'all') {
        $products_res = $conn->query("SELECT id FROM products WHERE category='$category'");
        while ($product = $products_res->fetch_object()) {
          if (!in_array($product->id, $products)) $products[] = $product->id;
        }
      }

      $count_query = "SELECT COUNT(*) AS count FROM stocks";
      $query = "SELECT
          stocks.*,
          products.user AS farmerid,
          products.name AS productname,
          products.category,
          transactions.status AS trans_status,
          (SELECT name FROM users WHERE users.id=products.user) AS userfullname
        FROM stocks
        JOIN products ON products.id=stocks.product
        JOIN transactions ON transactions.transaction_code=stocks.transaction_code";

      $wheres = ['stocks.quantity < 0'];
      if (count($products) > 0) {
        $products_q = [];
        for ($i = 0; $i < count($products); $i++) {
          $productid = $products[$i];
          $products_q[] = "stocks.product=$productid";
        }

        $products_q = implode(' OR ', $products_q);
        $wheres[] = "($products_q)";
      }

      if (!empty($category) && $category !== 'all' && count($products) === 0) {
        $wheres[] = 'stocks.product=0';
      }

      $add_q = ' WHERE ' . implode(' AND ', $wheres);
      $query .= $add_q;
      $count_query .= $add_q;

      $orders = [];
      if ($productsort && $productsort === 'asc') $orders[] = 'productname ASC';
      if ($productsort && $productsort === 'desc') $orders[] = 'productname DESC';
      if ($farmersort && $farmersort === 'asc') $orders[] = 'userfullname ASC';
      if ($farmersort && $farmersort === 'desc') $orders[] = 'userfullname DESC';
      if ($stockoutsort && $stockoutsort === 'asc') $orders[] = 'stocks.date ASC';
      if ($stockoutsort && $stockoutsort === 'desc') $orders[] = 'stocks.date DESC';

      $default_order = 'stocks.date DESC';
      $add_q = ' ORDER BY ' . (count($orders) > 0 ? implode(', ', $orders) : $default_order);
      $query .= $add_q;

      $page_q = (intval($page) - 1) * intval($limit);
      $stocks_res = $conn->query("$query LIMIT $page_q, $limit");
      $count_res = $conn->query($count_query);
      $count = $count_res->fetch_object()->count;
      $stocks = [];

      while ($stock = $stocks_res->fetch_object()) {
        $farmerid = $stock->farmerid;
        $stockitem = [
          'id' => $stock->id,
          'transaction_code' => $stock->transaction_code,
          'quantity' => $stock->quantity,
          'date' => $stock->date,
          'revenue' => $stock->amount,
          'status' => $stock->trans_status,
          'username' => $stock->userfullname,
          'product' => [
            'name' => $stock->productname,
            'user' => $farmerid,
            'category' => $stock->category,
            'price' => (float) floatval($stock->amount) / floatval($stock->quantity) * -1
          ]
        ];

        $stocks[] = $stockitem;
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['stocks'] = $stocks;
      $result['next'] = ($page_q + intval($limit)) < $count;
      $result['prev'] = $page !== '1';
      $result['pages'] = ceil($count / intval($limit));
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
