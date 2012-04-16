<?php
echo '<table>';
if($_POST['charSet'] == 'Set' && !empty($_POST['charauth']) && !empty($_POST['charname']))
{
	$query = msquery("select account.dbo.user_profile.login_flag from account.dbo.user_profile left join character.dbo.user_character on character.dbo.user_character.user_no = account.dbo.user_profile.user_no where character_name = '%s'", $_POST['charname']);
	$count = mssql_num_rows($query);
	if ($count == 1)
	{
		$fetch = mssql_fetch_array($query);
		if ($fetch['login_flag'] == '0')
		{
			$name = str_replace('[GM]', '', $_POST['charname']);
			$name = str_replace('[DEV]', '', $name);
			$name = str_replace('[DEKARON]', '', $name);
			if ($_POST['charauth'] <> 'none')
			{
				$name = '['.$_POST['charauth'].']'.$name;
			}
			$query = msquery("select character_name from character.dbo.user_character where character_name = '%s'", $name);
			$count = mssql_num_rows($query);
			if ($count == 0)
			{
				msquery("UPDATE character.dbo.user_character set character_name = '%s' where character_name = '%s'", $name, $_POST['charname']);
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
	$query = msquery("SELECT account from %s.dbo.auth where account = '%s'", $ini['MSSQL']['extrasDB'], $_GET['acct']);
	$count = mssql_num_rows($query);
	if ($count == '1')
	{
		$query = msquery("select character_name from character.dbo.user_character left join account.dbo.user_profile on account.dbo.user_profile.user_no = character.dbo.user_character.user_no where account.dbo.user_profile.user_id = '%s'", $_GET['acct']);
		echo '<form action="?do=',entScape($_GET['do']),'" method="post"><tr><td>Character: <select name=charname>';
		while($fetch = mssql_fetch_array($query))
		{
			echo '<option value="',entScape($fetch['character_name']),'" selected>',entScape($fetch['character_name']),'</option>';
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
		msquery("DELETE FROM %s.dbo.auth where account = '%s'", $ini['MSSQL']['extrasDB'], $_GET['delacct']);
		echo '<tr><td>Account ',entScape($_GET['delacct']),' successfully removed!</td></tr></table>';
	}
	elseif(empty($_POST['type']))
	{
		$acctQuery = msquery("SELECT * FROM %s.dbo.auth order by auth desc", $ini['MSSQL']['extrasDB']);
		echo '
		<form action="?do='.entScape($_GET['do']).'" method="POST">
			<tr>
				<td><u>Account</u></td>
				<td><u>Authority</u></td>
				<td><u>Web Name</u></td>
			</tr>';
		while ($acct = mssql_fetch_array($acctQuery))
		{
			if ($acct['auth'] == '3'){$acct['auth'] = 'Admin';}
			if ($acct['auth'] == '2'){$acct['auth'] = 'GM';}
			echo '
			<tr>
				<td><a href="?do='.entScape($_GET['do']).'&acct=',entScape($acct['account']),'">',entScape($acct['account']),'</a></td>
				<td>',entScape($acct['auth']),'</td>
				<td>',entScape($acct['webName']),'</td>
				<td><a href="?do='.entScape($_GET['do']).'&type=Delete&delacct=',entScape($acct['account']),'">Remove</a></td>
			</tr>';
		}
		echo '
			<tr>
				<td colspan="4">Account: <input type="text" name="acct" /></td>
			</tr>
			<tr>
				<td colspan="4">Web name: <input type="text" name="webName" /></td>
			</tr>
			<tr>
				<td colspan="4">Authority: <select name="auth"><option value="GM" selected>GM</option><option value="Admin">Admin</option></select></td>
			</tr>
			<tr>
				<td colspan="4"><input name="type" type="submit" value="Add"/></td>
			</tr>
		</form>';
	}
	elseif($_POST['type'] == 'Add' && !empty($_POST['acct']) && !empty($_POST['auth']) && !empty($_POST['webName']))
	{
		if($_POST['auth'] == 'Admin')
		{
			$_POST['auth'] = $ini['Other']['lvl.Admin'];
		}
		else
		{
			$_POST['auth'] = $ini['Other']['lvl.GM'];
		}
		msquery("INSERT INTO %s.dbo.auth (account, auth, webName) values ('%s','%s','%s')", $ini['MSSQL']['extrasDB'],$_POST['acct'], $_POST['auth'], $_POST['webName']);
		echo '<tr><td>Account ',entScape($_POST['acct']),' successfully added!</td></tr></table>';
	}
	else
	{
		echo '<tr><td>Invalid action!</td></tr></table>';
	}
}
?>
