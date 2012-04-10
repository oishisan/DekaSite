<?php
echo '
<table>
	<form method="POST">
		<tr>
			<td>Old Password:<br><input type="password" name="old"></td>
		</tr>
		<tr>
			<td>New Password:<br><input type="password" name="new1"></td>
		</tr>
		<tr>
			<td>New Password (again):<br><input type="password" name="new2" ></td>
		</tr>
		<tr>
			<td colspan=1><input type="submit" name="upass" value="Update Password"></td>
		</tr>
	</form>
</table>';
	
if(isset($_POST['upass']))
{
	$result1 = msquery("SELECT user_pwd FROM account.dbo.user_profile WHERE user_no = '%s'", $_SESSION['user_no']);
	$count1 = mssql_fetch_array($result1);
	if(empty($_POST['old']) || empty($_POST['new1']) || empty($_POST['new2']))
	{
		echo 'You have not filled in all fields.';
	}
	elseif(strlen($_POST['new1']) < 3)
	{
		echo 'The password must be at least 3 characters long.';
	}
	elseif($_POST['new1'] <> $_POST['new2'])
	{
		echo 'The new passwords do not match!';
	}
	elseif($count1['user_pwd'] <> md5($_POST['old']))
	{
		echo 'Invalid password!';
	}
	else
	{
		msquery("UPDATE account.dbo.USER_PROFILE SET user_pwd = '%s' WHERE user_no = '%s'", md5($_POST['new1']), $_SESSION['user_no']);
		echo 'Your password has been successfully updated.';
	}
}
?>
