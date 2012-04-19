<?php
requireExtras();
echo'<a href="?do=',entScape($_GET['do']),'&action=bank">Bank</a>';
if($ini['Other']['dilbank.buyEnabled'] == true)
{
	echo '<a href="?do=',entScape($_GET['do']),'&action=buy">Buy</a>';
}
echo '<br>';
if($_GET['action'] == 'buy' && $ini['Other']['dilbank.buyEnabled'] == true)
{
	if(isset($_POST['buy']) && ($_POST['bType'] == 'coin' || $_POST['bType'] ==  'dil'))
	{
		if(!ctype_digit($_POST['amount']))
		{
			echo 'Amount must be a positive integer only.<br>';
		}
		else
		{
			$query = msquery("SELECT (cash.dbo.user_cash.amount + cash.dbo.user_cash.free_amount) as total, dil ,count(cash.dbo.user_cash.amount) as num from cash.dbo.user_cash left join %s.dbo.userExt on cash.dbo.user_cash.user_no = %s.dbo.userExt.user_no where cash.dbo.user_cash.user_no = '%s' group by cash.dbo.user_cash.amount, cash.dbo.user_cash.free_amount, dil", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
			$arrayQ = mssql_fetch_array($query);
			if ($arrayQ['num'] == 1)
			{
				if($_POST['bType'] == 'coin')
				{
					$cost = $_POST['amount'] * $ini['Other']['dilbank.price'];
					if($arrayQ['dil'] >= $cost)
					{
						msquery("UPDATE %s.dbo.userExt SET dil = dil - %s where user_no = '%s'", $ini['MSSQL']['extrasDB'], $cost, $_SESSION['user_no']);
						msquery("UPDATE cash.dbo.user_cash SET amount = amount + %s where user_no = '%s'", $_POST['amount'], $_SESSION['user_no']);
						echo 'You have successfully bought ',entScape($_POST['amount']),' coin(s).<br>';
					}
					else
					{
						echo 'You do not have enough dil to buy that many coins.<br>';
					}
				}
				else
				{
					if ($arrayQ['total'] >= $_POST['amount'])
					{
						$dil = $_POST['amount'] * $ini['Other']['dilbank.price'];
						msquery("UPDATE %s.dbo.userExt SET dil = dil + %s where user_no = '%s'", $ini['MSSQL']['extrasDB'], $dil, $_SESSION['user_no']);
						msquery("UPDATE cash.dbo.user_cash SET amount = amount - %s where user_no = '%s'", $_POST['amount'], $_SESSION['user_no']);
						echo 'You have successfully bought ',entScape($dil),' dil.<br>';
					}
					else
					{
						echo 'You do not have enough coins to buy that much dil.<br>';
					}
				}
			}
			else
			{
				echo 'You have not visted the d-shop in game yet.<br>';
			}
		}
	}

	$bQuery = msquery("SELECT dil FROM %s.dbo.userExt WHERE user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
	$bFetch = mssql_fetch_array($bQuery);
	echo 'Banked dil: <b>',$bFetch['dil'],'</b>
	<form action="?do=',$_GET['do'],'&action=buy" method="POST">
	<input type="text" name="amount" /> <select name="bType"><option value="dil">Dil(x',entScape($ini['Other']['dilbank.price']),')</option><option value="coin">Coins</option></select>
	<br><input type="submit" name="buy" value="Buy" />
	</form>';

}
else
{
	$charQuery = msquery("SELECT character_name FROM character.dbo.user_character WHERE user_no = '%s'", $_SESSION['user_no']);
	if (mssql_num_rows($charQuery) > 0)
	{
		if($_POST['type'] == 'Deposit' || $_POST['type'] == 'Deposit All' || $_POST['type'] == 'Withdraw' || $_POST['type'] == 'Withdraw Max')
		{
			if(!ctype_digit($_POST['dil']) && $_POST['type'] <> 'Deposit All' && $_POST['type'] != 'Withdraw Max')
			{
				echo 'Dil only consist of whole numbers.';
			}
			elseif ($_POST['dil'] <= '0' && $_POST['type'] <> 'Deposit All' && $_POST['type'] != 'Withdraw Max')
			{
				echo 'Dil must be greater than 0!';

			}
			elseif (empty($_POST['dil']) && $_POST['type'] <> 'Deposit All' && $_POST['type'] != 'Withdraw Max')
			{
				echo 'Dil cannot be empty!';
				
			}
			else
			{
				
				$loginQuery = msquery("SELECT login_flag FROM account.dbo.user_profile WHERE user_no = '%s'", $_SESSION['user_no']);
				$loginFlag = mssql_fetch_array($loginQuery);
				if ($loginFlag['login_flag'] == '0')
				{
					$infoQuery = msquery("SELECT dwMoney, count(dwMoney) as num from character.dbo.user_character where character_name = '%s' and user_no = '%s' group by dwMoney", $_POST['oCharList'], $_SESSION['user_no']);
					$info = mssql_fetch_array($infoQuery);
					if ($info['num'] == 1)
					{
						if($_POST['type'] == 'Withdraw' || $_POST['type'] == 'Withdraw Max')
						{
							$bankedQuery = msquery("SELECT dil from %s.dbo.userExt where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
							$bankedList = mssql_fetch_array($bankedQuery);
							if ($_POST['type'] == 'Withdraw Max')
							{
								if ($bankedList['dil'] >= '1000000000')
								{
									$_POST['dil'] = 1000000000 - $info['dwMoney'];
								}
								else
								{
									$_POST['dil'] = $bankedList['dil'];
								}
							}
							if ($bankedList['dil'] >= $_POST['dil'] && $_POST['dil'] <> '0')
							{
								if (($info['dwMoney'] + $_POST['dil']) <= '1000000000')
								{
									$exp = $bankedList['dil'] - $_POST['dil'];
									msquery("UPDATE %s.dbo.userExt SET dil = '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $exp, $_SESSION['user_no']);
									$exp =  $info['dwMoney'] + $_POST['dil'];
									msquery("UPDATE character.dbo.user_character set dwMoney = '%s' where character_name = '%s'", $exp, $_POST['oCharList']);
									echo 'You have successfully withdrawn ',entScape($_POST['dil']),' Dil to ',entScape($_POST['oCharList']),'.';
								}
								else
								{
									echo 'You cannnot withdraw that much Dil because your character\'s Dil will exceed its maximum value: 1000000000';
								}
							}
							else
							{
								echo 'You don\'t have that much Dil to withdraw!';
							}
						}
						else
						{
							if ($_POST['type'] == 'Deposit All')
							{
								$_POST['dil'] = $info['dwMoney'];
							}
							if ($info['dwMoney'] >= $_POST['dil'] && $_POST['dil'] <> '0')
							{
								$exp = $info['dwMoney'] - $_POST['dil'];
								msquery("UPDATE character.dbo.user_character set dwMoney = '%s' where character_name = '%s'", $exp, $_POST['oCharList']);
								$bankedQuery = msquery("SELECT dil FROM %s.dbo.userExt WHERE user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
								$bankedList = mssql_fetch_array($bankedQuery);
								$exp = $bankedList['dil'] + $_POST['dil'];
								msquery("UPDATE %s.dbo.userExt SET dil = '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $exp, $_SESSION['user_no']);
								echo 'You have successfully banked ',entScape($_POST['dil']),' Dil off of ',entScape($_POST['oCharList']),'.';
							}
							else
							{
								echo 'You don\'t have that much Dil to bank!';
							}
						}
					}
					else
					{
						echo 'Data access error!';
					}
				}
				else
				{
					echo 'You must be logged out of your account!';
				}
				
			}
		}
		$bankedQuery = msquery("SELECT dil FROM %s.dbo.userExt WHERE user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
		$bankedList = mssql_fetch_array($bankedQuery);
		$charQuery = msquery("SELECT character_name, dwMoney FROM character.dbo.user_character WHERE user_no = '%s'", $_SESSION['user_no']);
		echo '<table>
		<form action="?do=',entScape($_GET['do']),'&type=bank" method="POST">
		<tr><td>Banked dil: <b>',entScape($bankedList['dil']),'</b></td></tr><tr><td><select name="oCharList">';
		while ($charList = mssql_fetch_array($charQuery))
		{
			echo '<option value="',entScape($charList['character_name']),'">',entScape($charList['character_name']),' (',entScape($charList['dwMoney']),')</option>';
		}
		echo '</select></td></tr>';
		echo '<tr><td>Dil:<br><input type="text" name="dil" /></td></tr>
		<tr><td><input name="type" type="submit" value="Deposit" /> <input name="type" type="submit" value="Deposit All" /><br>
			<input name="type" type="submit" value="Withdraw" /> <input name="type" type="submit" value="Withdraw Max"/></td></tr>
		</form></table>';
	}
	else
	{
		echo 'You have no characters to preform banking operations with!';
	}

}
?>
