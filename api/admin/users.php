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
  'users' => null,
  'next' => false,
  'prev' => false,
  'pages' => 0
];

$query = <<<EOD
SELECT
  users.*,
  (
    SELECT COALESCE(SUM(t.amount), 0)
    FROM (
      SELECT
        products.id,
        (
          SELECT COALESCE(SUM(transactions.amount), 0)
          FROM transactions WHERE transactions.product=products.id AND transactions.status='success'
        ) AS amount
      FROM products WHERE products.user=users.id
    ) t
  ) AS sales
FROM users WHERE 
EOD;

if (!empty($_GET['token'])) {
  $decoded = null;
  $page = !empty($_GET['page']) ? $conn->real_escape_string($_GET['page']) : '1';
  $limit = !empty($_GET['limit']) ? $conn->real_escape_string($_GET['limit']) : '10';
  $view = !empty($_GET['view']) ? $conn->real_escape_string($_GET['view']) : 'all';
  $user = !empty($_GET['user']) ? $conn->real_escape_string($_GET['user']) : null;
  $usersort = !empty($_GET['user_sort']) ? $conn->real_escape_string($_GET['user_sort']) : null;
  $salessort = !empty($_GET['sales_sort']) ? $conn->real_escape_string($_GET['sales_sort']) : null;

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
    if ($usertype === 'admin' || $usertype === 'headadmin') {
      if (!preg_match('/^(\d+)$/', $page)) {
        $result['message'] = 'Invalid page';
        die(json_encode($result));
      }

      if ($user && !preg_match('/^((F|C)\d{2})$/', $user)) {
        $result['message'] = 'Invalid farmer code';
        die(json_encode($result));
      }

      $count_query = "SELECT COUNT(*) AS count FROM users WHERE ";
      $add_q = '';
      $wheres = [];

      if ($view === 'all') $wheres[] = "type!='admin' AND type!='headadmin'";
      else if ($view === 'customers') $wheres[] = "type='customer'";
      else if ($view === 'farmers') $wheres[] = "type='farmer'";
      else {
        $result['message'] = 'Invalid view';
        die(json_encode($result));
      }

      if ($user) {
        $code = substr($user, 0, 1);
        $usertype = $code === 'F' ? 'farmer' : 'customer';
        $where = "type='$usertype'";
        $userid = intval(substr($user, 1, 2));
        $wheres[] = "id=$userid";
        if (!in_array($where, $wheres)) $wheres[] = $where;
      }

      $add_q = implode(' AND ', $wheres);
      $query .= $add_q;
      $count_query .= $add_q;

      $orders = [];
      if ($salessort && $salessort === 'asc') $orders[] = 'sales ASC';
      if ($salessort && $salessort === 'desc') $orders[] = 'sales DESC';
      if ($usersort && $usersort === 'asc') $orders[] = 'name ASC';
      if ($usersort && $usersort === 'desc') $orders[] = 'name DESC';
      $add_q = count($orders) > 0 ? implode(', ', $orders) : 'created DESC';
      $query .= " ORDER BY $add_q";

      $page_q = (intval($page) - 1) * intval($limit);
      $users_res = $conn->query("$query LIMIT $page_q, $limit");
      $result['debug'] = "$query LIMIT $page_q, $limit";
      $count_res = $conn->query($count_query);
      $count = $count_res->fetch_object()->count;
      $users = [];
      $expose = [
        'id', 'username', 'email', 'mobile', 'name', 'gender', 'birthday',
        'addressstreet', 'addresspurok', 'addressbrgy', 'type', 'created', 'lastlogin'
      ];

      if (!empty($_GET['sales'])) $expose[] = 'sales';

      while ($user = $users_res->fetch_object()) {
        $exposed = [];
        foreach ($expose as $prop) $exposed[$prop] = $user->{$prop};
        $prefix = $user->type === 'customer' ? 'C' : 'F';
        $userid = $user->id;
        $code = $user->id;
        while (strlen($code) < 2) $code = "0$code";
        $exposed['code'] = $prefix . $code;
        $users[] = $exposed;
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['users'] = $users;
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
