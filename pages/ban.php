<?php
echo '<div><form action="?do=',entScape($_GET['do']),'&action=ban" method="POST">
<select name="type"><option value="account">Account Name</option>
<option value="charname">Character Name</option></select><input type="text" name="dataname"><br>
Reason: <input type="text" name="reason"><br>
<input type="submit" name="select" value="Ban Account" />
</form></div>';

if($_GET['action'] == 'ban' && !empty($_POST['dataname'])) 
{
	if($_POST['type'] == 'account') 
	{
		$bQuery = msquery("SELECT login_tag, count(login_tag) as num FROM account.dbo.USER_PROFILE WHERE user_id = '%s' group by login_tag", $_POST['dataname']);
		$bFetch = $bQuery->fetch();
		$accountname = $_POST['dataname'];
	} 
	else 
	{
		$bQuery = msquery("SELECT account.dbo.user_profile.login_tag, user_id, count(user_id) as num FROM account.dbo.USER_PROFILE left join character.dbo.user_character on character.dbo.user_character.user_no = account.dbo.user_profile.user_no WHERE character_name = '%s' group by account.dbo.user_profile.login_tag, user_id", $_POST['dataname']);
		$bFetch = $bQuery->fetch();
		$accountname = $bFetch['user_id'];
	}
	if($bFetch['num'] == 1 && $bFetch['login_tag'] == 'Y')
	{
		if (empty($_POST['reason'])) $_POST['reason'] = 'no reason';
		msquery("UPDATE account.dbo.USER_PROFILE SET login_tag = 'N' WHERE user_id = '%s'", $accountname);
		msquery("INSERT INTO %s.dbo.banned (wDate, accountname, reason, wBy, type) VALUES (getdate(), '%s', '%s', '%s', 'b')", $ini['MSSQL']['extrasDB'], $accountname, $_POST['reason'], $_SESSION['webName']);
		echo 'The account "',entScape($accountname),'" was banned.<br>';
		$dQuery = msquery("select top 1 Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) as IP, count(account.dbo.user_connlog_key.user_no) as num from account.dbo.user_connlog_key left join account.dbo.user_profile on account.dbo.user_profile.user_no = account.dbo.user_connlog_key.user_no where user_id = '%s' and account.dbo.user_profile.login_flag <> '0' group by account.dbo.user_connlog_key.login_time,conn_ip order by account.dbo.user_connlog_key.login_time desc", $accountname);
		$dFetch = $dQuery->fetch();
		if ($dFetch['num'] >= 1)
		{
			foreach($ini['Other']['ports.close'] as $port)
			{
				system('start '.$ini['Other']['file.cports'].' /close * '.$port.' '.$dFetch['IP'].' *');
			}
			echo 'The account "',entScape($accountname),'" has been disconnected.';
		}
	}
	elseif($bFetch['login_tag'] == 'N') 
	{
		echo 'The account is already banned.';
	}
	else
	{
		echo 'Could not find the account/character specified.';
	}

}

if($_GET['action'] == 'unban' && !empty($_GET['aid'])) 
{
	$uQuery = msquery("SELECT login_tag, count(login_tag) as num FROM account.dbo.USER_PROFILE WHERE user_id = '%s' group by login_tag", $_GET['aid']);
	$uFetch = $uQuery->fetch();
	if($uFetch['num'] == 1 && $uFetch['login_tag'] == 'N')
	{
		msquery("UPDATE account.dbo.USER_PROFILE SET login_tag = 'Y' WHERE user_id = '%s'", $_GET['aid']);
		msquery("DELETE FROM %s.dbo.banned WHERE accountname = '%s' and type = 'b'", $ini['MSSQL']['extrasDB'], $_GET['aid']);
		msquery("INSERT INTO %s.dbo.banned (wDate, accountname, wBy, type) VALUES (getdate(), '%s','%s', 'u')", $ini['MSSQL']['extrasDB'], $_GET['aid'] , $_SESSION['webName']);
		echo 'The account "',entScape($_GET['aid']),'" was unbanned.';

	}
	elseif($row[0] == 'Y') 
	{
		echo 'The account is not banned.';
	} 
	else 
	{
		echo 'The account could not be found.';
	} 	
}

echo '<br><br>If no end time is given, it will use the current time.
<form action="?do=',entScape($_GET['do']),'" method="POST">
Start time: <input type="text" name="stime" /><br>
End time: <input type="text" name="etime" /><br>
<select name="lType"><option value="b">Bans</option><option value="u">Unbans</option></select><br>
<input type="submit" name="search" value="Search">
</form>';
if(!empty($_POST['stime']) && isset($_POST['search']))
{
	if(empty($_POST['etime'])) 
	{
		$_POST['etime'] = date('M j Y g:iA');
	}
	else
	{
		$_POST['etime'] = date('M j Y g:iA',strtotime($_POST['etime']));
	}
	$_POST['stime'] = date('M j Y g:iA',strtotime($_POST['stime']));
	if(isset($_POST['lType']) && $_POST['lType'] == 'b')
	{
		$bQuery = msquery("SELECT wDate, accountname, reason, wBy FROM %s.dbo.banned where type = 'b' and wDate >= convert(datetime,'%s') and wDate <= convert(datetime,'%s') order by wDate desc", $ini['MSSQL']['extrasDB'], $_POST['stime'], $_POST['etime']);
		$bQuery = $bQuery->fetchAll();
		if(count($bQuery) > 0)
		{
			echo 'Current bans<br>
			',entScape($_POST['stime']),' - ',entScape($_POST['etime']),'
			<table><tr><th>Date</th><th>Account</th><th>Reason</th><th>Issued by</th>';
			foreach ($bQuery as $bFetch) 
			{
				echo '<tr><td>',entScape($bFetch['wDate']),'</td>
				<td>',entScape($bFetch['accountname']),'</td>
				<td>',entScape($bFetch['reason']),'</td>
				<td>',entScape($bFetch['wBy']),'</td>
				<td><a href="?do=',entScape($_GET['do']),'&action=unban&aid=',entScape($bFetch['accountname']),'">Unban</a></td></tr>';
			}
			echo '</table>';
		}
		else
		{
			echo 'No logs were found for the specified time period.';
		}
	}
	else
	{
		$bQuery = msquery("SELECT wDate, accountname, wBy FROM %s.dbo.banned where type = 'u' and wDate >= convert(datetime,'%s') and wDate <= convert(datetime,'%s') order by wDate desc", $ini['MSSQL']['extrasDB'], $_POST['stime'], $_POST['etime']);
		$bQuery = $bQuery->fetchAll();
		if(count($bQuery) > 0)
		{
			echo 'Unban Log<br>
			',entScape($_POST['stime']),' - ',entScape($_POST['etime']),'
			<table><th>Date</th><th>Account</th><th>Issued by</th></tr>';
			foreach ($bQuery as $bFetch) 
			{
				echo '<tr>
				<td>',entScape($bFetch['wDate']),'</td>
				<td>',entScape($bFetch['accountname']),'</td>
				<td>',entScape($bFetch['wBy']),'</td></tr>';
			}	
			echo '</table>';
		}
		else
		{
			echo 'No logs were found for the specified time period.';
		}
	}
}
?>
