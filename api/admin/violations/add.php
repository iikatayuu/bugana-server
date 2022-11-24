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
  $transaction_id = !empty($_POST['transactionid']) ? $conn->real_escape_string($_POST['transactionid']) : null;

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
      $transaction_res = $conn->query("SELECT * FROM transactions WHERE transaction_code='$transaction_id' LIMIT 1");
      if ($transaction_res->num_rows === 0) {
        $result['message'] = 'No transaction found';
        die(json_encode($result));
      }

      $transaction = $transaction_res->fetch_object();
      $userid = $transaction->user;
      $conn->query("INSERT INTO violations (user, transaction_code) VALUES ($userid, '$transaction_id')");

      $result['success'] = true;
      $result['message'] = '';
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
