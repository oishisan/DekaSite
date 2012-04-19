<?php
ob_start();
session_start();
require 'config/core.php';
echo '
<html>
	<title>',entScape($ini['Other']['site.title']),'</title>
	<head>
	<style type="text/css">';
        include $ini['Other']['site.css'];
     	echo'</style></head>
	<body><div id="top">';
	include 'config/top.php'; 
	echo'</div><div id="side">';
	include 'config/side.php'; 
	echo'</div><div id="content">';
	include 'config/content.php';
	echo '</div>';
	include 'config/footer.php';
ob_end_flush();
?>
