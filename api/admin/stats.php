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

function sort_top ($a, $b) {
  $an = $a['earnings'];
  $bn = $b['earnings'];
  return $bn - $an;
}

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
      $total_customers_res = $conn->query("SELECT COUNT(*) AS total FROM users WHERE type='customer'");
      $total_customers = $total_customers_res->fetch_object()->total;
      $total_farmers_res = $conn->query("SELECT COUNT(*) AS total FROM users WHERE type='farmer'");
      $total_farmers = $total_farmers_res->fetch_object()->total;

      $current_day = date('w');
      $day_start = date('Y-m-d 00:00:00');
      $day_end = date('Y-m-d 23:59:59');
      $week_start = date('Y-m-d 00:00:00', strtotime("-$current_day days"));
      $week_end = date('Y-m-d 23:59:59', strtotime('+' . (6 - intval($current_day)) . ' days'));

      $total_products_res = $conn->query("SELECT COALESCE(SUM(quantity), 0) AS total FROM transactions WHERE status='success' AND date BETWEEN '$day_start' AND '$day_end'");
      $total_products = $total_products_res->fetch_object()->total;
      $total_products_week_res = $conn->query("SELECT COALESCE(SUM(quantity), 0) AS total FROM transactions WHERE status='success' AND date BETWEEN '$week_start' AND '$week_end'");
      $total_products_week = $total_products_week_res->fetch_object()->total;

      $total_products_unsold_res = $conn->query("SELECT COALESCE(SUM(quantity), 0) AS total FROM stocks WHERE status='perished' AND date BETWEEN '$day_start' AND '$day_end'");
      $total_products_unsold = $total_products_unsold_res->fetch_object()->total;
      $total_products_unsold_week_res = $conn->query("SELECT COALESCE(SUM(quantity), 0) AS total FROM stocks WHERE status='perished' AND date BETWEEN '$week_start' AND '$week_end'");
      $total_products_unsold_week = $total_products_unsold_week_res->fetch_object()->total;

      $total_orders_res = $conn->query("SELECT COUNT(DISTINCT(transaction_code)) AS total FROM transactions WHERE status='success' AND date BETWEEN '$day_start' AND '$day_end'");
      $total_orders = $total_orders_res->fetch_object()->total;
      $total_orders_week_res = $conn->query("SELECT COUNT(DISTINCT(transaction_code)) AS total FROM transactions WHERE status='success' AND date BETWEEN '$week_start' AND '$week_end'");
      $total_orders_week = $total_orders_week_res->fetch_object()->total;

      $new_users_res = $conn->query("SELECT * FROM users WHERE type='customer' ORDER BY created DESC LIMIT 3");
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

      $prod_res = $conn->query("SELECT
          products.*,
          users.code,
          COALESCE(SUM(stocks.quantity), 0) AS stocks
        FROM products
        JOIN stocks ON stocks.product=products.id
        JOIN users ON users.id=products.user
        GROUP BY products.id
        ORDER BY stocks ASC
        LIMIT 3
      ");

      $restocks = [];
      while ($prod = $prod_res->fetch_object()) {
        $productname = $prod->name;
        if ($prod->stocks < 5) {
          $restocks[] = [
            'name' => $prod->name,
            'stocks' => $prod->stocks
          ];
        }
      }

      $top_users = [];
      $farmers_res = $conn->query("SELECT * FROM users WHERE type='farmer'");
      while ($farmer = $farmers_res->fetch_object()) {
        $farmerid = $farmer->id;
        $total_sales = 0;
        $products = [];
        $products_res = $conn->query(
          "SELECT
            products.*,
            COALESCE(SUM(transactions.amount), 0) AS sales
          FROM products
          JOIN transactions ON transactions.product=products.id
          WHERE products.user=$farmerid
          GROUP BY products.id
          ORDER BY sales DESC"
        );

        while ($product = $products_res->fetch_object()) {
          $total_sales += floatval($product->sales);

          $imgpath = __DIR__ . '/../../userdata/products';
          $productid = $product->id;
          $imgres = glob("$imgpath/$productid-*.{jpg,jpeg,png}", GLOB_BRACE);
          $photos = [];

          foreach ($imgres as $img) {
            $basename = pathinfo($img, PATHINFO_BASENAME);
            $photos[] = "/userdata/products/$basename";
          }

          $products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'photos' => $photos
          ];
        }

        $top_users[] = [
          'id' => $farmer->id,
          'code' => $farmer->code,
          'name' => $farmer->name,
          'earnings' => $total_sales,
          'products' => $products
        ];
      }

      usort($top_users, 'sort_top');
      $result['success'] = true;
      $result['message'] = '';
      $result['stats'] = [
        'totalProductsSold' => [
          'day' => $total_products,
          'week' => $total_products_week
        ],
        'totalProductsUnsold' => [
          'day' => intval($total_products_unsold) * -1,
          'week' => intval($total_products_unsold_week) * -1
        ],
        'totalOrders' => [
          'day' => $total_orders,
          'week' => $total_orders_week
        ],
        'topUsers' => $top_users,
        'restocks' => $restocks,
        'totalCustomers' => $total_customers,
        'totalFarmers' => $total_farmers,
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
