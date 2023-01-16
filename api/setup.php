<?php

require_once '../includes/config.php';
require_once '../includes/database.php';

header('Content-Type: application/json');

$result = [
  'success' => false,
  'message' => 'Invalid request'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $headadmin_user = !empty($_POST['headadmin-username']) ? $conn->real_escape_string($_POST['headadmin-username']) : null;
  $headadmin_pass = !empty($_POST['headadmin-password']) ? password_hash($_POST['headadmin-password'], PASSWORD_BCRYPT) : null;

  if ($headadmin_user === null || $headadmin_pass === null) {
    $result['message'] = 'Invalid params';
    die(json_encode($result));
  }

  $headadmin_res = $conn->query("SELECT * FROM users WHERE type='headadmin'");
  if ($headadmin_user !== null && $headadmin_res->num_rows === 0) {
    $conn->query("INSERT INTO users (code, username, password, email, mobile, name, gender, birthday, addressstreet, addresspurok, addressbrgy, type, verified)
                  VALUES ('A00', '$headadmin_user', '$headadmin_pass', 'headadmin', '', 'Head Administrator', '', '', '', '', '', 'headadmin', 1)");
  }

  $result['success'] = true;
  $result['message'] = '';
}

echo json_encode($result);

?>
