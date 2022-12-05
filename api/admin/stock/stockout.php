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
  'stocks' => [],
  'next' => false,
  'prev' => false,
  'pages' => 0
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
    $usertype = $decoded->type;
    if ($usertype === 'admin' || $usertype === 'headadmin') {
      $page = !empty($_GET['page']) ? $_GET['page'] : '1';
      $limit = !empty($_GET['limit']) ? $conn->real_escape_string($_GET['limit']) : '10';
      $search = !empty($_GET['search']) ? $conn->real_escape_string($_GET['search']) : null;

      if (!preg_match('/^(\d+)$/', $page)) {
        $result['message'] = 'Invalid page';
        die(json_encode($result));
      }

      $count_query = "SELECT COUNT(*) AS count FROM stocks";
      $query = "SELECT
          stocks.*,
          products.user AS farmerid,
          products.name AS productname,
          products.category
        FROM stocks
        JOIN products ON products.id=stocks.product";

      $wheres = ['stocks.quantity < 0'];
      if ($search) $wheres[] = "stocks.transaction_code LIKE '%$search%'";

      $add_q = ' WHERE ' . implode(' AND ', $wheres);
      $query .= $add_q;
      $count_query .= $add_q;

      $page_q = (intval($page) - 1) * intval($limit);
      $stocks_res = $conn->query("$query ORDER BY stocks.date DESC LIMIT $page_q, $limit");
      $count_res = $conn->query($count_query);
      $count = $count_res->fetch_object()->count;
      $stocks = [];

      while ($stock = $stocks_res->fetch_object()) {
        $farmerid = $stock->farmerid;
        $stockitem = [
          'id' => $stock->id,
          'transaction_code' => $stock->transaction_code,
          'quantity' => $stock->quantity,
          'date' => $stock->date,
          'revenue' => $stock->amount,
          'status' => $stock->status,
          'product' => [
            'name' => $stock->productname,
            'user' => $farmerid,
            'category' => $stock->category,
            'price' => (float) floatval($stock->amount) / floatval($stock->quantity) * -1
          ]
        ];

        $stocks[] = $stockitem;
      }

      $result['success'] = true;
      $result['message'] = '';
      $result['stocks'] = $stocks;
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
