<?php
if(isset($_POST['CharList'])&& isset($_POST['buy']))
{
	if(!ctype_digit($_POST['amount']))
	{
		echo 'Amount must be integers only.';
	}
	else
	{
		$query = msquery("SELECT cash.dbo.user_cash.amount, account.dbo.user_profile.login_flag, count(cash.dbo.user_cash.amount) as num from cash.dbo.user_cash left join account.dbo.user_profile on cash.dbo.user_cash.user_no = account.dbo.user_profile.user_no where cash.dbo.user_cash.user_no = '%s' group by cash.dbo.user_cash.amount, account.dbo.user_profile.login_flag", $_SESSION['user_no']);
		$arrayQ = mssql_fetch_array($query);
		if ($arrayQ['num'] == 1)
		{
			if($arrayQ['login_flag'] == '0')
			{
				$charQuery = msquery("SELECT dwMoney, count(dwmoney) as num FROM character.dbo.user_character WHERE user_no = '%s' and character_name = '%s' group by dwmoney", $_SESSION['user_no'], $_POST['CharList']);
				$char = mssql_fetch_array($charQuery);
				if ($char['num'] == 1)
				{
					if($char[0] >= ($_POST['amount'] * $ini['Other']['dilbank.price']))
					{
						msquery("UPDATE character.dbo.user_character SET dwMoney = dwMoney - %s where user_no = '%s' and character_name = '%s'", ($_POST['amount'] * $ini['Other']['dilbank.price']), $_SESSION['user_no'], $_POST['CharList']);
						msquery("UPDATE cash.dbo.user_cash SET amount = amount + %s where user_no = '%s'", $_POST['amount'] ,$_SESSION['user_no']);
						echo 'You have successfully bought ',entScape($_POST['amount']),' coin(s).';
					}
					else
					{
						echo 'You do not have enough dil to buy that many coins.';
					}
				}
				else
				{
					echo 'Character error.';
				}
			}
			else
			{
				echo 'Please logout first.';
			}
		}
		else
		{
			echo 'You have not visted the d-shop in game yet.';
		}
	}
}

$charQuery = msquery("SELECT character_name, dwMoney FROM character.dbo.user_character WHERE user_no = '%s'",$_SESSION['user_no']);
$characters = mssql_num_rows($charQuery);
if ($characters > 0)
{
	echo '
	<br><br>',$ini['Other']['dilbank.price'],' dil is worth 1 coin.
	<form action="?do=',$_GET['do'],'" method="POST">
	How many coins do you want to buy? <input type="text" name="amount" /><br>';
	echo 'Which character do you want to use?<select name="CharList">';
	while ($charList = mssql_fetch_array($charQuery))
	{
		echo '<option value="',entScape($charList['character_name']),'">',entScape($charList['character_name']),' (',entScape($charList['dwMoney']),')</option>';
	}
	echo '
	</select><br>
	<input type="submit" name="buy" value="Buy" />
	</form>';

}
else
{
	echo 'You do not have any characters to buy coins with!';
}
?>
