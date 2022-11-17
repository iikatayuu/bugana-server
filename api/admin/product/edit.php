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
  $id = $conn->real_escape_string($_POST['id']);
  $name = $conn->real_escape_string($_POST['name']);
  $category = $conn->real_escape_string($_POST['category']);
  $description = $conn->real_escape_string($_POST['description']);
  $price = $conn->real_escape_string($_POST['price']);
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
      if (!preg_match('/^(\d+(\.\d+)?)$/', $price)) {
        $result['message'] = 'Invalid price';
        die(json_encode($result));
      }

      $success = $conn->query("UPDATE products SET name='$name', category='$category', description='$description', price=$price, edited=CURRENT_TIMESTAMP() WHERE id=$id");
      if ($success) {
        $imgpath = __DIR__ . '/../../../userdata/products';

        if (count($photos) > 0) {
          $imgres = glob("$imgpath/$id-*.{jpg,jpeg,png}", GLOB_BRACE);
          foreach ($imgres as $filename) unlink($filename);
        }

        for ($i = 0; $i < count($photos); $i++) {
          $photo = $photos[$i];
          $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
          move_uploaded_file($photo['tmp_name'], "$imgpath/$id-$i.$ext");
        }

        $result['success'] = true;
        $result['message'] = '';
      } else {
        $result['message'] = 'Unable to update product';
      }
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
