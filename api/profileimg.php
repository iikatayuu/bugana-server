<?php

$userid = !empty($_GET['id']) ? intval($_GET['id']) : 0;
$imgpath = __DIR__ . '/../userdata/imgs';
$imgres = glob("$imgpath/$userid.{jpg,jpeg,png}", GLOB_BRACE);
$result = "$imgpath/0.png";

foreach ($imgres as $filename) {
  $result = $filename;
  break;
}

$mime = mime_content_type($result);
header("Content-Type: $mime");
readfile($result);

?>
