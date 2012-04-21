<?php
/*
CSS page specific IDs
---------------------
#chars		information containing characters
#char		information on a character
#accts		information containing account links
#ips		information containing ip links
#heading	the sectional headings of the information
*/
echo '<form action="?do=',entScape($_GET['do']),'" Method="POST">
<select name="type"><option value="account" selected>Account</option><option value="ip">IP</option><option value="char">Character</option></select>
<input name="data" type="text"> <input name="submit" type="submit" value="Search!"></form>';

if ((!empty($_POST['data']) && $_POST['type'] == 'account') || ($_GET['type'] == 'account' && !empty($_GET['data'])))
{
	if ($_GET['type'] == 'account')
	{
		$data = $_GET['data'];
	} 
	else 
	{
		$data = $_POST['data'];
	}	
	$query = msquery("Select count(user_id )as num, user_no from account.dbo.user_profile where user_id = '%s' group by user_no", $data);
	$fetch = mssql_fetch_array($query);
	if ($fetch['num'] == '1')
	{
		echo 'Account lookup: ',entScape($data),'<br>';
		echo '<div id="chars"><span id="heading">Characters</span>';
		$query2 = msquery("Select character_name from character.dbo.user_character where user_no = '%s'", $fetch['user_no']);
		while ($fetch2 = mssql_fetch_array($query2))
		{
			echo '<br><a href="?do=',entScape($_GET['do']),'&type=char&data=',entScape($fetch2['character_name']),'">',entScape($fetch2['character_name']),'</a>';
		}
		$linked = array();
		$query = msquery("select distinct Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) as IP from account.dbo.user_connlog_key left join account.dbo.user_profile on account.dbo.user_profile.user_no = account.dbo.user_connlog_key.user_no where user_id = '%s'", $data);
		echo '</div><div id="accts"><span id="heading">Account Links</span>';
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
				echo '<br><a href="?do=',entScape($_GET['do']),'&type=account&data=',entScape($linked[$i]),'">',entScape($linked[$i]),'</a>';
				$i++;
			}while($i < $count);
		}
		else
		{
			echo '<br>No linked accounts';
		}
		echo '</div><div id="ips"><span id="heading">Unique IPs</span>';
		$query = msquery("select distinct Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) as IP, user_id from account.dbo.user_connlog_key left join account.dbo.user_profile on account.dbo.user_profile.user_no = account.dbo.user_connlog_key.user_no where user_id = '%s'", $data);
		$rows = mssql_num_rows($query);
		if ($rows > '0')
		{
			while ($fetch = mssql_fetch_array($query))
			{
				echo '<br><a href="?do=',entScape($_GET['do']),'&type=ip&data=',entScape($fetch['IP']),'">',entScape($fetch['IP']),'</a>';
			}
		}
		else
		{
			echo '<br>No linked IPs';
		}
		echo '</div>';
	}
	else
	{
		echo 'Account not found!';
	}
}
elseif ((!empty($_POST['data']) && $_POST['type'] == 'ip') || ($_GET['type'] == 'ip' && !empty($_GET['data'])))
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
		echo 'IP lookup: ',entScape($data);
		while ($fetch = mssql_fetch_array($query))
		{
			echo '<br><a href="?do=',entScape($_GET['do']),'&type=account&data=',entScape($fetch['user_id']),'">',entScape($fetch['user_id']),'</a>';
		}
	}
	else
	{
		echo 'IP not found!';
	}
}
elseif((!empty($_POST['data']) && $_POST['type'] == 'char') || (!empty($_GET['data']) && $_GET['type'] == 'char'))
{
	if($_GET['type'] == 'char')
	{
		$data = $_GET['data'];
	}
	else
	{
		$data = $_POST['data'];
	}
	$cQuery = msquery("select user_id, dwMoney, dwStoreMoney, dwStorageMoney, nHP, nMP, wStr, wDex, wCon, wSpr, wStatPoint, wSkillPoint, wLevel, byPCClass, wPKCount, nShield, dwPVPPoint, wWinRecord, wLoseRecord, count(user_id) as num from account.dbo.user_profile left join character.dbo.user_character on account.dbo.user_profile.user_no = character.dbo.user_character.user_no where character_name = '%s' group by user_id, dwMoney, dwStoreMoney, dwStorageMoney, nHP, nMP, wStr, wDex, wCon, wSpr, wStatPoint, wSkillPoint, wLevel, byPCClass, wPKCount, nShield, dwPVPPoint, wWinRecord, wLoseRecord", $data);
	$cFetch = mssql_fetch_array($cQuery);
	if ($cFetch['num'] == '1')
	{
		if($cFetch['byPCClass'] == '0') $cFetch['byPCClass'] = 'Knight';
		if($cFetch['byPCClass'] == '1') $cFetch['byPCClass'] = 'Hunter';
		if($cFetch['byPCClass'] == '2') $cFetch['byPCClass'] = 'Mage';
		if($cFetch['byPCClass'] == '3') $cFetch['byPCClass'] = 'Summoner';
		if($cFetch['byPCClass'] == '4') $cFetch['byPCClass'] = 'Segnale';
		if($cFetch['byPCClass'] == '5') $cFetch['byPCClass'] = 'Bagi';
		if($cFetch['byPCClass'] == '6') $cFetch['byPCClass'] = 'Aloken';
		echo 'Account: <a href="?do=',entScape($_GET['do']),'&type=account&data=',entScape($cFetch['user_id']),'">',entscape($cFetch['user_id']),'</a><br>
		<div id="char">Character: ',entScape($data),'<br>
		Money: ',entScape($cFetch['dwMoney']),'<br>
		Storage money: ',entScape($cFetch['dwStorageMoney']),'<br>
		Store money: ',entScape($cFetch['dwStoreMoney']),'<br>
		Class: ',entScape($cFetch['byPCClass']),'<br>
		Level: ',entScape($cFetch['wLevel']),'<br>
		Shield: ',entScape($cFetch['nShield']),'<br>
		HP: ',entScape($cFetch['nHP']),'<br>
		MP: ',entScape($cFetch['nMP']),'<br>
		Stat points: ',entScape($cFetch['wStatPoint']),'<br>
		Str: ',entScape($cFetch['wStr']),'<br>
		Dex: ',entScape($cFetch['wDex']),'<br>
		Con: ',entScape($cFetch['wCon']),'<br>
		Spr: ',entScape($cFetch['wSpr']),'<br>
		Skill points: ',entScape($cFetch['wSkillPoint']),'<br>
		PKs: ',entScape($cFetch['wPKCount']),'<br>
		PVP Points: ',entScape($cFetch['dwPVPPoint']),'<br>
		Wins: ',entScape($cFetch['wWinRecord']),'<br>
		Losses: ',entScape($cFetch['wLoseRecord']),'</div>';
	}
	else
	{
		echo 'Character not found!';
	}
}
?>
