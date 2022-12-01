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
  'message' => 'Invalid request'
];

if (!empty($_POST['token'])) {
  $decoded = null;
  $code = $conn->real_escape_string($_POST['farmer-code']);
  $name = $conn->real_escape_string($_POST['name']);
  $category = $conn->real_escape_string($_POST['category']);
  $description = $conn->real_escape_string($_POST['description']);
  $price = $conn->real_escape_string($_POST['price']);
  $perish = $conn->real_escape_string($_POST['perish-days']);
  $photos = [];

  $i = 0;
  while (isset($_FILES["photo-$i"])) {
    $photo = $_FILES["photo-$i"];
    $photos[] = $photo;
    $i++;
  }

  try {
    $decoded = JWT::decode($_POST['token'], new Key(JWT_KEY, JWT_ALGO));
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
      if (!preg_match('/^(C|F)(\d{2})$/', $code)) {
        $result['message'] = 'Invalid code';
        die(json_encode($result));
      }

      if (!preg_match('/^(\d+(\.\d+)?)$/', $price)) {
        $result['message'] = 'Invalid price';
        die(json_encode($result));
      }

      $type = substr($code, 0, 1) === 'C' ? 'customer' : 'farmer';
      if ($type !== 'farmer') {
        $result['message'] = 'User is not a farmer';
        die(json_encode($result));
      }

      $userid = intval(substr($code, 1, 2));
      $users_res = $conn->query("SELECT * FROM users WHERE id=$userid AND type='farmer' LIMIT 1");
      
      if ($users_res->num_rows === 0) {
        $result['message'] = 'User code does not exist';
        die(json_encode($result));
      }

      $conn->query("INSERT INTO products (name, user, category, description, price, perish) VALUES ('$name', $userid, '$category', '$description', $price, $perish)");
      $productid = $conn->insert_id;
      $imgpath = __DIR__ . '/../../../userdata/products';
      for ($i = 0; $i < count($photos); $i++) {
        $photo = $photos[$i];
        $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
        move_uploaded_file($photo['tmp_name'], "$imgpath/$productid-$i.$ext");
      }

      $result['success'] = true;
      $result['message'] = '';
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
