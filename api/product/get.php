<?php

require_once '../../includes/database.php';
require_once '../../includes/utils.php';

header('Content-Type: application/json');
cors();

$result = [
  'success' => false,
  'message' => 'Invalid request',
  'product' => null
];

if (!empty($_GET['id'])) {
  $id = $conn->real_escape_string($_GET['id']);
  $products_res = $conn->query("SELECT * FROM products WHERE id=$id LIMIT 1");

  if ($products_res->num_rows > 0) {
    $product = $products_res->fetch_object();
    $imgpath = __DIR__ . '/../../userdata/products';
    $imgres = glob("$imgpath/$id-*.{jpg,jpeg,png}", GLOB_BRACE);
    $photos = [];

    foreach ($imgres as $img) {
      $basename = pathinfo($img, PATHINFO_BASENAME);
      $photos[] = "/userdata/products/$basename";
    }

    if (!empty($_GET['stock'])) {
      $stock_in = [];
      $stock_out = [];
      $current_stocks = 0;
      $stocks_res = $conn->query("SELECT * FROM stocks WHERE product=$id ORDER BY date DESC");
  
      while ($stocks = $stocks_res->fetch_object()) {
        $quantity = intval($stocks->quantity);
        $current_stocks += $quantity;
  
        if ($quantity < 0) {
          $stock_out[] = [
            'quantity' => $quantity,
            'date' => $stocks->date
          ];
        } else {
          $stock_in[] = [
            'quantity' => $quantity,
            'date' => $stocks->date
          ];
        }
      }
  
      $product->stocksIn = $stock_in;
      $product->stocksOut = $stock_out;
      $product->currentStocks = $current_stocks;
    }

    $product->photos = $photos;
    $result['success'] = true;
    $result['message'] = '';
    $result['product'] = $product;
  } else {
    $result['message'] = 'Product ID not found';
  }
}

echo json_encode($result);

?>
