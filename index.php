<?php
ob_start();
session_start();
require 'config/core.php';
echo '
<html>
	<title>DekaSite</title>
	<head></head>
	<body><center>';
		include 'config/top.php'; echo'</center>
		<table style="border-top: 1px solid black;" width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td style="border-right:1px solid black;" valign="top" align="center" width="15%">'; include 'config/side.php'; echo'</td>
				<td style="padding: 5px 25px;" valign="top" align="Left">'; include 'config/content.php'; include 'config/footer.php';
ob_end_flush();
?>
