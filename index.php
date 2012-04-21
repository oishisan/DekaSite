<?php
ob_start();
session_start();
require 'config/core.php';
echo '<html><title>',entScape($ini['Other']['site.title']),'</title><head>
<link rel="stylesheet" type="text/css" href="',entScape($ini['Other']['site.css']),'" /></head>
<body><div id="mContain"><div id="top">';
include 'config/top.php'; 
echo'</div><div id="side">';
include 'config/side.php'; 
echo'</div><div id="content">';
include 'config/content.php';
echo '</div></div>';
include 'config/footer.php';
ob_end_flush();
?>
