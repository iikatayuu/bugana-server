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

$query = <<<EOD
SELECT
  products.name,
  (
    SELECT COALESCE(SUM(transactions.amount), 0)
    FROM transactions
    WHERE
      transactions.product=products.id AND
      transactions.status='success' AND
      transactions.date BETWEEN '%DATE_START%' AND '%DATE_END%'
  ) AS earned
  FROM products
  ORDER BY earned DESC, products.created ASC LIMIT 20
EOD;

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

      $total_products_unsold_res = $conn->query("SELECT COALESCE(SUM(quantity), 0) AS total FROM stocks WHERE status='perished'");
      $total_products_unsold = $total_products_unsold_res->fetch_object()->total;

      $total_orders_res = $conn->query("SELECT COUNT(DISTINCT(transaction_code)) AS total FROM transactions WHERE status='success'");
      $total_orders = $total_orders_res->fetch_object()->total;

      $new_users_res = $conn->query("SELECT * FROM users WHERE type='customer' ORDER BY created LIMIT 10");
      $new_users = [];
      while ($user = $new_users_res->fetch_object()) {
        $new_users[] = [
          'id' => $user->id,
          'name' => $user->name
        ];
      }

      $weekly = [];
      $monthly = [];
      $yearly = [];

      $current_day = date('w');
      $week_start = date('Y-m-d 00:00:00', strtotime("-$current_day days"));
      $week_end = date('Y-m-d 23:59:59', strtotime('+' . (6 - intval($current_day)) . ' days'));
      $weekly_query = str_replace(['%DATE_START%', '%DATE_END%'], [$week_start, $week_end], $query);
      $weekly_res = $conn->query($weekly_query);

      while ($stat = $weekly_res->fetch_object()) {
        if (!$stat->name) continue;
        $weekly[] = [
          'name' => $stat->name,
          'earned' => floatval($stat->earned)
        ];
      }

      $day_month = date('t');
      $month_start = date('Y-m-01 00:00:00');
      $month_end = date("Y-m-$day_month 23:59:59");
      $monthly_query = str_replace(['%DATE_START%', '%DATE_END%'], [$month_start, $month_end], $query);
      $monthly_res = $conn->query($monthly_query);

      while ($stat = $monthly_res->fetch_object()) {
        if (!$stat->name) continue;
        $monthly[] = [
          'name' => $stat->name,
          'earned' => floatval($stat->earned)
        ];
      }

      $year_start = date('Y-01-01 00:00:00');
      $year_end = date('Y-12-31 23:59:59');
      $yearly_query = str_replace(['%DATE_START%', '%DATE_END%'], [$year_start, $year_end], $query);
      $yearly_res = $conn->query($yearly_query);

      while ($stat = $yearly_res->fetch_object()) {
        if (!$stat->name) continue;
        $yearly[] = [
          'name' => $stat->name,
          'earned' => floatval($stat->earned)
        ];
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['stats'] = [
        'totalProductsSold' => $total_products,
        'totalProductsUnsold' => intval($total_products_unsold) * -1,
        'totalOrders' => $total_orders,
        'totalUsers' => $total_users,
        'users' => $new_users,
        'weekly' => $weekly,
        'monthly' => $monthly,
        'yearly' => $yearly
      ];
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
