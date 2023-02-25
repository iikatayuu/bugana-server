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
  'sales' => []
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
    if ($usertype === 'headadmin' || $usertype === 'admin') {
      $date = !empty($_POST['date']) ? $conn->real_escape_string($_POST['date']) : 'weekly';
      $prev = isset($_POST['prev']);

      $query = "SELECT
          COALESCE(SUM(amount), 0) AS sales,
          COALESCE(SUM(shipping), 0) AS shipping
        FROM transactions WHERE status='success'";

      $perished_query = "SELECT COALESCE(SUM(amount)) AS amount FROM stocks WHERE status='perished'";

      if ($date === 'daily') {
        $ts = $prev ? strtotime('-1 day') : gmtime();
        $day_start = date('Y-m-d 00:00:00', $ts);
        $day_end = date('Y-m-d 23:59:59', $ts);
        $add_query = " AND date BETWEEN '$day_start' AND '$day_end'";
        $query .= $add_query;
        $perished_query .= $add_query;
      } else if ($date === 'weekly') {
        $ts = $prev ? strtotime('-1 week') : gmtime();
        $current_day = date('w');
        $week_start = date('Y-m-d 00:00:00', strtotime("-$current_day days", $ts));
        $week_end = date('Y-m-d 23:59:59', strtotime('+' . (6 - intval($current_day)) . ' days', $ts));
        $add_query = " AND date BETWEEN '$week_start' AND '$week_end'";
        $query .= $add_query;
        $perished_query .= $add_query;
      } else if ($date === 'monthly') {
        $ts = $prev ? strtotime('-1 month') : gmtime();
        $month_days = date('t', $ts);
        $month_start = date('Y-m-01 00:00:00', $ts);
        $month_end = date("Y-m-$month_days 23:59:59", $ts);
        $add_query = " AND date BETWEEN '$month_start' AND '$month_end'";
        $query .= $add_query;
        $perished_query .= $add_query;
      } else if ($date === 'annual') {
        $ts = $prev ? strtotime('-1 year') : gmtime();
        $year_start = date('Y-01-01 00:00:00', $ts);
        $year_end = date("Y-12-31 23:59:59", $ts);
        $add_query = " AND date BETWEEN '$year_start' AND '$year_end'";
        $query .= $add_query;
        $perished_query .= $add_query;
      }

      $total_sales_res = $conn->query($query);
      $total_sales = $total_sales_res->fetch_object();

      $perished_res = $conn->query($perished_query);
      $perished = $perished_res->fetch_object();

      $total = floatval($total_sales->sales);
      $delivery = floatval($total_sales->shipping);
      $unsold = floatval($perished->amount);
      $grandtotal = $total + $delivery;

      $result['success'] = true;
      $result['message'] = '';
      $result['sales'] = [
        'total' => $total,
        'delivery' => $delivery,
        'unsold' => $unsold,
        'grandtotal' => $grandtotal
      ];
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
