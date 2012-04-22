<?php

echo '<form action="?do=',entScape($_GET['do']),'" method="POST">Character: <input type="text" name="schar" /> <input type="submit" name="search" value="Search"/></form>';
if((isset($_POST['search']) || (isset($_POST['update']) && !empty($_POST['oChar']))) && !empty($_POST['schar']))
{
	if(isset($_POST['update']))
	{
		$cQuery = msquery("select character_name from character.dbo.user_character where character_name ='%s'",$_POST['oChar']);
		$error = null;
		if(mssql_num_rows($cQuery) == 1)
		{
			$result = msquery("select account.dbo.user_profile.login_flag from account.dbo.user_profile left join character.dbo.user_character on character.dbo.user_character.user_no = account.dbo.user_profile.user_no WHERE account.dbo.user_profile.login_flag = '1100' and character_name = '%s'", $_POST['oChar']);			
			if(mssql_num_rows($result) == 0)
			{
				foreach($_POST as $key=>$val)
				{
					if($key != 'schar' && $key != 'oChar' && $key != 'update' && !ctype_digit($val))
					{
						$error = 'All values except character must be digits.';
					}
					if($error != null) break;
				}
				if($error == null)
				{
					msquery("UPDATE character.dbo.user_character SET dwMoney = '%s',dwStoreMoney = '%s',dwStorageMoney = '%s',nHP = '%s',nMP = '%s',wStr = '%s',wCon = '%s',wSpr = '%s',wStatPoint = '%s',wSkillPoint = '%s',wLevel = '%s',byPCClass = '%s',wPKCount = '%s',nShield = '%s',dwPVPPoint = '%s',wWinRecord = '%s',wLoseRecord = '%s' where character_name = '%s'",$_POST['dwMoney'], $_POST['dwStoreMoney'], $_POST['dwStorageMoney'], $_POST['nHP'], $_POST['nMP'], $_POST['wStr'], $_POST['wCon'], $_POST['wSpr'], $_POST['wStatPoint'], $_POST['wSkillPoint'], $_POST['wLevel'], $_POST['byPCClass'], $_POST['wPKCount'], $_POST['nShield'], $_POST['dwPVPPoint'], $_POST['wWinRecord'], $_POST['wLoseRecord'] ,$_POST['oChar']);
					echo 'Character updated successfully.<br>';
					if($_POST['schar'] != $_POST['oChar'])
					{
						$cQuery = msquery("select character_name from character.dbo.user_character where character_name = '%s'", $_POST['schar']);
						if(mssql_num_rows($cQuery) == 0)
						{
							msquery("UPDATE character.dbo.user_character set character_name = '%s' where character_name = '%s'", $_POST['schar'], $_POST['oChar']);
						}
						else
						{
							$_POST['schar'] = $_POST['oChar'];
							echo 'Unable to change character name because it already exists.<br>';
						}
					}
				}
				else
				{
					echo $error;
				}
			}
			else
			{
				echo 'Account is online.';
			}
		}
		else
		{
			echo 'Could not find character to update.<br>';
		}
	}
	$cQuery = msquery("select dwMoney, dwStoreMoney, dwStorageMoney, nHP, nMP, wStr, wDex, wCon, wSpr, wStatPoint, wSkillPoint, wLevel, byPCClass, wPKCount, nShield, dwPVPPoint, wWinRecord, wLoseRecord, count(character_name) as num from character.dbo.user_character where character_name = '%s' group by dwMoney, dwStoreMoney, dwStorageMoney, nHP, nMP, wStr, wDex, wCon, wSpr, wStatPoint, wSkillPoint, wLevel, byPCClass, wPKCount, nShield, dwPVPPoint, wWinRecord, wLoseRecord", $_POST['schar']);
	$cFetch = mssql_fetch_array($cQuery);
	if ($cFetch['num'] == '1')
	{
		echo '<form action="?do=',entScape($_GET['do']),'" method="POST">Character: <input type="text" name="schar" value="',entScape($_POST['schar']),'"/><input type="hidden" name="oChar" value="',entScape($_POST['schar']),'" /><br>
		Money: <input type="text" name="dwMoney" value="',entScape($cFetch['dwMoney']),'"/><br>
		Storage money: <input type="text" name="dwStorageMoney" value="',entScape($cFetch['dwStorageMoney']),'"/><br>
		Store money: <input type="text" name="dwStoreMoney" value="',entScape($cFetch['dwStoreMoney']),'"/><br>
		Class: <select name="byPCClass">';
		echo '<option value="0" ';
		if($cFetch['byPCClass'] == '0') echo 'selected="selected"';
		echo'>Knight</option><option value="1" ';
		if($cFetch['byPCClass'] == '1') echo 'selected="selected"';
		echo'>Hunter</option><option value="2" ';
		if($cFetch['byPCClass'] == '2') echo 'selected="selected"';
		echo'>Mage</option><option value="3" ';
		if($cFetch['byPCClass'] == '3') echo 'selected="selected"';
		echo'>Summoner</option><option value="4" ';
		if($cFetch['byPCClass'] == '4') echo 'selected="selected"';
		echo'>Segnale</option><option value="5" ';
		if($cFetch['byPCClass'] == '5') echo 'selected="selected"';
		echo'>Bagi</option><option value="6" ';
		if($cFetch['byPCClass'] == '6') echo 'selected="selected"';
		echo'>Aloken</option>';
		echo'</select><br>Level: <input type="text" name="wLevel" value="',entScape($cFetch['wLevel']),'"/><br>
		Shield: <input type="text" name="nShield" value="',entScape($cFetch['nShield']),'"/><br>
		HP: <input type="text" name="nHP" value="',entScape($cFetch['nHP']),'"/><br>
		MP: <input type="text" name="nMP" value="',entScape($cFetch['nMP']),'"/><br>
		Stat points: <input type="text" name="wStatPoint" value="',entScape($cFetch['wStatPoint']),'"/><br>
		Str: <input type="text" name="wStr" value="',entScape($cFetch['wStr']),'"/><br>
		Dex: <input type="text" name="wDex" value="',entScape($cFetch['wDex']),'"/><br>
		Con: <input type="text" name="wCon" value="',entScape($cFetch['wCon']),'"/><br>
		Spr: <input type="text" name="wSpr" value="',entScape($cFetch['wSpr']),'"/><br>
		Skill points: <input type="text" name="wSkillPoint" value="',entScape($cFetch['wSkillPoint']),'"/><br>
		PKs: <input type="text" name="wPKCount" value="',entScape($cFetch['wPKCount']),'"/><br>
		PVP Points: <input type="text" name="dwPVPPoint" value="',entScape($cFetch['dwPVPPoint']),'"/><br>
		Wins: <input type="text" name="wWinRecord" value="',entScape($cFetch['wWinRecord']),'"/><br>
		Losses: <input type="text" name="wLoseRecord" value="',entScape($cFetch['wLoseRecord']),'"/><br>
		<input type="submit" name="update" value="Update"></form>';
	}
	else
	{
		echo 'Character not found.';
	}
}
?>
