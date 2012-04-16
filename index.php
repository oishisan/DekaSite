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
	<body><center>';
		include 'config/top.php'; echo'</center>
		<table id="indexTable" width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td id="indexTableSide" valign="top" align="center" width="15%">';
				include 'config/side.php'; 
				echo'</td><td id="IndexTableTop" valign="top" align="Left">'; 
				include 'config/content.php'; 
				include 'config/footer.php';
ob_end_flush();
?>
