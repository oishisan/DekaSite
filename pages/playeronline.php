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
			foreach($ini['Other']['ports.close'] as $port)
			{
				system('start '.$ini['Other']['file.cports'].' /close * '.$port.' '.$_GET['ip'].' *');
			}
			echo 'Disconnect command sent to the ip of "',entScape($_GET['ip']),'" .<br><br>';
	}
	$result = msquery("select p.user_id as uid, c.character_name as cnm, c.wlevel as clvl, c.wmapindex cmapi, c.bypcclass as ccls, Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) as ip FROM character.dbo.user_character c JOIN account.dbo.user_profile p ON c.user_no = p.user_no join account.dbo.user_connlog_key ucl on c.user_no = ucl.user_no WHERE c.login_time IN (SELECT max(login_time) FROM character.dbo.user_character GROUP BY user_no) AND p.login_flag = '1100' and p.login_time = ucl.login_time order by c.cnm asc, ucl.login_time desc");
	echo '<table><tr><td colspan="6">Players online: ',entScape(mssql_num_rows($result)),'</td></tr>
		        <td>Character</td> 
		        <td>Level</td>
		        <td>Class</td>
		        <td>Map</td>
				<td>IP</td>
		        </tr>'; 
	while ($record = mssql_fetch_array($result)) 
	{ 
		if ($record['ccls'] == 0) $class = 'Knight'; 
		if ($record['ccls'] == 1) $class = 'Hunter'; 
		if ($record['ccls'] == 2) $class = 'Mage'; 
		if ($record['ccls'] == 3) $class = 'Summoner'; 
		if ($record['ccls'] == 4) $class = 'Segnale'; 
		if ($record['ccls'] == 5) $class = 'Bagi'; 
		if ($record['ccls'] == 6) $class = 'Aloken'; 
		if (array_key_exists($record['cmapi'], $maps)) 
		{
			$map = $maps[$record['cmapi']];
		}
		else
		{
			$map = $record['cmapi'];
		}
		echo '<tr><td>',entScape($record['cnm']),'</td> 
				<td>',entScape($record['clvl']),'</td> 
				<td>',entScape($class),'</td> 
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
