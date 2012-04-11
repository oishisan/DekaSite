<?php
echo '<table><form action="?do=coinmanage" method="POST">
	<tr><td>Character Name:<br><input type="text" name="charname"></td></tr>
	<tr><td>Give Coins (+):<br><input type=text name="coinsP" value="0"></td></tr>
	<tr><td>Take Coins (-):<br><input type=text name="coinsM" value="0"></td></tr>
	<tr><td colspan="2"><input type=submit name="select" value="Update"></td></tr></form></table>';
 
if(!empty($_POST['select']) && !empty($_POST['charname'])) 
{
	if(!ctype_digit($_POST['coinsP']) || !ctype_digit($_POST['coinsM']))
	{
		echo 'Coin amount can only be a positive integer.';
	} 
	else
	{
		$query = msquery("select count(amount) as num from cash.dbo.user_cash join character.dbo.user_character on character.dbo.user_character.user_no = cash.dbo.user_cash.user_no where character_name= '%s'", $_POST['charname']);
		$fetchQ = mssql_fetch_array($query);
		if ($fetchQ['num'] == 1)
		{
			msquery("update cash.dbo.user_cash set amount = amount + '%s' - '%s' from cash.dbo.user_cash join character.dbo.user_character on character.dbo.user_character.user_no = cash.dbo.user_cash.user_no where character_name= '%s'", $_POST['coinsP'], $_POST['coinsM'], $_POST['charname']);
			echo entScape($_POST['charname']),' was given ',entScape($_POST['coinsP']),' coins and had ',entScape($_POST['coinsM']),' coins taken.';
		}
		else
		{
			echo 'Character has not visited d-shop or does not exist.';
		}
	}
}

?>
