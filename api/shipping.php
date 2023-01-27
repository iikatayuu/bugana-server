<?php

require_once '../includes/database.php';
require_once '../includes/utils.php';

header('Content-Type: application/json');
cors();

$result = [
  'success' => false,
  'message' => 'Invalid request',
  'fee' => 0
];

$brgy = !empty($_GET['brgy']) ? $conn->real_escape_string($_GET['brgy']) : null;
if ($brgy) {
  $shippings_res = $conn->query("SELECT * FROM shipping WHERE name='$brgy' LIMIT 1");
  if ($shippings_res->num_rows > 0) {
    $result['fee'] = floatval($shippings_res->fetch_object()->fee);
    $result['success'] = true;
    $result['message'] = '';
  } else {
    $result['message'] = 'Barangay was not found';
  }
}

echo json_encode($result);

?>
