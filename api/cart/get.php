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
  'cart' => null
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
    $cart_res = $conn->query(
      "SELECT
        carts.*,
        products.id AS productid, products.name, products.user AS productuser, products.category, products.description, products.price,
        users.id AS userid, users.name AS userfullname, users.username
      FROM carts
      JOIN products ON carts.product=products.id
      JOIN users ON products.user=users.id
      WHERE carts.user=$userid
      ORDER BY products.user DESC"
    );

    $cart = [];
    $imgpath = __DIR__ . '/../../userdata/products';
    while ($item = $cart_res->fetch_object()) {
      $productid = $item->productid;
      $imgres = glob("$imgpath/$productid-*.{jpg,jpeg,png}", GLOB_BRACE);
      $photos = [];
      foreach ($imgres as $img) {
        $basename = pathinfo($img, PATHINFO_BASENAME);
        $photos[] = "/userdata/products/$basename";
      }

      $cartitem = [
        'id' => $item->id,
        'user' => [
          'id' => $item->userid,
          'name' => $item->userfullname,
          'username' => $item->username
        ],
        'quantity' => $item->quantity,
        'date' => $item->date,
        'product' => [
          'id' => $item->productid,
          'name' => $item->name,
          'user' => $item->productuser,
          'category' => $item->category,
          'description' => $item->description,
          'price' => $item->price,
          'photos' => $photos
        ]
      ];

      $cart[] = $cartitem;
    }

    $result['success'] = true;
    $result['message'] = '';
    $result['cart'] = $cart;
  }
}

echo json_encode($result);

?>
