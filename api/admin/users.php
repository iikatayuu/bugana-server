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
  'prev' => false
];

if (!empty($_GET['token'])) {
  $decoded = null;
  $page = isset($_GET['page']) ? $_GET['page'] : '1';
  $view = isset($_GET['view']) ? $_GET['view'] : 'all';

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
      $limit = 20;
      if (!preg_match('/^(\d+)$/', $page)) {
        $result['message'] = 'Invalid page';
        die(json_encode($result));
      }

      $query = "SELECT * FROM users WHERE ";
      $count_query = "SELECT COUNT(*) AS count FROM users WHERE ";
      $add_q = '';

      if ($view === 'all') $add_q = "type!='admin' AND type!='headadmin'";
      else if ($view === 'customers') $add_q = "type='customer'";
      else if ($view === 'farmers') $add_q = "type='farmer'";
      else {
        $result['message'] = 'Invalid view';
        die(json_encode($result));
      }

      $query .= $add_q;
      $count_query .= $add_q;

      $page_q = (intval($page) - 1) * $limit;
      $users_res = $conn->query("$query ORDER BY created DESC LIMIT $page_q, $limit");
      $count_res = $conn->query($count_query);
      $count = $count_res->fetch_object()->count;
      $users = [];
      $expose = [
        'id', 'username', 'email', 'mobile', 'name', 'gender', 'birthday',
        'addressstreet', 'addressbrgy', 'addresscity', 'type', 'created', 'lastlogin'
      ];

      while ($user = $users_res->fetch_object()) {
        $exposed = [];
        foreach ($expose as $prop) $exposed[$prop] = $user->{$prop};
        $prefix = $user->type === 'customer' ? 'C' : 'F';
        $code = $user->id;
        while (strlen($code) < 2) $code = "0$code";
        $exposed['code'] = $prefix . $code;
        $users[] = $exposed;
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['users'] = $users;
      $result['next'] = ($page_q + $limit) < $count;
      $result['prev'] = $page !== '1';
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
