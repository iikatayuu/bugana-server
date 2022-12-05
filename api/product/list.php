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
  'products' => null,
  'next' => false,
  'prev' => false,
  'pages' => 0
];

$page = !empty($_GET['page']) ? $_GET['page'] : '1';
$limit = !empty($_GET['limit']) ? $conn->real_escape_string($_GET['limit']) : '10';
$category = !empty($_GET['category']) ? $conn->real_escape_string($_GET['category']) : 'all';
$farmer = !empty($_GET['farmer']) ? $conn->real_escape_string($_GET['farmer']) : null;
$search = !empty($_GET['search']) ? $conn->real_escape_string($_GET['search']) : null;
$productsort = !empty($_GET['product_sort']) ? $conn->real_escape_string($_GET['product_sort']) : null;
$pricesort = !empty($_GET['price_sort']) ? $conn->real_escape_string($_GET['price_sort']) : null;
$random = isset($_GET['random']);

if (!preg_match('/^(\d+)$/', $page)) {
  $result['message'] = 'Invalid page';
  die(json_encode($result));
}

if ($farmer && !preg_match('/^(F\d{2})$/', $farmer)) {
  $result['message'] = 'Invalid farmer code';
  die(json_encode($result));
}

$query = "SELECT * FROM products";
$count_query = "SELECT COUNT(*) AS count FROM products";
$wheres = [];

if ($category !== 'all' && $category !== '') $wheres[] = "category='$category'";
if ($search) $wheres[] = "name LIKE '%$search%'";
if ($farmer) {
  $userid = intval(substr($farmer, 1, 2));
  $wheres[] = "user=$userid";
}

$add_q = count($wheres) > 0 ? ' WHERE ' . implode(' AND ', $wheres) : '';
$query .= $add_q;
$count_query .= $add_q;

$orders = [];
if ($productsort && $productsort === 'asc') $orders[] = 'name ASC';
if ($productsort && $productsort === 'desc') $orders[] = 'name DESC';
if ($pricesort && $pricesort === 'asc') $orders[] = 'price ASC';
if ($pricesort && $pricesort === 'desc') $orders[] = 'price DESC';
$default_order = $random ? 'rand()' : 'created DESC';
$add_q = ' ORDER BY ' . (count($orders) > 0 ? implode(', ', $orders) : $default_order);
$query .= $add_q;

$page_q = (intval($page) - 1) * intval($limit);
$products_res = $conn->query("$query LIMIT $page_q, $limit");
$count_res = $conn->query($count_query);
$count = $count_res->fetch_object()->count;
$products = [];
$imgpath = __DIR__ . '/../../userdata/products';

while ($product = $products_res->fetch_object()) {
  $id = $product->id;
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

  if (!empty($_GET['stock'])) {
    $stock_in = [];
    $stock_out = [];
    $current_stocks = 0;
    $stocks_res = $conn->query("SELECT * FROM stocks WHERE product=$id ORDER BY date DESC");

    while ($stocks = $stocks_res->fetch_object()) {
      $stockid = $stocks->id;
      $quantity = intval($stocks->quantity);
      $current_stocks += $quantity;

      if ($quantity < 0) {
        $status = $stocks->status;
        $stock_out[] = [
          'quantity' => $quantity,
          'status' => $status,
          'date' => $stocks->date
        ];
      } else {
        $outs_res = $conn->query("SELECT * FROM stocks WHERE quantity < 0 AND stocks=$stockid");
        $revenue = 0;
        while ($out = $outs_res->fetch_object()) {
          if ($out->status == 'sold') $revenue += intval($out->amount);
        }

        $now_date = time();
        $stock_date = strtotime($stocks->date);
        $perish_days = $product->perish;
        $perish = strtotime("+$perish_days days", $stock_date);
        $days_to_perish = round(($perish - $now_date) / (60 * 60 * 24));

        $stock_in[] = [
          'quantity' => $quantity,
          'date' => $stocks->date,
          'revenue' => $revenue,
          'perishDays' => $days_to_perish > 0 ? $days_to_perish : 0
        ];
      }
    }

    $product->stocksIn = $stock_in;
    $product->stocksOut = $stock_out;
    $product->currentStocks = $current_stocks;
  }

  $products[] = $product;
}

$result['success'] = true;
$result['message'] = '';
$result['products'] = $products;
$result['next'] = ($page_q + intval($limit)) < $count;
$result['prev'] = $page !== '1';
$result['pages'] = ceil($count / intval($limit));

echo json_encode($result);

?>
