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
  'message' => 'Invalid request'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['token'];
  $image = $_FILES['image'];

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

  if ($decoded !== null) {
    $userid = $decoded->userid;
    $user_res = $conn->query("SELECT * FROM users WHERE id=$userid LIMIT 1");
    
    if ($user_res->num_rows > 0) {
      $imgpath = __DIR__ . '/../userdata/imgs';
      $imgres = glob("$imgpath/$userid.{jpg,jpeg,png}", GLOB_BRACE);
      foreach ($imgres as $filename) unlink($filename);

      $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
      move_uploaded_file($image['tmp_name'], "$imgpath/$userid.$ext");
      $result['success'] = true;
      $result['message'] = '';
    } else {
      $result['message'] = 'No user found';
    }
  }
}

echo json_encode($result);

?>
