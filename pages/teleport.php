<?php
$fd = fopen ($ini['Other']['file.maplist'], "r");
if ($fd)
{
	$maps = array();
	fgetcsv($fd, 4096);
	while ($buffer = fgetcsv($fd, 4096)) 
	{
		$maps[$buffer[0]] = $buffer[1];
	}
	fclose ($fd);
	$aValues = array_keys($maps);
	$count = count($aValues);
	echo '<table><form action="?do=',entScape($_GET['do']),'" method="POST">
	<tr><td>Character:<br><input type="text" name="charname" /></td></tr>
	<tr><td>Map:<br><select name="mapsel">';
	$i=0;
	do 
	{
		echo '<option value="',entScape($aValues[$i]),'">',entScape($maps[$aValues[$i]]),'</option>';
		$i+=1;
	}while($i != $count);
	echo '</select></td></tr>
	<tr><td>X: <input type="text" name="xval" /> Y: <input type="text" name="yval" /></td></tr>
	<tr><td><input type="submit" name="teleport" value="Teleport" /></td></tr>
	</form></table>';
	if(isset($_POST['teleport']) & !empty($_POST['charname']))
	{
		$cQuery = msquery("SELECT count(account.dbo.user_profile.user_no) as num, account.dbo.user_profile.login_flag from character.dbo.user_character left join account.dbo.user_profile on character.dbo.user_character.user_no = account.dbo.user_profile.user_no where character_name = '%s' group by account.dbo.user_profile.login_flag", $_POST['charname']);
		$cFetch = mssql_fetch_array($cQuery);
		if ($cFetch['num'] == 1)
		{
			if ($cFetch['login_flag'] == '0')
			{
				If ((!ctype_digit($_POST['xval']) && !empty($_POST['xval'])) || (!ctype_digit($_POST['yval']) && !empty($_POST['yval'])))
				{
					echo 'X and Y values can only be positive integers!';
				}
				else
				{
					if (empty($_POST['xval'])) $_POST['xval'] = 0;
					if (empty($_POST['yval'])) $_POST['yval'] = 0;
					msquery("UPDATE character.dbo.user_character SET wPosX = '%s', wPosY = '%s', wMapIndex = '%s' where character_name = '%s'", $_POST['xval'], $_POST['yval'], $_POST['mapsel'], $_POST['charname']);
					echo entScape($_POST['charname']),' successfully teleported!';
				}
			}
			else
			{
				echo 'Account is online.';
			}
		}
		else
		{
			echo 'Character not found!';
		}
	}	
}
else
{
	echo 'Unable to open map list!';
}
?>
