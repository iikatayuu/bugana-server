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
  'transactions' => []
];

if (!empty($_GET['token'])) {
  $decoded = null;
  try {
    $decoded = JWT::decode($_GET['token'], new Key(JWT_KEY, JWT_ALGO));
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
    $date = !empty($_GET['date']) ? $_GET['date'] : null;
    $query = "SELECT
        transactions.*,
        products.id AS productid, products.name, products.user AS farmerid, products.price
      FROM transactions
      JOIN products ON products.id=transactions.product";

    if ($date === 'weekly') {
      $current_day = date('w');
      $week_start = date('Y-m-d 00:00:00', strtotime("-$current_day days"));
      $week_end = date('Y-m-d 23:59:59', strtotime('+' . (6 - intval($current_day)) . ' days'));
      $query .= " WHERE transactions.date BETWEEN '$week_start' AND '$week_end'";
    } else if ($date === 'monthly') {
      $month_days = date('t');
      $month_start = date('Y-m-01 00:00:00');
      $month_end = date("Y-m-$month_days 23:59:59");
      $query .= " WHERE transactions.date BETWEEN '$month_start' AND '$month_end'";
    }

    $query .= " ORDER BY transactions.date DESC";
    $transactions_res = $conn->query($query);
    $transactions = [];
    while ($transaction = $transactions_res->fetch_object()) {
      $productid = $transaction->productid;
      $transactionitem = [
        'id' => $transaction->id,
        'quantity' => $transaction->quantity,
        'date' => $transaction->date,
        'amount' => $transaction->amount,
        'product' => [
          'id' => $transaction->productid,
          'name' => $transaction->name,
          'user' => $transaction->farmerid,
          'price' => $transaction->price
        ]
      ];

      $transactions[] = $transactionitem;
    }

    $result['success'] = true;
    $result['message'] = '';
    $result['transactions'] = $transactions;
  }
}

echo json_encode($result);

?>
