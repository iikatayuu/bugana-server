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
  $password = !empty($POST['password']) ? $POST['password'] : null;
  $email = $conn->real_escape_string($POST['email']);
  $mobile = $conn->real_escape_string($POST['mobile']);
  $address_street = $conn->real_escape_string($POST['address-street']);
  $address_purok = $conn->real_escape_string($POST['address-purok']);
  $address_brgy = $conn->real_escape_string($POST['address-brgy']);
  $verified = 0;

  if ($token === null && ($type === 'farmer' || $type === 'admin')) {
    $result['message'] = 'Token is required';
    die(json_encode($result));
  }

  if ($token !== null) {
    $decoded = null;
    try {
      $decoded = JWT::decode($token, new Key(JWT_KEY, JWT_ALGO));
    } catch (InvalidArgumentException $e) {
      $result['message'] = 'Invalid token';
    } catch (SignatureInvalidException $e)  {
      $result['message'] = 'Invalid signature';
    } catch (BeforeValidException $e) {
      $result['message'] = 'Token is no longer valid';
    } catch (ExpiredException $e) {
      $result['message'] = 'Token already expired';
    }

    if ($decoded === null) die(json_encode($result));
    if ($decoded->type === 'headadmin' || $decoded->type === 'admin') $verified = 1;
    else {
      $result['message'] = 'User is not admin';
      die(json_encode($result));
    }
  }

  if ($type !== 'admin' && !preg_match('/^([a-zA-Z0-9\-_]{8,32})$/', $password)) {
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
  if ($user_res->num_rows > 0) {
    $result['message'] = 'Username already exists';
    die(json_encode($result));
  }

  $name_res = $conn->query("SELECT * FROM users WHERE name='$name' LIMIT 1");
  if ($name_res->num_rows > 0) {
    $result['message'] = 'Name already exists';
    die(json_encode($result));
  }

  $phone_res = $conn->query("SELECT * FROM users WHERE mobile='$mobile' LIMIT 1");
  if ($phone_res->num_rows > 0) {
    $result['message'] = 'Phone number already exists';
    die(json_encode($result));
  }

  $name_res = $conn->query("SELECT * FROM users WHERE name='$name' LIMIT 1");
  if ($name_res->num_rows > 0) {
    $result['message'] = 'Name already exists';
    die(json_encode($result));
  }

  $code_res = $conn->query("SELECT code FROM users WHERE type='$type' ORDER BY id DESC LIMIT 1");
  $num = 0;

  if ($code_res->num_rows > 0) {
    $code_obj = $code_res->fetch_object();
    $num = intval(substr($code_obj->code, 1));
  }

  $numstr = strval(++$num);
  while (strlen($numstr) < 2) $numstr = "0$numstr";
  $code_prefix = '';
  if ($type === 'customer') $code_prefix = 'C';
  if ($type === 'farmer') $code_prefix = 'F';
  if ($type === 'admin') $code_prefix = 'A';
  $code = $code_prefix . $numstr;

  $hash = password_hash($type === 'admin' ? 'admin' : $password, PASSWORD_BCRYPT);
  $conn->query("INSERT INTO users (code, username, password, email, mobile, name, gender, birthday, addressstreet, addresspurok, addressbrgy, type, verified)
                VALUES ('$code', '$username', '$hash', '$email', '$mobile', '$name', '$gender', '$birthday', '$address_street', '$address_purok', '$address_brgy', '$type', $verified)");

  if ($type === 'customer' && $verified === 0) {
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
}

echo json_encode($result);

?>
