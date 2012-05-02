<?php
$error = null;
if($_GET['action'] == 'change' && isset($_POST['char']) && isset($_POST['cls']))
{
	$cQuery = null;
	if($ini['MSSQL']['extras'] == true)
	{
		$cQuery = msquery("select c.character_no, dwMoney, wLevel, count(r.character_no) as num from character.dbo.user_character c left join %s.dbo.rebirth r on r.character_no = c.character_no where character_name = '%s' and user_no = '%s' group by c.character_no, wLevel, dwMoney", $ini['MSSQL']['extrasDB'], $_POST['char'], $_SESSION['user_no']);	
	}
	else
	{
		$cQuery = msquery("select c.character_no, c.dwMoney, c.wLevel from account.dbo.user_profile up join character.dbo.user_character c on c.user_no = up.user_no where up.user_no = '%s' and c.character_name = '%s'", $_SESSION['user_no'], $_POST['char']);
	}
	$cQuery = $cQuery->fetchAll();
	if(count($cQuery) != 1) 
	{
		$error = 'Character not found on your account.';
	}
	else
	{
		if(isset($ini['Other']['changecls.dil']) && $cQuery[0]['dwMoney'] < $ini['Other']['changecls.dil'])
		{
			$error = 'You do not have enough dil.';
		}
		else
		{
			$cashQuery = msquery("select up.login_flag, (uc.amount + uc.free_amount) as total from account.dbo.user_profile up join cash.dbo.user_cash uc on uc.user_no = up.user_no where up.user_no = '%s' group by up.login_flag, uc.amount, uc.free_amount", $_SESSION['user_no']);
			$cashQuery = $cashQuery->fetchAll();
			if(count($cashQuery) != 1)
			{
				$error = 'Character has not visited d-shop.';
			}
			else
			{
				if($cashQuery[0]['login_flag'] != '0')
				{
					$error = 'Account is online.';
				}
				else
				{
					if(isset($ini['Other']['changecls.coin']) && $cashQuery[0]['total'] < $ini['Other']['changecls.coin'])
					{
						$error = 'You do not have enough D-Coins.';
					}
					else
					{
						$bQuery = msquery("select character_no from character.dbo.user_suit where character_no = '%s'", $cQuery[0]['character_no']);
						$bQuery = $bQuery->fetchAll();
						if(count($bQuery) > 0)
						{
							$error = 'Please un-equip all items.';
						}

					}
				}
			}

		}
	}

	if($error == null)
	{
		$stats = ($cQuery[0]['wLevel'] - 1) * 5;
		$sp = ($cQuery[0]['wLevel'] - 1) * 1;
		if($ini['MSSQL']['extras'] == true)
		{
			if($cQuery[0]['num'] > 0)
			{
				for($i = 0; $i < ($cQuery[0]['num']+1); $i++)
				{
					$rArray = explode(',',$ini['Other']['rebirth'][$i]);
					$stats += $rArray[1];
					if($ini['Other']['rebirth.SkillPoint'] == false)
					{
						$sp += ($rArray[0] - 1) * 1;
					}
				}

			}
		}
		$_POST['cls'] = (int)$_POST['cls'];
		if($_POST['cls'] < 0 || $_POST['cls'] > 5) $_POST['cls'] = 0;
		$sQuery = msquery("SELECT wStr, wCon, wDex, wSpr from character.dbo.user_character where character_no = 'DEKARON%s000001'", $_POST['cls']); 
		$sFetch = $sQuery->fetch();
		$dil = 0;
		if(isset($ini['Other']['changecls.dil'])) $dil = $ini['Other']['changecls.dil'];
		if(isset($ini['Other']['changecls.coin']))
		{
			msquery("UPDATE cash.dbo.user_cash SET amount = amount - '%s' where user_no = '%s'", $ini['Other']['changecls.coin'], $_SESSION['user_no']);
		}
		msquery("DELETE FROM character.dbo.user_slot WHERE character_no = '%s'", $cQuery[0]['character_no']);
		msquery("DELETE FROM character.dbo.user_skill WHERE character_no = '%s'", $cQuery[0]['character_no']);
		msquery("UPDATE character.dbo.user_character SET wStr = '%s', wSpr = '%s', wCon = '%s', wDex = '%s', wStatPoint = '%s', byPCClass = '%s', wSkillPoint = '%s', dwMoney = dwMoney - '%s' where character_no = '%s'", $sFetch['wStr'], $sFetch['wSpr'], $sFetch['wCon'], $sFetch['wDex'], $stats, $_POST['cls'], $sp, $dil, $cQuery[0]['character_no']);		
		$error = 'Class was successfully changed for '.$_POST['char'].'.';
	}
}
$cQuery = msquery("select c.character_name from account.dbo.user_profile up left join character.dbo.user_character c on c.user_no = up.user_no where up.user_no = '%s'", $_SESSION['user_no']);
$cQuery = $cQuery->fetchAll();
if(count($cQuery) > 0)
{
	if(isset($ini['Other']['changecls.dil'])) echo 'Dil cost: ',entScape($ini['Other']['changecls.dil']),'<br>';
	if(isset($ini['Other']['changecls.coin'])) echo 'D-Coin cost: ',entScape($ini['Other']['changecls.coin'],'<br>');

	echo '<form action="?do=',entScape($_GET['do']),'&action=change" method="POST">';
	echo 'Character: <select name="char">';
	foreach($cQuery as $cFetch)
	{
		if($cFetch['rebirth'] == null) $cFetch['rebirth'] = 0;
		echo '<option value="',entScape($cFetch['character_name']),'">',entScape($cFetch['character_name']),'</option>';
	}
	echo '</select><br>
	Class: <select name="cls">
	<option value="0">Knight</option>
	<option value="1">Hunter</option>
	<option value="2">Mage</option>
	<option value="3">Summoner</option>
	<option value="4">Segnale</option>
	<option value="5">Bagi</option>
	</select><br>
	<input type="submit" value="Change" /></form>';
}
else
{
	$error = 'You do not have any characters to change classes with.';
}
if($error != null)
{
	echo entScape($error);
}

?>
