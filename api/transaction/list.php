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
  'transactions' => []
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
    $is_admin = $usertype === 'admin' || $usertype === 'headadmin';
    $userid = !empty($_GET['id']) && $is_admin ? $conn->real_escape_string($_GET['id']) : $decoded->userid;
    $type = !empty($_GET['type']) ? $conn->real_escape_string($_GET['type']) : null;
    $pending = isset($_GET['pending']);
    $farmer = isset($_GET['farmer']);

    $query = "SELECT
        transactions.*,
        products.id AS productid, products.name, products.description, products.user AS farmerid,
        (SELECT users.name FROM users WHERE users.id=farmerid) AS userfullname,
        (SELECT users.username FROM users WHERE users.id=farmerid) AS username,
        (SELECT users.addressstreet FROM users WHERE users.id=farmerid) AS addressstreet,
        (SELECT users.addresspurok FROM users WHERE users.id=farmerid) AS addresspurok,
        (SELECT users.addressbrgy FROM users WHERE users.id=farmerid) AS addressbrgy
      FROM transactions
      JOIN products ON products.id=transactions.product
      WHERE transactions.user=$userid";

    if ($farmer) {
      $query = "SELECT
          transactions.*,
          products.id AS productid, products.name, products.description, products.user AS farmerid,
          (SELECT users.name FROM users WHERE users.id=transactions.user) AS userfullname,
          (SELECT users.username FROM users WHERE users.id=transactions.user) AS username,
          (SELECT users.addressstreet FROM users WHERE users.id=transactions.user) AS addressstreet,
          (SELECT users.addresspurok FROM users WHERE users.id=transactions.user) AS addresspurok,
          (SELECT users.addressbrgy FROM users WHERE users.id=transactions.user) AS addressbrgy
        FROM products
        JOIN transactions ON transactions.product=products.id
        WHERE products.user=$userid";
    }

    if ($type) $query .= " AND transactions.paymentoption='$type'";
    if ($pending && !$farmer) $query .= " AND transactions.status<>'success'";
    $query .= " ORDER BY transactions.date DESC";
    $transactions_res = $conn->query($query);

    $transactions = [];
    $imgpath = __DIR__ . '/../../userdata/products';
    while ($transaction = $transactions_res->fetch_object()) {
      $productid = $transaction->productid;
      $imgres = glob("$imgpath/$productid-*.{jpg,jpeg,png}", GLOB_BRACE);
      $photos = [];
      foreach ($imgres as $img) {
        $basename = pathinfo($img, PATHINFO_BASENAME);
        $photos[] = "/userdata/products/$basename";
      }

      $transactionitem = [
        'id' => $transaction->id,
        'code' => $transaction->transaction_code,
        'user' => [
          'id' => $transaction->farmerid,
          'name' => $transaction->userfullname,
          'username' => $transaction->username,
          'addressstreet' => $transaction->addressstreet,
          'addresspurok' => $transaction->addresspurok,
          'addressbrgy' => $transaction->addressbrgy
        ],
        'product' => [
          'id' => $transaction->productid,
          'name' => $transaction->name,
          'description' => $transaction->description,
          'user' => $transaction->farmerid,
          'photos' => $photos
        ],
        'quantity' => $transaction->quantity,
        'date' => $transaction->date,
        'shipping' => $transaction->shipping,
        'amount' => $transaction->amount,
        'paymentoption' => $transaction->paymentoption,
        'status' => $transaction->status
      ];

      $transactions[] = $transactionitem;
    }

    $result['success'] = true;
    $result['message'] = '';
    $result['transactions'] = $transactions;
  }
}

echo json_encode($result);

?>
