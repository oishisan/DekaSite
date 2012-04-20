<?php
$fd = fopen ($ini['Other']['file.maplist'], "r");
if ($fd)
{
	$maps = array();
	fgetcsv($fd, 1024);
	while ($buffer = fgetcsv($fd, 1024)) 
	{
		$maps[$buffer[0]] = $buffer[1];
	}
	fclose ($fd);
	
	if ($_GET['type'] == 'disconnect' && !empty($_GET['ip']))
	{
	
		if(preg_match("~^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$~", $_GET['ip']))
		{
			foreach($ini['Other']['ports.close'] as $port)
			{
				system('start '.$ini['Other']['file.cports'].' /close * '.$port.' '.$_GET['ip'].' *');
			}
			echo 'Disconnect command sent to the ip of "',entScape($_GET['ip']),'" .<br><br>';
		}
		else
		{
			echo 'Invalid ip address format.';
		}
	}
	$result = msquery("select character_name, wmapindex, Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) as ip FROM character.dbo.user_character left JOIN character.dbo.char_connlog_key on character.dbo.user_character.character_no = character.dbo.char_connlog_key.character_no left join account.dbo.user_profile on account.dbo.user_profile.user_no = character.dbo.user_character.user_no WHERE character.dbo.char_connlog_key.logout_time is null and character.dbo.user_character.login_time = character.dbo.char_connlog_key.login_time and account.dbo.user_profile.login_flag = '1100' order by character.dbo.user_character.character_name asc");
	echo '<table><tr><th colspan="3">Players online: ',entScape(mssql_num_rows($result)),'</th></tr>
		        <th>Character</th>
			<th>Map</th>
			<th>IP</th>
		        </tr>'; 
	while ($record = mssql_fetch_array($result)) 
	{ 
		if (array_key_exists($record['wmapindex'], $maps)) 
		{
			$map = $maps[$record['wmapindex']];
		}
		else
		{
			$map = $record['wmapindex'];
		}
		echo '<tr><td>',entScape($record['character_name']),'</td> 
				<td>',entScape($map),'</td>
				<td><a href="?do=playeronline&type=disconnect&ip=',entScape($record['ip']),'">',entScape($record['ip']),'</a></td></tr>'; 
	}
	echo '</table>';  
	
}
else
{
	echo 'Maps file not found!';
}
?>
