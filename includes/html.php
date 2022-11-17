<?php

function out_header ($title, $styles = [], $scripts = []) {
  ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="viewport-fit=cover, width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />

  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png" />
  <link rel="manifest" href="/site.webmanifest" />
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#ed5958" />
  <meta name="msapplication-TileColor" content="#ed5958" />
  <meta name="theme-color" content="#ffffff" />
  
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-title" content="BUGANA" />
  <meta name="apple-mobile-web-app-status-bar-style" content="light" />

  <link rel="stylesheet" href="/css/reset.css" />
  <link rel="stylesheet" href="/css/default.css" />

  <?php foreach ($styles as $style) { ?>
  <link rel="stylesheet" href="<?= $style ?>" />
  <?php } ?>

  <script src="/js/lib/jquery.min.js"></script>
  <script src="/js/lib/chart.min.js"></script>
  <script src="/js/default.js"></script>
  <?php foreach ($scripts as $script) { ?>
  <script src="<?= $script ?>"></script>
  <?php } ?>

  <title><?= $title ?></title>
</head>

<body>
<?php
  echo ob_get_clean();
}

function out_footer () {
  ob_start();
?>
</body>
</html>
<?php
  echo ob_get_clean();
}

?>
