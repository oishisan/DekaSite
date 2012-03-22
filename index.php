<?php
ob_start();
session_start();
require 'config/loginBE.php';
echo '
<html>
	<title>Simple Dekaron</title>
	<head></head>
	<body><center>';
		include 'top.php'; echo'</center>
		<table style="border-top: 1px solid black;" width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td style="border-right:1px solid black;" valign="top" align="center" width="15%">'; include 'side.php'; echo'</td>
				<td style="padding: 5px 25px;" valign="top" align="Left">'; include 'content.php'; echo'</td>
			</tr>
		</table>
	</body>
</html>';
ob_end_flush();
?>
