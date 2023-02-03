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
  'users' => null,
  'next' => false,
  'prev' => false,
  'pages' => 0
];

$query = <<<EOD
SELECT
  users.*,
  (
    SELECT COUNT(violations.id) FROM violations WHERE violations.user=users.id
  ) AS counts
FROM users WHERE type='customer' ORDER BY counts DESC
EOD;

$count_query = "SELECT COUNT(*) AS count FROM users WHERE type='customer'";

if (!empty($_GET['token'])) {
  $decoded = null;
  $page = !empty($_GET['page']) ? $conn->real_escape_string($_GET['page']) : '1';
  $limit = !empty($_GET['limit']) ? $conn->real_escape_string($_GET['limit']) : '10';

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

      $page_q = (intval($page) - 1) * intval($limit);
      $users_res = $conn->query("$query LIMIT $page_q, $limit");
      $count_res = $conn->query($count_query);
      $count = $count_res->fetch_object()->count;
      $users = [];
      $expose = [
        'id', 'code', 'username', 'email', 'mobile', 'name',
        'addressstreet', 'addresspurok', 'addressbrgy', 'type', 'created', 'lastlogin',
        'counts', 'active'
      ];

      while ($user = $users_res->fetch_object()) {
        $exposed = [];
        foreach ($expose as $prop) $exposed[$prop] = $user->{$prop};
        $exposed['transaction'] = null;

        $userid = $user->id;
        $last_transaction_res = $conn->query(
          "SELECT
            violations.id,
            violations.transaction_code,
            transactions.id AS trans_id
          FROM violations
          INNER JOIN transactions ON transactions.transaction_code=violations.transaction_code
          WHERE violations.user=$userid ORDER BY violations.id DESC LIMIT 1"
        );

        if ($last_transaction_res->num_rows > 0) {
          $last_transaction = $last_transaction_res->fetch_object();
          $exposed['transaction'] = [
            'transaction_id' => $last_transaction->trans_id,
            'transaction_code' => $last_transaction->transaction_code
          ];
        }

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
