<?php

require_once '../includes/database.php';

$shippings_res = $conn->query("SELECT * FROM shipping");
$shippings = [];

while ($shipping = $shippings_res->fetch_object()) {
  $shippings[] = [
    'name' => $shipping->name,
    'fee' => $shipping->fee
  ];
}

echo json_encode($shippings);

?>
