<?php

require_once '../vendor/autoload.php';
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/utils.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
cors();

$result = [
  'success' => false,
  'message' => 'Invalid request'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $POST = !empty($_SERVER['CONTENT_TYPE']) && explode(';', $_SERVER['CONTENT_TYPE'])[0] === 'application/json'
    ? parse_jsondata()
    : $_POST;

  $username = $conn->real_escape_string($POST['username']);
  $user_res = $conn->query("SELECT * FROM users WHERE username='$username' LIMIT 1");

  if ($user_res->num_rows > 0) {
    $user = $user_res->fetch_object();
    $userid = $user->id;
    $email = $user->email;
    $name = $user->name;
    $temp_pass = random_str(8);
    $hash = password_hash($temp_pass, PASSWORD_BCRYPT);
    $conn->query("UPDATE users SET temp_password='$hash' WHERE id=$userid");

    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = SMTP_HOST;
      $mail->SMTPAuth = true;
      $mail->Username = SMTP_USER;
      $mail->Password = SMTP_PASS;
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port = 465;

      $mail->setFrom(SMTP_FROM, 'BUGANA');
      $mail->addAddress($email, $name);

      $mail->Subject = 'Temporary Password';
      $mail->Body = "Hi, $name!\nHere is your temporary password: $temp_pass";

      $mail->send();
      $result['success'] = true;
      $result['message'] = '';
    } catch (Exception $e) {
      $result['message'] = 'Unable to send message';
    }
  } else {
    $result['message'] = 'Username not found.';
  }
}

echo json_encode($result);

?>
