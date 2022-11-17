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
  'stats' => null
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

    if ($usertype === 'admin' || $usertype === 'headadmin') {
      $total_users_res = $conn->query("SELECT COUNT(*) AS total FROM users");
      $total_users = $total_users_res->fetch_object()->total;

      $total_products_res = $conn->query("SELECT COALESCE(SUM(quantity), 0) AS total FROM transactions WHERE status='success'");
      $total_products = $total_products_res->fetch_object()->total;

      $total_orders_res = $conn->query("SELECT COUNT(DISTINCT(transaction_code)) AS total FROM transactions WHERE status='success'");
      $total_orders = $total_orders_res->fetch_object()->total;

      $current_day = date('w');
      $week_start = date('Y-m-d 00:00:00', strtotime("-$current_day days"));
      $week_end = date('Y-m-d 23:59:59', strtotime('+' . (6 - intval($current_day)) . ' days'));
      $weekly_res = $conn->query(
        "SELECT
          products.name,
          (
            SELECT COALESCE(SUM(transactions.amount), 0)
            FROM transactions
            WHERE
              transactions.product=products.id AND
              transactions.status='success' AND
              transactions.date BETWEEN '$week_start' AND '$week_end'
          ) AS earned
        FROM products
        ORDER BY earned DESC, products.created ASC LIMIT 20"
      );
      $weekly = [];

      while ($stat = $weekly_res->fetch_object()) {
        if (!$stat->name) continue;
        $weekly[] = [
          'name' => $stat->name,
          'earned' => floatval($stat->earned)
        ];
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['stats'] = [
        'totalProductsSold' => $total_products,
        'totalOrders' => $total_orders,
        'totalUsers' => $total_users,
        'weekly' => $weekly
      ];
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
