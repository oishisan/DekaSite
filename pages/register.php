<?php
echo '
<table>
	<tr>
		<td>Registration</td>
	</tr>
	<form method="POST">
		<tr>
			<td>Account:<br><input type="text" name="accname"></td>
		</tr>
		<tr>
			<td>Password:<br><input type="password" name="accpass1"></td></tr>
		<tr>
			<td>Password (Again):<br><input type="password" name="accpass2"></td></tr>
		<tr>
			<td>E-Mail:<br><input type="text" name="accmail"></td></tr>
		<tr>
			<td><input type="submit" name="sub" value="Create Account"></td>
		</tr>
	</form>';

if(isset($_POST['sub']))
{
	$result1 = mssql_query("SELECT * FROM account.dbo.USER_PROFILE WHERE user_id = '".mssql_escape($_POST['accname'])."'");
	$result2 = mssql_query("SELECT * FROM account.dbo.Tbl_user WHERE user_id = '".mssql_escape($_POST['accname'])."'");
	$result3 = mssql_query("SELECT * FROM account.dbo.Tbl_user WHERE user_mail = '".mssql_escape($_POST['accmail'])."'");
	$row1 = mssql_num_rows($result1);
	$row2 = mssql_num_rows($result2);
	$row3 = mssql_num_rows($result3);
	echo '<tr><td>';
	if(empty($_POST['accname']) || empty($_POST['accpass1']) || empty($_POST['accpass2'])|| empty($_POST['accname']) || empty($_POST['accmail']))
	{
		echo 'You didn\'t fill in all the fields.';
	}
	elseif($row1 > '0' || $row2 > '0')
	{
		echo 'This Account name already exists.';
	}
	elseif($row3 > '0')
	{
		echo 'This E-Mail is already in use.';
	}
	elseif($_POST['accpass1'] != $_POST['accpass2'])
	{
		echo 'The passwords do not match.';
	}
	elseif(!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/i",$_POST['accmail']))
	{
		echo 'You have entered and invalid e-mail format.';
	}
	elseif(strlen($_POST['accname']) < 3)
	{
		echo 'The Account name must be at least 3 characters.';
	}
	elseif(strlen($_POST['accpass1']) < 3)
	{
		echo 'The Password must be 3 characters.';
	}
	else
	{
		$dk_time=strftime("%y%m%d%H%M%S");
		list($usec1, $sec1) = explode(" ",microtime());
		$dk_user_no=$dk_time.substr($usec1,2,2);
		$accpass = md5($_POST['accpass1']);
		mssql_query("INSERT INTO account.dbo.USER_PROFILE (user_no,user_id,user_pwd,resident_no,user_type,login_flag,login_tag,ipt_time,login_time,logout_time,user_ip_addr,server_id) VALUES ('".mssql_escape($dk_user_no)."','".mssql_escape($_POST['accname'])."','".mssql_escape($accpass)."','801011000000','1','0','Y','".mssql_escape($date)."',null,null,null,'000')");
		mssql_query("INSERT INTO account.dbo.Tbl_user (user_no,user_id,user_mail) VALUES ('".mssql_escape($dk_user_no)."','".mssql_escape($_POST['accname'])."','".mssql_escape($_POST['accmail'])."')");
		echo 'The account was successfully created.';
	}
	echo '</td></tr>';
}
echo '</table>';
?>