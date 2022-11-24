<?php

require_once '../vendor/autoload.php';
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/utils.php';

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
  'token' => null
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $POST = !empty($_SERVER['CONTENT_TYPE']) && explode(';', $_SERVER['CONTENT_TYPE'])[0] === 'application/json'
    ? parse_jsondata()
    : $_POST;

  $type = !empty($POST['type']) ? $conn->real_escape_string($POST['type']) : 'customer';
  $token = !empty($POST['token']) ? $conn->real_escape_string($POST['token']) : null;
  $name = $conn->real_escape_string($POST['name']);
  $gender = $conn->real_escape_string($POST['gender']);
  $birthday = $conn->real_escape_string($POST['birthday']);
  $username = $conn->real_escape_string($POST['username']);
  $password = $POST['password'];
  $email = $conn->real_escape_string($POST['email']);
  $mobile = $conn->real_escape_string($POST['mobile']);
  $address_street = $conn->real_escape_string($POST['address-street']);
  $address_purok = $conn->real_escape_string($POST['address-purok']);
  $address_brgy = $conn->real_escape_string($POST['address-brgy']);

  if (!preg_match('/^([a-zA-Z0-9\-_]{8,32})$/', $password)) {
    $result['message'] = 'Invalid password';
    die(json_encode($result));
  }

  if (!preg_match('/^\+?([0-9]{11,13})$/', $mobile)) {
    $result['message'] = 'Invalid mobile number';
    die(json_encode($result));
  }

  if (!preg_match('/^(\d{4}-\d{2}-\d{2})$/', $birthday)) {
    $result['message'] = 'Invalid date for birthday';
    die(json_encode($result));
  }

  $user_res = $conn->query("SELECT * FROM users WHERE username='$username' LIMIT 1");
  if ($user_res->num_rows === 0) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $verified = $type === 'farmer' ? 1 : 0;

    $conn->query("INSERT INTO users (username, password, email, mobile, name, gender, birthday, addressstreet, addresspurok, addressbrgy, type, verified)
                  VALUES ('$username', '$hash', '$email', '$mobile', '$name', '$gender', '$birthday', '$address_street', '$address_purok', '$address_brgy', '$type', $verified)");

    if ($type === 'customer') {
      $imgpath = __DIR__ . '/../userdata/ids';
      $validid = $_FILES['valid-id'];
      $userid = $conn->insert_id;
      $ext = pathinfo($validid['name'], PATHINFO_EXTENSION);
      $success = move_uploaded_file($validid['tmp_name'], "$imgpath/$userid.$ext");

      if (!$success) {
        $conn->query("DELETE FROM users WHERE id=$userid");
        $result['message'] = 'Unable to upload ID';
        die(json_encode($result));
      }
    }

    $result['success'] = true;
    $result['message'] = 'Registered successfully';
  } else {
    $result['message'] = 'Username already exists';
  }
}

echo json_encode($result);

?>
