<?php
requireExtras();
echo '<table><tr><td><a href="?do=',entScape($_GET['do']),'&type=bank">Bank</a>';
if($ini['Other']['expbank.giftEnabled'] == true)
{
	echo '<a href="?do=',entScape($_GET['do']),'&type=gift">Gift Experience</a>';
}
if($ini['Other']['expbank.listEnabled'] == true)
{
	echo '<a href="?do=',entScape($_GET['do']),'&type=list">List Experience</a><a href="?do=',entScape($_GET['do']),'&type=listing">Listings</a>';
}
echo '</td></tr></table>';
if($_GET['type'] == 'gift' && $ini['Other']['expbank.giftEnabled'] == true)
{
	if($_POST['type'] == 'Gift' && !empty($_POST['sendTo']) && !empty($_POST['sendExp']))
	{

		if(ctype_digit($_POST['sendExp']))
		{
			$query = msquery("SELECT (amount + free_amount) as coins, exp from cash.dbo.user_cash left join %s.dbo.userExt on cash.dbo.user_cash.user_no = %s.dbo.userExt.user_no where cash.dbo.user_cash.user_no = '%s'", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
			$fetch = $query->fetchAll();
			if (count(fetch) == 1)
			{
				if ($fetch[0]['coins'] >= $ini['Other']['expbank.giftprice'] && $fetch[0]['exp'] >= $_POST['sendExp'])
				{
					$query2 = msquery("SELECT user_no, count(user_no) as num from character.dbo.user_character where character_name = '%s' group by user_no", $_POST['sendTo']);
					$fetch2 = $query2->fetchAll();
					if ($fetch2[0]['num'] == 1)
					{
						msquery("UPDATE cash.dbo.user_cash SET amount = amount - '%s' where user_no = '%s'", $ini['Other']['expbank.giftprice'], $_SESSION['user_no']);
						msquery("UPDATE %s.dbo.userExt SET exp = exp - '%s'where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_POST['sendExp'], $_SESSION['user_no']);
						msquery("UPDATE %s.dbo.userExt SET exp = exp + '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_POST['sendExp'], $fetch2[0]['user_no']);
						echo entScape($_POST['sendTo']),' has successfully received ',entScape($_POST['sendExp']),' experience in their bank!';
					}
					else
					{
						echo 'Character does not exist!';
					}
				}
				else
				{
					echo 'You do not have enough coins and/or that much experience to gift!';
				}
			}
			else
			{
				echo 'You have not visited the d-shop in game.';
			}
		}
		else
		{
			echo 'Experience only consists of whole numbers.';
		}
	}
	$bankedQuery = msquery("SELECT exp FROM %s.dbo.userExt WHERE user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
	$bankedList = $bankedQuery->fetch();
	echo '<table><form name="processexp" action="?do=',entScape($_GET['do']),'&type=gift" method="POST">
	<tr><td>Banked Experence: <b>',entScape($bankedList['exp']),'</b></td></tr>
	<tr><td>Character:<br><input type="text" name="sendTo"></input></td></tr>
	<tr><td>Experience:<br><input type="text" name="sendExp"></input></td></tr>
	<tr><td><input type="submit" name="type" value="Gift"></input></td></tr>
	</form></table>Gifting experience requires ',entScape($ini['Other']['expbank.giftprice']),' D-Coin(s).';

}
elseif($_GET['type'] == 'list' && $ini['Other']['expbank.listEnabled'] == true)
{
	if($_POST['type'] == 'List')
	{

		if(!ctype_digit($_POST['exp']) || !ctype_digit($_POST['dcoins']))
		{
			echo 'Experience and D-Coins only consist of whole numbers.';
			
		}
		elseif ($_POST['exp'] <= '0')
		{
			echo 'Experience must be greater than 0!';
			
		}
		elseif (empty($_POST['exp']) || empty($_POST['dcoins']))
		{
			echo 'Experience and D-Coins cannot be empty!';
		
		}
			elseif ($_POST['exp'] > '9223372036854775807' || $_POST['dcoins'] > '2147483647')
		{
			echo 'Experience value cannot exceed 9223372036854775807 and D-Coin value cannot exceed 2147483647!';

		}
		else
		{
			$query = msquery("SELECT amount from cash.dbo.user_cash where cash.dbo.user_cash.user_no = '%s'", $_SESSION['user_no']);
			if (count($query->fetchAll()) == 1)
			{
				$infoQuery = msquery("SELECT exp from %s.dbo.userExt where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
				$info = $infoQuery->fetch();
				if ($info['exp'] >= $_POST['exp'])
				{
					$exp = $info['exp'] - $_POST['exp'];
					msquery("UPDATE %s.dbo.userExt SET exp = '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], ($info['exp'] - $_POST['exp']), $_SESSION['user_no']);
					msquery("INSERT INTO %s.dbo.blist (aid, exp, coins) values ('%s','%s','%s')", $ini['MSSQL']['extrasDB'], $_SESSION['user_no'], $_POST['exp'], $_POST['dcoins']);
					echo 'You have successfully listed ',entScape($_POST['exp']),' experience for ',entScape($_POST['dcoins']),' D-Coins.';
				}
				else
				{
					echo 'You don\'t have that much experience in your bank to list!';
				}
			}
			else
			{
				echo 'You have not visited the D-Shop in game.';
			}
		}
	}
	$bankedQuery = msquery("SELECT exp FROM %s.dbo.userExt WHERE user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
	$bankedList = $bankedQuery->fetch();
	echo '<table><form name="processexp" action="?do=',entScape($_GET['do']),'&type=list" method="POST"><tr><td>Banked Experence: <b>',entScape($bankedList['exp']),'</b></td></tr>';
	echo '<tr><td>Experience:<br><input type="text" name="exp" /></td></tr>
	<tr><td>D-Coins:<br><input type="text" name="dcoins" /></td></tr>
	<tr><td><input name="type" type="submit" value="List" /></td></tr>
	</form></table>';

}
elseif($_GET['type'] == 'listing' && $ini['Other']['expbank.listEnabled'] == true)
{
	$acctQuery = msquery("SELECT (amount + free_amount) as total from cash.dbo.user_cash where user_no = '%s'", $_SESSION['user_no']);
	$acctCoins = $acctQuery->fetchAll();
	if (count($acctCoins) == 1)
	{
		if(($_GET['action'] == 'delete' || $_GET['action'] == 'buy') && !empty($_GET['aid']) && ctype_digit($_GET['aid']))
		{
			$auQuery = msquery("Select *,count(auctionID) as num FROM %s.dbo.blist where auctionID = '%s' group by auctionID, aid, exp, coins", $ini['MSSQL']['extrasDB'], $_GET['aid']);
			$auInfo = $auQuery->fetch();
			if ($auInfo['num'] == '1')
			{
				if($_GET['action'] == 'delete')
				{
					if ($auInfo['aid'] == $_SESSION['user_no'])
					{
						msquery("DELETE FROM %s.dbo.blist where auctionID = '%s'", $ini['MSSQL']['extrasDB'], $auInfo['auctionID']);
						$bankQuery = msquery("SELECT exp from %s.dbo.userExt where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
						$bankInfo = $bankQuery->fetch();
						$exp = $bankInfo['exp'] + $auInfo['exp'];
						msquery("UPDATE %s.dbo.userExt SET exp = '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $exp, $_SESSION['user_no']);
						echo 'Auction ',entScape($auInfo['auctionID']),' successfully deleted and ',entScape($auInfo['exp']),' experience returned to your bank.';
					}
					else
					{
						echo 'Unable to delete listing because you do not own this auction.';
					}
				}
				else
				{
					if ($auInfo['aid'] != $_SESSION['user_no'])
					{
						if ( $acctCoins[0]['total'] >= $auInfo['coins'])
						{
							msquery("UPDATE cash.dbo.user_cash SET amount = amount - '%s' where user_no = '%s'", $auInfo['coins'], $_SESSION['user_no']);
							msquery("DELETE FROM %s.dbo.blist where auctionID = '%s'", $ini['MSSQL']['extrasDB'], $auInfo['auctionID']);
							msquery("UPDATE cash.dbo.user_cash SET amount = amount + '%s' where user_no = '%s'", $auInfo['coins'], $auInfo['aid']);
							msquery("UPDATE %s.dbo.userExt SET exp = exp + '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $auInfo['exp'], $_SESSION['user_no']);
							echo 'Auction ',entScape($auInfo['auctionID']),' successfully bought and ',entScape($auInfo['exp']),' experience has been added to your bank.';
						}
						else
						{
							echo 'You do not have enough coins to buy this auction.';
						}
					}
					else
					{
						echo 'Unable to buy listing because you own this auction.';
					}
				}
			}
			else
			{
			echo 'Auction no longer exists.';
			}
		}	
		$listQuery = msquery("SELECT * from %s.dbo.blist", $ini['MSSQL']['extrasDB']);
		$acctQuery = msquery("SELECT (amount + free_amount) as total from cash.dbo.user_cash where user_no = '%s'", $_SESSION['user_no']);	
		$acctCoins = $acctQuery->fetch();
		$listQuery = $listQuery->fetchAll();
		echo '<table>
		<tr><td colspan="3">Your D-Coins: ',entscape($acctCoins['total']),'</td></tr>
		<tr><th>Experience</th><th>Price</th></tr>';
		foreach ($listQuery as $listings)
		{
			echo '<tr><td>',entScape($listings['exp']),'</td><td>',entScape($listings['coins']),'</td>';
			if ($listings['aid'] == $_SESSION['user_no'])
			{
				echo '<td><a href="?do=',entScape($_GET['do']),'&type=listing&action=delete&aid=',entScape($listings['auctionID']),'">Delete</a></td></tr>';
			}
			elseif($listings['aid'] <> $_SESSION['user_no'] && $listings['3'] <= $acctCoins['total'])
			{
				echo '<td><a href="?do=',entScape($_GET['do']),'&type=listing&action=buy&aid=',entScape($listings['auctionID']),'">Buy</a></td></tr>';
			}
			else
			{
			echo '</tr>';
			}
		}
		echo '</table>';
	}
	else
	{
		echo 'You have not visited the d-shop in game.';
	}
}
else 
{
	$charQuery = msquery("SELECT character_name FROM character.dbo.user_character WHERE user_no = '%s'", $_SESSION['user_no'])->fetchAll();
	if (count($charQuery) > 0)
	{
		if($_POST['type'] == 'Deposit' || $_POST['type'] == 'Deposit All' || $_POST['type'] == 'Withdraw' || $_POST['type'] == 'Withdraw Max')
		{
			if(!ctype_digit($_POST['exp']) && $_POST['type'] <> 'Deposit All' && $_POST['type'] != 'Withdraw Max')
			{
				echo 'Experience only consist of whole numbers.';
			}
			elseif ($_POST['exp'] <= '0' && $_POST['type'] <> 'Deposit All' && $_POST['type'] != 'Withdraw Max')
			{
				echo 'Experience must be greater than 0!';

			}
			elseif (empty($_POST['exp']) && $_POST['type'] <> 'Deposit All' && $_POST['type'] != 'Withdraw Max')
			{
				echo 'Experience cannot be empty!';
				
			}
			else
			{
				
				$loginQuery = msquery("SELECT login_flag FROM account.dbo.user_profile WHERE user_no = '%s'", $_SESSION['user_no']);
				$loginFlag = $loginQuery->fetch();
				if ($loginFlag['login_flag'] == '0')
				{
					$infoQuery = msquery("SELECT dwExp, count(dwExp) as num from character.dbo.user_character where character_name = '%s' and user_no = '%s' group by dwExp", $_POST['oCharList'], $_SESSION['user_no']);
					$info = $infoQuery->fetch();
					if ($info['num'] == 1)
					{
						if($_POST['type'] == 'Withdraw' || $_POST['type'] == 'Withdraw Max')
						{
							$bankedQuery = msquery("SELECT exp from %s.dbo.userExt where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
							$bankedList = $bankedQuery->fetch();
							if ($_POST['type'] == 'Withdraw Max')
							{
								if ($bankedList['exp'] >= '2147483648')
								{
									$_POST['exp'] = 2147483648 - $info['dwExp'];
								}
								else
								{
									$_POST['exp'] = $bankedList['exp'];
								}
							}
							if ($bankedList['exp'] >= $_POST['exp'] && $_POST['exp'] <> '0')
							{
								if (($info['dwExp'] + $_POST['exp']) <= '2147483648')
								{
									$exp = $bankedList['exp'] - $_POST['exp'];
									msquery("UPDATE %s.dbo.userExt SET exp = '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $exp, $_SESSION['user_no']);
									$exp =  $info['dwExp'] + $_POST['exp'];
									msquery("UPDATE character.dbo.user_character set dwEXP = '%s' where character_name = '%s'", $exp, $_POST['oCharList']);
									echo 'You have successfully withdrawn ',entScape($_POST['exp']),' experience to ',entScape($_POST['oCharList']),'.';
								}
								else
								{
									echo 'You cannnot withdraw that much experience because your character\'s experience will exceed its maximum value: 2147483648';
								}
							}
							else
							{
								echo 'You don\'t have that much experience to withdraw!';
							}
						}
						else
						{
							if ($_POST['type'] == 'Deposit All')
							{
								$_POST['exp'] = $info['dwExp'];
							}
							if ($info['dwExp'] >= $_POST['exp'] && $_POST['exp'] <> '0')
							{
								$exp = $info['dwExp'] - $_POST['exp'];
								msquery("UPDATE character.dbo.user_character set dwEXP = '%s' where character_name = '%s'", $exp, $_POST['oCharList']);
								$bankedQuery = msquery("SELECT exp FROM %s.dbo.userExt WHERE user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
								$bankedList = $bankedQuery->fetch();
								$exp = $bankedList['exp'] + $_POST['exp'];
								msquery("UPDATE %s.dbo.userExt SET exp = '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $exp, $_SESSION['user_no']);
								echo 'You have successfully banked ',entScape($_POST['exp']),' experience off of ',entScape($_POST['oCharList']),'.';
							}
							else
							{
								echo 'You don\'t have that much experience to bank!';
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
		$bankedQuery = msquery("SELECT exp FROM %s.dbo.userExt WHERE user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
		$bankedList = $bankedQuery->fetch();
		$charQuery = msquery("SELECT character_name, dwExp FROM character.dbo.user_character WHERE user_no = '%s'", $_SESSION['user_no'])->fetchAll();
		echo '<table>
		<form name="processexp" action="?do=',entScape($_GET['do']),'&type=bank" method="POST">
		<tr><td>Banked Experence: <b>',entScape($bankedList['exp']),'</b></td></tr><tr><td><select name="oCharList">';
		foreach ($charQuery as $charList)
		{
			echo '<option value="',entScape($charList['character_name']),'">',entScape($charList['character_name']),' (',entScape($charList['dwExp']),')</option>';
		}
		echo '</select></td></tr>';
		echo '<tr><td>Experience:<br><input type="text" name="exp" /></td></tr>
		<tr><td><input name="type" type="submit" value="Deposit" /> <input name="type" type="submit" value="Deposit All" /><br>
			<input name="type" type="submit" value="Withdraw" /> <input name="type" type="submit" value="Withdraw Max"/></td></tr>
		</form></table>';
	}
	else
	{
		echo 'You have no characters to preform banking operations with!';
	}
}
echo '</table>';
?>
