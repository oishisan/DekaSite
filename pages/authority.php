<?php
echo '<table>';
if($_POST['charSet'] == 'Set' && !empty($_POST['charauth']) && !empty($_POST['charname']))
{
	$query = mssql_query("select account.dbo.user_profile.login_flag from account.dbo.user_profile left join character.dbo.user_character on character.dbo.user_character.user_no = account.dbo.user_profile.user_no where character_name = '".mssql_escape($_POST['charname'])."'");
	$count = mssql_num_rows($query);
	if ($count == 1)
	{
		$fetch = mssql_fetch_array($query);
		if ($fetch[0] == '0')
		{
			$name = str_replace('[GM]', '', $_POST['charname']);
			$name = str_replace('[DEV]', '', $name);
			$name = str_replace('[DEKARON]', '', $name);
			if ($_POST['charauth'] <> 'none')
			{
				$name = '['.$_POST['charauth'].']'.$name;
			}
			$query = mssql_query("select character_name from character.dbo.user_character where character_name = '".mssql_escape($name)."'");
			$count = mssql_num_rows($query);
			if ($count == 0)
			{
				mssql_query("UPDATE character.dbo.user_character set character_name = '".mssql_escape($name)."' where character_name = '".mssql_escape($_POST['charname'])."'");
				echo '<tr><td>',entScape($_POST['charname']),' has been succesfully renamed to ',entScape($name),'.</td></tr>';
			}
			else
			{
				echo '<tr><td>',entScape($name),' is taken.</td></tr>';
			}
		}
		else
		{
			echo '<tr><td>Account is online.</td></tr>';
		}
	}
	else
	{
		echo '<tr><td>Character not found.</td></tr>';
	}
	echo '</table>';
}
elseif (!empty($_GET['acct']))
{
	$query = mssql_query("SELECT account from ".mssql_escape($ini['MSSQL']['extraDB']).".dbo.auth where account = '".mssql_escape($_GET['acct'])."'");
	$count = mssql_num_rows($query);
	if ($count == '1')
	{
		$query = mssql_query("select character_name from character.dbo.user_character left join account.dbo.tbl_user on account.dbo.tbl_user.user_no = character.dbo.user_character.user_no where account.dbo.tbl_user.user_id = '".mssql_escape($_GET['acct'])."'");
		echo '<form action="?do='.entScape($_GET['do']).'" method="post"><tr><td>Character: <select name=charname>';
		while($fetch = mssql_fetch_array($query))
		{
			echo '<option value="',entScape($fetch[0]),'" selected>',entScape($fetch[0]),'</option>';
		}
		echo '</select></td></tr>
			<tr>
				<td>Authority: <select name="charauth">
					<option value="none" selected>None</option>
					<option value="GM">GM</option>
					<option value="DEV">DEV</option>
					<option value="DEKARON">DEKARON</option>
				</select></td>
			</tr>
			<tr>
			<td colspan="2"><input type="submit" name="charSet" value="Set"></input></td>
			</tr>
		</form>';
	}
	elseif($count > '1')
	{
		echo '<tr><td>Please warn administrator about account duplication.</td></tr></table>';
	}
	else
	{
		echo '<tr><td>Account is not of elevated authority.</td></tr></table>';
	}
}
else
{
	if($_GET['type'] == 'Delete' && !empty($_GET['delacct']))
	{
		mssql_query("DELETE FROM ".mssql_escape($ini['MSSQL']['extraDB']).".dbo.auth where account = '".mssql_escape($_GET['delacct'])."'");
		echo '<tr><td>Account ',entScape($_GET['delacct']),' successfully removed!</td></tr></table>';
	}
	elseif(empty($_POST['type']))
	{
		$acctQuery = mssql_query("SELECT * FROM ".mssql_escape($ini['MSSQL']['extraDB']).".dbo.auth");
		echo '
		<form action="?do='.entScape($_GET['do']).'" method="POST">
			<tr>
				<td><u>Account</u></td>
				<td><u>Authority</u></td>
				<td><u>Web Name</u></td>
			</tr>';
		while ($acct = mssql_fetch_array($acctQuery))
		{
			if ($acct[1] == '3'){$acct[1] = 'Admin';}
			if ($acct[1] == '2'){$acct[1] = 'GM';}
			echo '
			<tr>
				<td><a href="?do='.entScape($_GET['do']).'&acct=',entScape($acct[0]),'">',entScape($acct[0]),'</a></td>
				<td>',entScape($acct[1]),'</td>
				<td>',entScape($acct[2]),'</td>
				<td><a href="?do='.entScape($_GET['do']).'&type=Delete&delacct=',entScape($acct[0]),'">Remove</a></td>
			</tr>';
		}
		echo '
			<tr>
				<td colspan="4">Account: <input type="text" name="acct" /></td>
			</tr>
			<tr>
				<td colspan="4">Web name: <input type="text" name="news" /></td>
			</tr>
			<tr>
				<td colspan="4">Authority: <select name="auth"><option value="GM" selected>GM</option><option value="Admin">Admin</option></select></td>
			</tr>
			<tr>
				<td colspan="4"><input name="type" type="submit" value="Add"/></td>
			</tr>
		</form>';
	}
	elseif($_POST['type'] == 'Add' && !empty($_POST['acct']) && !empty($_POST['auth']) && !empty($_POST['news']))
	{
		if($_POST['auth'] == 'Admin')
		{
			$_POST['auth'] = $ini['Other']['lvl.Admin'];
		}
		else
		{
			$_POST['auth'] = $ini['Other']['lvl.GM'];
		}
		mssql_query("INSERT INTO ".mssql_escape($ini['MSSQL']['extraDB']).".dbo.auth (account, auth, news) values ('".mssql_escape($_POST['acct'])."','".mssql_escape($_POST['auth'])."','".mssql_escape($_POST['news'])."')");
		echo '<tr><td>Account ',entScape($_POST['acct']),' successfully added!</td></tr></table>';
	}
	else
	{
		echo '<tr><td>Invalid action!</td></tr></table>';
	}
}
?>