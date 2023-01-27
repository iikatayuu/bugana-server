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
  'transactions' => [],
  'next' => false,
  'prev' => false,
  'pages' => 0
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
    $usertype = $decoded->type;
    if ($usertype === 'headadmin') {
      $page = !empty($_GET['page']) ? $_GET['page'] : '1';
      $limit = !empty($_GET['limit']) ? $conn->real_escape_string($_GET['limit']) : '10';
      $category = !empty($_GET['category']) ? $conn->real_escape_string($_GET['category']) : 'all';
      $search = !empty($_GET['search']) ? $conn->real_escape_string($_GET['search']) : null;
      $datesort = !empty($_GET['date_sort']) ? $conn->real_escape_string($_GET['date_sort']) : null;

      if (!preg_match('/^(\d+)$/', $page)) {
        $result['message'] = 'Invalid page';
        die(json_encode($result));
      }

      $count_query = "SELECT COUNT(DISTINCT transaction_code) AS count FROM transactions";
      $query = "SELECT
          transactions.*,
          users.id AS userid,
          users.code AS usercode,
          users.name AS userfullname,
          users.username AS username,
          users.addressstreet AS addressstreet,
          users.addresspurok AS addresspurok,
          users.addressbrgy AS addressbrgy,
          COALESCE(SUM(transactions.amount), 0) AS total_amount
        FROM transactions
        JOIN users ON users.id=transactions.user";

      $wheres = [];
      if ($category !== 'all' && $category !== '') $wheres[] = "transactions.status='$category'";
      if ($search) $wheres[] = "transactions.transaction_code LIKE '%$search%'";

      $add_q = count($wheres) > 0 ? ' WHERE ' . implode(' AND ', $wheres) : '';
      $query .= $add_q;
      $count_query .= $add_q;

      $orders = [];
      if ($datesort && $datesort === 'asc') $orders[] = 'transactions.date ASC';
      if ($datesort && $datesort === 'desc') $orders[] = 'transactions.date DESC';

      $query .= '  GROUP BY transactions.transaction_code ';
      $add_q = ' ORDER BY ' . (count($orders) > 0 ? implode(', ', $orders) : 'transactions.date DESC');
      $query .= $add_q;

      $page_q = (intval($page) - 1) * intval($limit);
      $transactions_res = $conn->query("$query LIMIT $page_q, $limit");
      $count_res = $conn->query($count_query);
      $count = $count_res->fetch_object()->count;
      $transactions = [];

      while ($transaction = $transactions_res->fetch_object()) {
        $transactionitem = [
          'id' => $transaction->id,
          'code' => $transaction->transaction_code,
          'user' => [
            'id' => $transaction->userid,
            'code' => $transaction->usercode,
            'name' => $transaction->userfullname,
            'username' => $transaction->username,
            'addressstreet' => $transaction->addressstreet,
            'addresspurok' => $transaction->addresspurok,
            'addressbrgy' => $transaction->addressbrgy
          ],
          'quantity' => $transaction->quantity,
          'date' => $transaction->date,
          'amount' => $transaction->amount,
          'total_amount' => $transaction->total_amount,
          'paymentoption' => $transaction->paymentoption,
          'status' => $transaction->status
        ];

        $transactions[] = $transactionitem;
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['transactions'] = $transactions;
      $result['next'] = ($page_q + intval($limit)) < $count;
      $result['prev'] = $page !== '1';
      $result['pages'] = ceil($count / intval($limit));
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
