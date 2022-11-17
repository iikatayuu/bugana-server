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
    $last_month = strtotime('-1 month');
    $last_month_days = date('t', $last_month);
    $last_month_start = date('Y-m-01 00:00:00', $last_month);
    $last_month_end = date("Y-m-$last_month_days 23:59:59", $last_month);
    $last_query = "SELECT
      (
        SELECT COALESCE(SUM(transactions.amount), 0) FROM transactions
        WHERE
          transactions.product=products.id AND
          transactions.status='success' AND
          transactions.date BETWEEN '$last_month_start' AND '$last_month_end'
      ) AS income
      FROM products WHERE products.user=$userid
    ";

    $last_month_res = $conn->query($last_query);
    $last_income = 0;
    while ($last_month = $last_month_res->fetch_object()) $last_income += floatval($last_month->income);

    $month_days = date('t');
    $month_start = date('Y-m-01 00:00:00');
    $month_end = date("Y-m-$month_days 23:59:59");
    $query = "SELECT
      (
        SELECT COALESCE(SUM(transactions.amount), 0) FROM transactions
        WHERE
          transactions.product=products.id AND
          transactions.status='success' AND
          transactions.date BETWEEN '$month_start' AND '$month_end'
      ) AS income
      FROM products WHERE products.user=$userid
    ";

    $month_res = $conn->query($query);
    $current_income = 0;
    while ($month = $month_res->fetch_object()) $current_income += floatval($month->income);

    $result['success'] = true;
    $result['message'] = '';
    $result['currentMonth'] = $current_income;
    $result['lastMonth'] = $last_income;
  }
}

echo json_encode($result);

?>
