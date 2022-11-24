<?php

$userid = !empty($_GET['id']) ? intval($_GET['id']) : 0;
$imgpath = __DIR__ . '/../../userdata/ids';
$imgres = glob("$imgpath/$userid.{jpg,jpeg,png}", GLOB_BRACE);
$result = '';

foreach ($imgres as $filename) {
  $result = $filename;
  break;
}

$mime = mime_content_type($result);
header("Content-Type: $mime");
readfile($result);

?>
