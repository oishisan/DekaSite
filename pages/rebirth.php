<?php
requireExtras();
if(isset($ini['Other']['rebirth']) && isset($ini['Other']['rebirth.location']))
{
	echo '<table><tr><th>Rebirth</th><th>Required level</th><th>Points acquired</th></tr>';
	$count = count($ini['Other']['rebirth']);
	for($i = 0; $i < $count; $i++)
	{
		$rArray = explode(',',$ini['Other']['rebirth'][$i]);
		echo '<tr><td>',entScape($i+1),'</td><td>',entScape($rArray[0]),'</td><td>',entScape($rArray[1]),'</td></tr>';
	}
	echo '</table>';
	if(isset($_POST['rebirth']) && !empty($_POST['rchar']))
	{
		$cQuery = msquery("select login_flag from account.dbo.user_profile where login_flag = '0' and user_no = '%s'", $_SESSION['user_no']);
		if(mssql_num_rows($cQuery) == 1)
		{
			$cQuery = msquery("select character.dbo.user_character.character_no, byPCClass, wLevel, rebirth from character.dbo.user_character left join %s.dbo.rebirth on %s.dbo.rebirth.character_no = character.dbo.user_character.character_no where character_name = '%s' and user_no = '%s'", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'],$_POST['rchar'], $_SESSION['user_no']);
			if(mssql_num_rows($cQuery) == 1)
			{
				$cFetch = mssql_fetch_array($cQuery);
				if($cFetch['rebirth'] == null)
				{
					$rArray = explode(',',$ini['Other']['rebirth'][0]);
					if($cFetch['wLevel'] >= $rArray[0])
					{
						if(!isset($_POST['loc']) || !ctype_digit($_POST['loc']) || $_POST['loc'] > (count($ini['Other']['rebirth.location']) - 1))
						{
							$_POST['loc'] = 0;
						}
						$rLoc = explode(',',$ini['Other']['rebirth.location'][$_POST['loc']]);
						msquery("INSERT into %s.dbo.rebirth values ('%s','1')", $ini['MSSQL']['extrasDB'], $cFetch['character_no']);
						$sQuery = msquery("SELECT wStr, wCon, wDex, wSpr, wLevel from character.dbo.user_character where character_no = 'DEKARON%s000001'", $cFetch['byPCClass']); 
						$sFetch = mssql_fetch_array($sQuery);
						msquery("UPDATE character.dbo.user_character SET wStr = '%s', wSpr = '%s', wCon = '%s', wDex = '%s', wLevel = '%s', wStatPoint = '%s', wPosX = '%s', wPosY = '%s', wMapIndex = '%s', dwExp = '0' where character_name = '%s'", $sFetch['wStr'], $sFetch['wSpr'], $sFetch['wCon'], $sFetch['wDex'], $sFetch['wLevel'], $rArray[1], $rLoc[1], $rLoc[2], $rLoc[0], $_POST['rchar']);
						if($ini['Other']['rebirth.SkillPoint'] == true)
						{
							msquery("UPDATE character.dbo.user_character SET wSkillPoint = '0' WHERE character_name = '%s'", $_POST['rchar']);
						}
						if($ini['Other']['rebirth.Skill'] == true)
						{
							msquery("DELETE FROM character.dbo.user_slot WHERE character_no = '%s'", $cFetch['character_no']);
							msquery("DELETE FROM character.dbo.user_skill WHERE character_no = '%s'", $cFetch['character_no']);
						}
						echo 'Rebirth successful!<br>';
					}
					else
					{
						echo 'Character does not meet level requirements.<br>';
					}
				}
				elseif($cFetch['rebirth'] < $count)
				{
					$rArray = explode(',',$ini['Other']['rebirth'][$cFetch['rebirth']]);
					if($cFetch['wLevel'] >= $rArray[0])
					{
						if(!isset($_POST['loc']) || !ctype_digit($_POST['loc']) || $_POST['loc'] > (count($ini['Other']['rebirth.location']) - 1))
						{
							$_POST['loc'] = 0;
						}
						$rLoc = explode(',',$ini['Other']['rebirth.location'][$_POST['loc']]);
						msquery("update %s.dbo.rebirth set rebirth = rebirth +1 where character_no = '%s'", $ini['MSSQL']['extrasDB'], $cFetch['character_no']);
						$sQuery = msquery("SELECT wStr, wCon, wDex, wSpr, wLevel from character.dbo.user_character where character_no = 'DEKARON%s000001'", $cFetch['byPCClass']); 
						$sFetch = mssql_fetch_array($sQuery);
						$stats = 0;
						for($i = 1; $i <= ($cFetch['rebirth']+1); $i++)
						{
							$rArray = explode(',',$ini['Other']['rebirth'][$i-1]);
							$stats += $rArray[1];
						}
						msquery("UPDATE character.dbo.user_character SET wStr = '%s', wSpr = '%s', wCon = '%s', wDex = '%s', wLevel = '%s', wStatPoint = '%s', wPosX = '%s', wPosY = '%s', wMapIndex = '%s', dwExp = '0' where character_name = '%s'", $sFetch['wStr'], $sFetch['wSpr'], $sFetch['wCon'], $sFetch['wDex'], $sFetch['wLevel'], $stats, $rLoc[1], $rLoc[2], $rLoc[0], $_POST['rchar']);
											if($ini['Other']['rebirth.SkillPoint'] == true)
						{
							msquery("UPDATE character.dbo.user_character SET wSkillPoint = '0' WHERE character_name = '%s'", $_POST['rchar']);
						}
						if($ini['Other']['rebirth.Skill'] == true)
						{
							msquery("DELETE FROM character.dbo.user_slot WHERE character_no = '%s'", $cFetch['character_no']);
							msquery("DELETE FROM character.dbo.user_skill WHERE character_no = '%s'", $cFetch['character_no']);
						}
						echo 'Rebirth successful!<br>';
					}
					else
					{
						echo 'Character does not meet level requirements.<br>';
					}
				}
				else
				{
					echo 'Character has hit maximum rebirths.<br>';
				}
			}
			else
			{
				echo 'Character does not exist on your account.<br>';
			}
		}
		else
		{
			echo 'Please logout of the game.<br>';
		}
	}
	$cQuery = msquery("select character_name, rebirth from account.dbo.user_profile left join character.dbo.user_character on character.dbo.user_character.user_no = account.dbo.user_profile.user_no left join %s.dbo.rebirth on character.dbo.user_character.character_no = %s.dbo.rebirth.character_no where (rebirth < '%s' or rebirth is null) and account.dbo.user_profile.user_no = '%s'", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $count, $_SESSION['user_no']);
	if(mssql_num_rows($cQuery) > 0)
	{
		echo '<form action="?do=',entScape($_GET['do']),'" method="POST">';
		echo 'Location: <select name="loc">';
		for($i = 0; $i < count($ini['Other']['rebirth.location']); $i++)
		{
			$rArray = explode(',',$ini['Other']['rebirth.location'][$i]);
			echo '<option value="',entScape($i),'">',entScape($rArray[3]),'</option>';
		}

		echo '</select><br>Character (rebirths): <select name="rchar">';
		while($cFetch = mssql_fetch_array($cQuery))
		{
			if($cFetch['rebirth'] == null) $cFetch['rebirth'] = 0;
			echo '<option value="',entScape($cFetch['character_name']),'">',entScape($cFetch['character_name']),' (',entScape($cFetch['rebirth']),')</option>';
		}
		echo '</select><br><input type="submit" name="rebirth" value="Rebirth" /></form>';
	}
	else
	{
		echo 'You don\'t have any characters that can be reborn.';
	}
}
else
{
	echo 'The rebirth system has not been setup.';
}
?>
