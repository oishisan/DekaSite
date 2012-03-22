<?php
$result = mssql_query("SELECT user_id FROM account.dbo.Tbl_user WHERE user_no = '".mssql_escape($_SESSION['user_no'])."'");
$row = mssql_fetch_row($result);
echo '
<table>
	<form method=POST>
		<tr>
			<td>Change Password</td>
		</tr>
		<tr>
			<td>Account: ',$row[0],'</td>
		</tr>
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
	$result1 = mssql_query("SELECT user_pwd FROM account.dbo.user_profile WHERE user_no = '".mssql_escape($_SESSION['user_no'])."'");
	$count1 = mssql_fetch_array($result1);
	if(empty($_POST['old']) || empty($_POST['new1']) || empty($_POST['new2']))
	{
		echo 'You have not filled in all fields.';
	}
	elseif(!preg_match("/[0-9a-zA-Z]?/", $_POST['new1']))
	{
		echo 'The password contains characters which are not allowed in the password.';
	}
	elseif(strlen($_POST['new1']) < 3)
	{
		echo 'The password must be at least 3 characters long.';
	}
	elseif($_POST['new1'] <> $_POST['new2'])
	{
		echo 'The new passwords do not match!';
	}
	elseif($count1[0] <> md5($_POST['old']))
	{
		echo 'Invalid password!';
	}
	else
	{
		mssql_query("UPDATE account.dbo.USER_PROFILE SET user_pwd = '".mssql_escape(md5($_POST['new1']))."' WHERE user_no = '".mssql_escape($_SESSION['user_no'])."'");
		echo "Your password has been successfully updated.";
	}
}
?>