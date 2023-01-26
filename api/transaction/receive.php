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
  'message' => 'Invalid request'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $POST = !empty($_SERVER['CONTENT_TYPE']) && explode(';', $_SERVER['CONTENT_TYPE'])[0] === 'application/json'
    ? parse_jsondata()
    : $_POST;

  $decoded = null;
  $code = $conn->real_escape_string($POST['code']);

  try {
    $decoded = JWT::decode($POST['token'], new Key(JWT_KEY, JWT_ALGO));
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
    $tx_res = $conn->query("SELECT * FROM transactions WHERE transaction_code='$code' LIMIT 1");
    if ($tx_res->num_rows > 0) {
      $tx = $tx_res->fetch_object();
      if ($tx->status === 'approved') {
        $conn->query("UPDATE transactions SET status='success' WHERE user=$userid AND transaction_code='$code'");
        $result['success'] = true;
        $result['message'] = '';
      } else {
        $result['message'] = 'Transaction is not approved yet';
      }
    }
  }
}

echo json_encode($result);

?>
