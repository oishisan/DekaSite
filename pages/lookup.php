<?php
echo '<table><form action="?do=',entScape($_GET['do']),'" Method="POST">
<tr><td >Master Lookup</td></tr>
<tr><td><select name="type">
	<option value="account" selected>Account</option>
	<option value="ip">IP</option>
	<option value="char">Character</option></select>
	<input name="data" type="text"></td></tr>
<tr><td><input name="submit" type="submit"value="Go!"></td></tr></form></table>';

if ((!empty($_POST['data']) && $_POST['type'] == 'account') or ($_GET['type'] == 'account' && !empty($_GET['data'])) or (!empty($_POST['data']) && $_POST['type'] == 'char'))
{
	if ($_GET['type'] == 'account')
	{
		$data = $_GET['data'];
	} 
	else 
	{
		$data = $_POST['data'];
	}
	if ($_POST['type'] == 'char')
	{
		$query = msquery("select user_id, count(user_id) as num from account.dbo.user_profile left join character.dbo.user_character on account.dbo.user_profile.user_no = character.dbo.user_character.user_no where character_name = '%s' group by user_id", $data);
		$fetch = mssql_fetch_array($query);
		if ($fetch['num'] == '1')
		{
			$data = $fetch['user_id'];
		}
		else
		{
			$data = '';
			$error = 'Character';
		}
	}
	else
	{
	$error = 'Account';
	}
	
	$query = msquery("Select count(user_id )as num, user_no from account.dbo.user_profile where user_id = '%s' group by user_no", $data);
	$fetch = mssql_fetch_array($query);
	if ($fetch['num'] == '1')
	{
		echo '<table><tr><td>Account Lookup for ',entScape($data),'</td></tr>';
		echo '<tr><td>Characters</td></tr>';
		$query2 = msquery("Select character_name from character.dbo.user_character where user_no = '%s'", $fetch['user_no']);
		while ($fetch2 = mssql_fetch_array($query2))
		{
			echo '<tr><td>',entScape($fetch2['character_name']),'</td></tr>';
		}
		$linked = array();
		$query = msquery("select distinct Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) as IP from account.dbo.user_connlog_key left join account.dbo.user_profile on account.dbo.user_profile.user_no = account.dbo.user_connlog_key.user_no where user_id = '%s'", $data);
		echo '<tr><td>Account Links</td></tr>';
		while ($fetch = mssql_fetch_array($query))
			{
				$query2 = msquery("select distinct user_id from account.dbo.user_connlog_key left join account.dbo.user_profile on account.dbo.user_profile.user_no = account.dbo.user_connlog_key.user_no where user_id <> '%s' and Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) = '%s'", $data, $fetch['IP']);
					while ($fetch2 = mssql_fetch_array($query2))
					{
						if (!in_array($fetch2['user_id'], $linked))
						{
							$linked[] = $fetch2['user_id'];
						}
					}
		}
		$count = count($linked);
		if ($count > 0)
		{
			$i = 0;
			do
			{
				echo '<tr><td><a href="?do=',entScape($_GET['do']),'&type=account&data=',entScape($linked[$i]),'">',entScape($linked[$i]),'</a></td></tr>';
				$i += 1;
			}while($i < $count);
		}
		else
		{
			echo '<tr><td>No linked accounts</td></tr>';
		}
		echo '<tr><td>Unique IPs</td></tr>';
		$query = msquery("select distinct Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) as IP, user_id from account.dbo.user_connlog_key left join account.dbo.user_profile on account.dbo.user_profile.user_no = account.dbo.user_connlog_key.user_no where user_id = '%s'", $data);
		$rows = mssql_num_rows($query);
		if ($rows > '0')
		{
			while ($fetch = mssql_fetch_array($query))
			{
			echo '<tr><td><a href="?do=',entScape($_GET['do']),'&type=ip&data=',entScape($fetch['IP']),'">',entScape($fetch['IP']),'</a></td></tr>';
			}
		}
		else
		{
			echo '<tr><td>No linked IPs</td></tr>';
		}
		echo '</table>';
	}
	elseif($fetch['num'] > '1')
	{
		echo 'Account confliction error! Please inform the administrator.';
	}
	else
	{
		echo $error,' not found!';
	}
}
elseif (!empty($_POST['data']) && $_POST['type'] == 'ip' or ($_GET['type'] == 'ip' && !empty($_GET['data'])))
{
	if ($_GET['type'] == 'ip')
	{
		$data = $_GET['data'];
	} 
	else 
	{
		$data = $_POST['data'];
	}
	$query = msquery("Select count(conn_ip) as num from account.dbo.user_connlog_key where Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) = '%s'", $data);
	$fetch = mssql_fetch_array($query);
	if ($fetch['num'] > '0')
	{
		$query = msquery("select distinct user_id from account.dbo.user_connlog_key left join account.dbo.user_profile on account.dbo.user_profile.user_no = account.dbo.user_connlog_key.user_no where Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) = '%s'", $data);
		echo '<table><tr><td>IP Lookup for ',entScape($data),'</td></tr>';
		while ($fetch = mssql_fetch_array($query))
		{
			echo '<tr><td><a href="?do=',entScape($_GET['do']),'&type=account&data=',entScape($fetch['user_id']),'">',entScape($fetch['user_id']),'</a></td></tr>';
		}
		echo '</table>';
	}
	else
	{
		echo 'IP not found!';
	}
}
?>
