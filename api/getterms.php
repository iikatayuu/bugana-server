<?php

require_once '../includes/database.php';
require_once '../includes/utils.php';

header('Content-Type: application/json');
cors();

$terms_res = $conn->query("SELECT * FROM terms ORDER BY id DESC");
$result = [
  'success' => false,
  'message' => 'Unknown error',
  'terms' => ''
];

if ($terms_res->num_rows > 0) {
  $terms = $terms_res->fetch_object();
  $result['success'] = true;
  $result['message'] = '';
  $result['terms'] = $terms->terms;
}

echo json_encode($result);

?>
