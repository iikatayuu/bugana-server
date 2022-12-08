<?php

require_once '../includes/database.php';

header('Content-Type: text/plain');

$perish_res = $conn->query("SELECT GROUP_CONCAT(stocks.id) AS perished, COUNT(stocks.id) AS length FROM stocks
  JOIN products ON products.id = stocks.product AND DATEDIFF(NOW(), stocks.date) >= products.perish
  WHERE stocks.stocks > 0");

$perish = $perish_res->fetch_object();
$perished = explode(',', $perish->perished);
$length = intval($perish->length);

for ($i = 0; $i < $length; $i++) {
  $stockid = $perished[$i];
  $stocks_res = $conn->query("SELECT * FROM stocks WHERE id=$stockid");

  while ($stock = $stocks_res->fetch_object()) {
    $productid = $stock->product;
    $products_res = $conn->query("SELECT * FROM products WHERE id=$productid LIMIT 1");
    $product = $products_res->fetch_object();

    $available = intval($stock->stocks);
    $quantity = $available * -1;
    $amount = floatval($product->price) * $available;
    $conn->query("INSERT INTO stocks (product, quantity, stocks, amount, status)
      VALUES ($productid, $quantity, $stockid, $amount, 'perished')");

    $conn->query("UPDATE stocks SET stocks=0 WHERE id=$stockid");
  }
}

echo 'success';

?>
