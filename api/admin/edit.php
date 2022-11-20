<?php

require_once '../../vendor/autoload.php';
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/utils.php';

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
  'users' => null
];

if (!empty($_POST['token'])) {
  $decoded = null;
  $id = $conn->real_escape_string($_POST['id']);
  $name = $conn->real_escape_string($_POST['name']);
  $gender = $conn->real_escape_string($_POST['gender']);
  $birthday = $conn->real_escape_string($_POST['birthday']);
  $password = $_POST['password'];
  $email = $conn->real_escape_string($_POST['email']);
  $mobile = $conn->real_escape_string($_POST['mobile']);
  $address_street = $conn->real_escape_string($_POST['address-street']);
  $address_purok = $conn->real_escape_string($_POST['address-purok']);
  $address_brgy = $conn->real_escape_string($_POST['address-brgy']);

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
      if (!preg_match('/^\+?([0-9]{11,13})$/', $mobile)) {
        $result['message'] = 'Invalid mobile number';
        die(json_encode($result));
      }
    
      if (!preg_match('/^(\d{4}-\d{2}-\d{2})$/', $birthday)) {
        $result['message'] = 'Invalid date for birthday';
        die(json_encode($result));
      }

      if ($password !== '') {
        if (preg_match('/^([a-zA-Z0-9\-_]{8,32})$/', $password)) {
          $password = password_hash($password, PASSWORD_BCRYPT);
          $conn->query("UPDATE users SET password='$password' WHERE id=$id");
        } else {
          $result['message'] = 'Invalid password';
          die(json_encode($result));
        }
      }

      $conn->query("UPDATE users SET name='$name', gender='$gender', birthday='$birthday', email='$email', mobile='$mobile',
                    addressstreet='$address_street', addresspurok='$address_purok', addressbrgy='$address_brgy' WHERE id=$id");

      $result['success'] = true;
      $result['message'] = '';
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
