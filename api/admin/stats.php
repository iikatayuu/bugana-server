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
  SUM(
    (
      SELECT COALESCE(SUM(transactions.quantity), 0)
      FROM transactions
      WHERE
        transactions.product=products.id AND
        transactions.status='success' AND
        transactions.date BETWEEN '%DATE_START%' AND '%DATE_END%'
    )
  ) AS quantities
  FROM products
  GROUP BY products.name
  ORDER BY quantities DESC, products.created ASC LIMIT 20
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
      $weekly_date_start = date('M d', strtotime("-$current_day days"));
      $weekly_date_end = date('M d', strtotime('+' . (6 - intval($current_day)) . ' days'));
      $weekly_date = "$weekly_date_start - $weekly_date_end";
      $weekly_query = str_replace(['%DATE_START%', '%DATE_END%'], [$week_start, $week_end], $query);
      $weekly_res = $conn->query($weekly_query);

      while ($stat = $weekly_res->fetch_object()) {
        if (!$stat->name) continue;
        $weekly[] = [
          'name' => $stat->name,
          'quantities' => intval($stat->quantities)
        ];
      }

      $day_month = date('t');
      $month_start = date('Y-m-01 00:00:00');
      $month_end = date("Y-m-$day_month 23:59:59");
      $monthly_date = date('M Y');
      $monthly_query = str_replace(['%DATE_START%', '%DATE_END%'], [$month_start, $month_end], $query);
      $monthly_res = $conn->query($monthly_query);

      while ($stat = $monthly_res->fetch_object()) {
        if (!$stat->name) continue;
        $monthly[] = [
          'name' => $stat->name,
          'quantities' => intval($stat->quantities)
        ];
      }

      $year_start = date('Y-01-01 00:00:00');
      $year_end = date('Y-12-31 23:59:59');
      $yearly_date = date('Y');
      $yearly_query = str_replace(['%DATE_START%', '%DATE_END%'], [$year_start, $year_end], $query);
      $yearly_res = $conn->query($yearly_query);

      while ($stat = $yearly_res->fetch_object()) {
        if (!$stat->name) continue;
        $yearly[] = [
          'name' => $stat->name,
          'quantities' => intval($stat->quantities)
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
        'weeklyDate' => $weekly_date,
        'monthly' => $monthly,
        'monthlyDate' => $monthly_date,
        'yearly' => $yearly,
        'yearlyDate' => $yearly_date
      ];
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
