<?php
echo'
<table>
	<form method="POST">
		<tr>
			<td>Username: <br><input type="text" name="accname" /></td>
		</tr>
		<tr>
			<td>Password: <br><input type="password" name="accpass" /></td>
		</tr>
		<tr>
			<td><input type="submit" name="login" value="Login"></td>
		</tr>';
if(isset($errormsg)) echo '<tr><td>',entScape($errormsg),'</td></tr>';
echo'
	</form>
</table>';
?>