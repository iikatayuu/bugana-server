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
  'notifications' => []
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
    $userid = $decoded->userid;
    $usertype = $decoded->type;

    if ($usertype === 'admin' || $usertype === 'headadmin') {
      $notifications = [];
      $days_to_notify = DAYS_TO_NOTIFY;
      $notis_res = $conn->query("SELECT
          stocks.*,
          products.name AS productname,
          products.perish - DATEDIFF(NOW(), stocks.date) AS days_left,
          users.code,
          users.name AS userfullname
        FROM stocks
        JOIN products ON
          products.id = stocks.product AND
          DATEDIFF(NOW(), stocks.date) < products.perish AND
          products.perish - DATEDIFF(NOW(), stocks.date) <= $days_to_notify
        JOIN users ON users.id = products.user
        WHERE stocks.stocks > 0 AND stocks.quantity > 0;
      ");

      while ($noti = $notis_res->fetch_object()) {
        $productname = $noti->productname;
        $username = $noti->userfullname;
        $days_left = $noti->days_left;
        $notifications[] = [
          'message' => "$username: \"$productname\" is perishing in $days_left days"
        ];
      }

      $notis_res = $conn->query("SELECT
          products.*,
          COALESCE(SUM(stocks.quantity), 0) AS stocks,
          users.code,
          users.name AS userfullname
        FROM products
        JOIN stocks ON stocks.product=products.id
        JOIN users ON users.id=products.user
        GROUP BY products.id"
      );

      while ($noti = $notis_res->fetch_object()) {
        $productname = $noti->name;
        $username = $noti->userfullname;

        if ($noti->stocks < 5) {
          $notifications[] = [
            'message' => "$username: \"$productname\" needs restocking"
          ];
        }
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['notifications'] = $notifications;
    } else {
      $result['message'] = 'User is not admin';
    }
  }
}

echo json_encode($result);

?>
