<?php

$config_json = file_get_contents(__DIR__ . '/../config.json');
$config = json_decode($config_json);

$conn = null;
$conn_host = $config->mysql->host;
$conn_user = $config->mysql->user;
$conn_pass = $config->mysql->pass;
$conn_db = $config->mysql->db;
$conn_port = $config->mysql->port;
$conn_ssl = $config->mysql->ssl;

if ($conn_ssl) {
  $conn = mysqli_init();
  $conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
  $conn->ssl_set(NULL, NULL, $conn_ssl, NULL, NULL);
  $conn->real_connect($conn_host, $conn_user, $conn_pass, $conn_db, $conn_port);
} else {
  $conn = new mysqli($conn_host, $conn_user, $conn_pass, $conn_db, $conn_port);
}

?>
