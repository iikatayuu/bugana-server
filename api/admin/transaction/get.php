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
  'message' => 'Invalid request',
  'transactions' => []
];

if (!empty($_POST['token'])) {
  $decoded = null;
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
    if ($usertype === 'headadmin') {
      $code = !empty($_POST['code']) ? $conn->real_escape_string($_POST['code']) : null;
      if (!$code) {
        $response['message'] = 'Invalid code';
        die(json_encode($response));
      }

      $query = "SELECT
          transactions.*,
          users.name AS userfullname,
          users.code AS usercode,
          users.username AS username,
          users.addressstreet AS addressstreet,
          users.addresspurok AS addresspurok,
          users.addressbrgy AS addressbrgy,
          products.name AS productname,
          products.user AS farmerid,
          products.price AS price
        FROM transactions
        JOIN users ON users.id=transactions.user
        JOIN products ON products.id=transactions.product
        WHERE transactions.transaction_code='$code'";

      $transactions_res = $conn->query($query);
      $transactions = [];

      while ($transaction = $transactions_res->fetch_object()) {
        $usercode = $transaction->usercode;
        $farmerid = $transaction->farmerid;
        $farmer_res = $conn->query("SELECT * FROM users WHERE id=$farmerid LIMIT 1");
        $farmerobj = $farmer_res->num_rows > 0 ? $farmer_res->fetch_object() : null;
        $farmercode = $farmerobj->code;

        $transactionitem = [
          'id' => $transaction->id,
          'code' => $transaction->transaction_code,
          'user' => [
            'id' => $transaction->user,
            'code' => $usercode,
            'name' => $transaction->userfullname,
            'username' => $transaction->username,
            'addressstreet' => $transaction->addressstreet,
            'addresspurok' => $transaction->addresspurok,
            'addressbrgy' => $transaction->addressbrgy
          ],
          'product' => [
            'id' => $transaction->product,
            'code' => $farmercode,
            'name' => $transaction->productname,
            'price' => $transaction->price
          ],
          'quantity' => $transaction->quantity,
          'date' => $transaction->date,
          'amount' => $transaction->amount,
          'paymentoption' => $transaction->paymentoption,
          'status' => $transaction->status
        ];

        $transactions[] = $transactionitem;
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['transactions'] = $transactions;
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
