<?php

$config_json = file_get_contents(__DIR__ . '/../config.json');
$config = json_decode($config_json);

define('WEBURL', $config->weburl);
define('JWT_KEY', $config->jwt->key);
define('JWT_ALGO', $config->jwt->algo);
define('DAYS_TO_NOTIFY', $config->days_to_notify);

define('SMTP_HOST', $config->smtp->host);
define('SMTP_USER', $config->smtp->username);
define('SMTP_PASS', $config->smtp->password);
define('SMTP_FROM', $config->smtp->from);

?>
