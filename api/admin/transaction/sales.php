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
    $usertype = $decoded->type;
    if ($usertype !== 'admin' && $usertype !== 'headadmin') {
      $result['message'] = 'No access';
      die(json_encode($result));
    }

    $date = !empty($_GET['date']) ? $_GET['date'] : null;
    $unsold = !empty($_GET['unsold']) ? $_GET['unsold'] : null;
    $month = !empty($_GET['month']) ? intval($_GET['month']) + 1 : 1;

    if (!$unsold) {
      $query = "SELECT
          transactions.*,
          products.id AS productid, products.name, products.user AS farmerid, products.price,
          COALESCE(SUM(transactions.amount), 0) AS amount,
          COALESCE(SUM(transactions.quantity), 0) AS quantity
        FROM transactions
        JOIN products ON products.id=transactions.product
        WHERE transactions.status='success'";

      if ($date === 'weekly') {
        $current_day = date('w');
        $week_start = date('Y-m-d 00:00:00', strtotime("-$current_day days"));
        $week_end = date('Y-m-d 23:59:59', strtotime('+' . (6 - intval($current_day)) . ' days'));
        $query .= " AND transactions.date BETWEEN '$week_start' AND '$week_end'";
      } else if ($date === 'monthly') {
        $month_start = '';
        $month_end = '';

        if (!$month) {
          $month_start = date('Y-m-01 00:00:00');
          $month_end = date("Y-m-t 23:59:59");
        } else {
          $year = intval(date('Y'));
          $month = strval($month);
          while (strlen($month) < 2) $month = "0$month";
          $month_time = strtotime("$year-$month-01");
          $month_start = date('Y-m-01 00:00:00', $month_time);
          $month_end = date('Y-m-t 23:59:59', $month_time);
        }

        $query .= " AND transactions.date BETWEEN '$month_start' AND '$month_end'";
      } else if ($date === 'annual') {
        $year_start = date('Y-01-01 00:00:00');
        $year_end = date("Y-12-31 23:59:59");
        $query .= " AND transactions.date BETWEEN '$year_start' AND '$year_end'";
      }

      $query .= " GROUP BY products.name ORDER BY transactions.date DESC";
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
    } else {
      $query = "SELECT
          stocks.*,
          products.id AS productid,
          products.name,
          products.user AS farmerid,
          products.price,
          COALESCE(SUM(stocks.amount), 0) AS amount,
          COALESCE(SUM(stocks.quantity), 0) AS quantity
        FROM stocks
        JOIN products ON products.id=stocks.product WHERE stocks.status='perished'";

      if ($date === 'weekly') {
        $current_day = date('w');
        $week_start = date('Y-m-d 00:00:00', strtotime("-$current_day days"));
        $week_end = date('Y-m-d 23:59:59', strtotime('+' . (6 - intval($current_day)) . ' days'));
        $query .= " AND stocks.date BETWEEN '$week_start' AND '$week_end'";
      } else if ($date === 'monthly') {
        $month_days = date('t');
        $month_start = date('Y-m-01 00:00:00');
        $month_end = date("Y-m-$month_days 23:59:59");
        $query .= " AND stocks.date BETWEEN '$month_start' AND '$month_end'";
      } else if ($date === 'annual') {
        $year_start = date('Y-01-01 00:00:00');
        $year_end = date("Y-12-31 23:59:59");
        $query .= " AND stocks.date BETWEEN '$year_start' AND '$year_end'";
      }

      $query .= " GROUP BY products.name ORDER BY stocks.date DESC";
      $transactions_res = $conn->query($query);
      $transactions = [];
      while ($transaction = $transactions_res->fetch_object()) {
        $productid = $transaction->productid;
        $transactionitem = [
          'id' => $transaction->id,
          'quantity' => $transaction->quantity * -1,
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
    }

    $result['success'] = true;
    $result['message'] = '';
    $result['transactions'] = $transactions;
  }
}

echo json_encode($result);

?>
