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
  'months' => []
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
      $month = intval(date('m'));
      $year = intval(date('Y'));
      $months = [];

      for ($i = 1; $i <= $month; $i++) {
        $il = strval($month);
        while (strlen($il) < 2) $il = "0$il";

        $month_time = strtotime("$year-$il-01");
        $month_start = date('Y-m-01 00:00:00', $month_time);
        $month_end = date('Y-m-t 23:59:59', $month_time);
        $month_res = $conn->query("SELECT
            COALESCE(SUM(amount), 0) AS sales,
            COALESCE(SUM(shipping), 0) AS shipping
          FROM transactions
          WHERE
            status='success' AND
            date BETWEEN '$month_start' AND '$month_end'"
        );

        $months[] = $month_res->fetch_object()->sales;
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['months'] = $months;
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
