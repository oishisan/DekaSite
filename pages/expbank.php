<?php
requireExtras();
echo '<table><tr><td colspan="4">Experience Bank</td></tr><tr><td><a href=?do=',entScape($_GET['do']),'&type=bank target=_self>Visit Bank</a> | <a href=?do=',entScape($_GET['do']),'&type=gift target=_self>Gift Experience</a> | <a href=?do=',entScape($_GET['do']),'&type=list target=_self>List Experience</a> | <a href=?do=',entScape($_GET['do']),'&type=listing target=_self>Check Listings</a></td></tr></table>';
if($_GET['type'] == 'bank' || empty($_GET['type'])) 
{
	$charQuery = msquery("SELECT character_name, dwExp FROM character.dbo.user_character WHERE user_no = '%s'", $_SESSION['user_no']);
	$characters = mssql_num_rows($charQuery);
	if ($characters > 0)
	{
		if($_POST['type'] == 'Deposit' || $_POST['type'] == 'Deposit All')
		{
			if(!ctype_digit($_POST['exp']) && $_POST['type'] <> 'Deposit All')
			{
				echo 'Experience only consist of whole numbers.';
				exit;
			}
			elseif ($_POST['exp'] <= '0' && $_POST['type'] <> 'Deposit All')
			{
				echo 'Experience must be greater than 0!';
				exit;
			}
			elseif (empty($_POST['exp']) && $_POST['type'] <> 'Deposit All')
			{
				echo 'Experience cannot be empty!';
				exit;
			}
			else
			{
				
				$loginQuery = 	msquery("SELECT login_flag FROM account.dbo.user_profile WHERE user_no = '%s'", $_SESSION['user_no']);
				$loginFlag = mssql_fetch_array($loginQuery);
				if ($loginFlag['login_flag'] == '0')
				{
					$infoQuery = msquery("SELECT dwExp from character.dbo.user_character where character_name = '%s' and user_no = '%s'", $_POST['oCharList'], $_SESSION['user_no']);
					$isAcct = mssql_num_rows($infoQuery);
					if ($isAcct == '1')
					{
						$info = mssql_fetch_array($infoQuery);
						if ($_POST['type'] == 'Deposit All')
						{
							$_POST['exp'] = $info[0];
						}
						if ($info[0] >= $_POST['exp'] && $_POST['exp'] <> '0')
						{
							$ip=$_SERVER['REMOTE_ADDR'];
							$exp = $info[0] - $_POST['exp'];
							msquery("UPDATE character.dbo.user_character set dwEXP = '%s' where character_name = '%s'", $exp, $_POST['oCharList']);
							$bankedQuery = msquery("SELECT exp FROM %s.dbo.userExt WHERE user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
							$bankedList = mssql_fetch_array($bankedQuery);
							$exp = $bankedList[0] + $_POST['exp'];
							msquery("UPDATE %s.dbo.userExt SET exp = '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $exp, $_SESSION['user_no']);
							echo 'You have successfully banked ',entScape($_POST['exp']),' experience off of ',entScape($_POST['oCharList']),'.';
						}
						else
						{
							echo 'You don\'t have that much experience to bank!';
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
		if($_POST['type'] == 'Withdraw' || $_POST['type'] == 'Withdraw Max')
		{
			if(!ctype_digit($_POST['exp']) && $_POST['type'] <> 'Withdraw Max')
			{
				echo 'Experience only consist of whole numbers.';
				exit;
			}
			elseif ($_POST['exp'] <= '0' && $_POST['type'] <> 'Withdraw Max')
			{
				echo 'Experience must be greater than 0!';
				exit;
			}
			elseif (empty($_POST['exp']) && $_POST['type'] <> 'Withdraw Max')
			{
				echo 'Experience cannot be empty!';
				exit;
			}
			else
			{
				$loginQuery = msquery("SELECT login_flag FROM account.dbo.user_profile WHERE user_no = '%s'", $_SESSION['user_no']);
				$loginFlag = mssql_fetch_array($loginQuery);
				if ($loginFlag['login_flag'] == '0')
				{
					$infoQuery = msquery("SELECT dwExp from character.dbo.user_character where character_name = '%s' and user_no = '%s'", $_POST['oCharList'], $_SESSION['user_no']);
					$isAcct = mssql_num_rows($infoQuery);
					if ($isAcct == '1')
					{
						$info = mssql_fetch_array($infoQuery);
						$bankedQuery = msquery("SELECT exp from %s.dbo.userExt where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
						$bankedList = mssql_fetch_array($bankedQuery);
						if ($_POST['type'] == 'Withdraw Max')
						{
							if ($bankedList[0] >= '2147483648')
							{
								$_POST['exp'] = 2147483648 - $info[0];
							}
							else
							{
								$_POST['exp'] = $bankedList[0];
							}
						}
						if ($bankedList[0] >= $_POST['exp'] && $_POST['exp'] <> '0')
						{
							if (($info[0] + $_POST['exp']) <= '2147483648')
							{
								$ip=$_SERVER['REMOTE_ADDR'];
								$exp = $bankedList[0] - $_POST['exp'];
								msquery("UPDATE %s.dbo.userExt SET exp = '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $exp, $_SESSION['user_no']);
								$exp =  $info[0] + $_POST['exp'];
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
		$bankedList = mssql_fetch_array($bankedQuery);
		echo '<table><form name=processexp action=?do=',entScape($_GET['do']),'&type=bank method=POST><tr><tr><td>Banked Experence: <b>',entScape($bankedList[0]),'</b></td></tr><tr><td><select name=oCharList>';
		while ($charList = mssql_fetch_array($charQuery))
		{
			echo '<option value="',entScape($charList['character_name']),'">',entScape($charList['character_name']),' (',entScape($charList['dwExp']),')</option>';
		}
		echo '</select></td></tr>';
		echo '<tr><td>Experience:<br><input type=text name=exp /></td></tr><tr><td><input name=type type=submit value=Deposit /> <input name=type type=submit value="Deposit All" /><br><input name=type type=submit value=Withdraw /> <input name=type type=submit value="Withdraw Max"/></td></tr></form></table>';
	}
	else
	{
		echo 'You have no characters to preform banking operations with!';
	}
}
elseif($_GET['type'] == 'gift')
{
if( $_POST['type'] == 'Gift' && isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token'] && !empty($_POST['sendTo']) && !empty($_POST['sendExp']))
{
	unset($_SESSION['token']);
	echo '<table>';
	if(ctype_digit($_POST['sendExp']))
	{
		$query = msquery("SELECT (amount + free_amount) as coins, exp from cash.dbo.user_cash left join %s.dbo.userExt on cash.dbo.user_cash.user_no = %s.dbo.userExt.user_no where cash.dbo.user_cash.user_no = '%s'", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
		$count = mssql_num_rows($query);
		if ($count == 1)
		{
			$fetch = mssql_fetch_array($query);
			if ($fetch[0] >= $ini['Other']['expbank.giftprice'] && $fetch[1] >= $_POST['sendExp'])
			{
				$query2 = msquery("SELECT user_no, count(user_no) as num from character.dbo.user_character where character_name = '%s' group by user_no", $_POST['sendTo']);
				$fetch2 = mssql_fetch_array($query2);
				if ($fetch2['num'] == 1)
				{
					msquery("UPDATE cash.dbo.user_cash SET amount = amount - '%s' where user_no = '%s'", $ini['Other']['expbank.giftprice'], $_SESSION['user_no']);
					msquery("UPDATE %s.dbo.userExt SET exp = exp - '%s'where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_POST['sendExp'], $_SESSION['user_no']);
					msquery("UPDATE %s.dbo.userExt SET exp = exp + '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_POST['sendExp'], $fetch2['user_no']);
					echo '<tr><td>',entScape($_POST['sendTo']),' has successfully received ',entScape($_POST['sendExp']),' experience in their bank!</td></tr>';
				}
				else
				{
					echo '<tr><td>Character does not exist!</td></tr>';
				}
			}
			else
			{
				echo '<tr><td>You do not have enough coins and/or that much experience to gift!</td></tr>';
			}
		}
		else
		{
			echo '<tr><td>You have not visited the d-shop and, therefore, do not have enough coins!</td></tr>';
		}
	}
	else
	{
		echo '<tr><td>Experience only consists of whole numbers!</td></tr>';
	}
	echo '</table>';
}
	unset($_SESSION['token']);
	$_SESSION['token'] = sha1(microtime(true).uniqid($_SERVER['REMOTE_ADDR'], true).rand());
	$bankedQuery = msquery("SELECT exp FROM %s.dbo.userExt WHERE user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
	$bankedList = mssql_fetch_array($bankedQuery);
	echo '<table><form name=processexp action=?do=',entScape($_GET['do']),'&type=gift method=POST>
	<tr><td>Banked Experence: <b>',entScape($bankedList[0]),'</b></td></tr>
	<tr><td>Character:<br><input type=text name=sendTo></input></td></tr>
	<tr><td>Experience:<br><input type=text name=sendExp></input></td></tr>
	<tr><td><input type=hidden name=token value="',entScape($_SESSION['token']),'"></input><input type=submit name=type value=Gift></input></td></tr>
	</form></table>Gifting experience requires ',entScape($ini['Other']['expbank.giftprice']),' D-Coin(s).';

}
elseif($_GET['type'] == 'list')
{
if($_POST['type'] == 'List' && isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token'])
{
	unset($_SESSION['token']);
	if(!ctype_digit($_POST['exp']) || !ctype_digit($_POST['dcoins']))
	{
		echo 'Experience and D-Coins only consist of whole numbers.';
		exit;
	}
	elseif ($_POST['exp'] <= '0')
	{
		echo 'Experience must be greater than 0!';
		exit;
	}
	elseif (empty($_POST['exp']) || empty($_POST['dcoins']))
	{
		echo 'Experience and D-Coins cannot be empty!';
		exit;
	}
		elseif ($_POST['exp'] > '9223372036854775807' || $_POST['dcoins'] > '2147483647')
	{
		echo 'Experience value cannot exceed 9223372036854775807 and D-Coin value cannot exceed 2147483647!';
		exit;
	}
	else
	{
		

		$infoQuery = msquery("SELECT exp from %s.dbo.userExt where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
		$info = mssql_fetch_array($infoQuery);
		if ($info[0] >= $_POST['exp'])
		{
			$ip=$_SERVER['REMOTE_ADDR'];
			$exp = $info[0] - $_POST['exp'];
			msquery("UPDATE %s.dbo.userExt SET exp = '%s' where user_no = '%s'", $ini['MSSQL']['extrasDB'], $exp, $_SESSION['user_no']);
			$exp =  $_POST['exp'];
			msquery("INSERT INTO %s.dbo.blist (aid, exp, coins) values ('%s','%s','%s')", $ini['MSSQL']['extrasDB'], $_SESSION['user_no'], $_POST['exp'], $_POST['dcoins']);
			$auctionQuery = msquery("SELECT TOP 1 auctionID FROM %s.dbo.blist where aid = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
			$getID = mssql_fetch_array($auctionQuery);
			echo 'You have successfully listed ',entScape($_POST['exp']),' experience for ',entScape($_POST['dcoins']),' D-Coins.';
		}
		else
		{
			echo 'You don\'t have that much experience in your bank to list!';
		}
		
		
	}
}
	unset($_SESSION['token']);
	$_SESSION['token'] = sha1(microtime(true).uniqid($_SERVER['REMOTE_ADDR'], true).rand());
	$bankedQuery = msquery("SELECT exp FROM %s.dbo.userExt WHERE user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
	$bankedList = mssql_fetch_array($bankedQuery);
	echo '<table><form name=processexp action=?do=',entScape($_GET['do']),'&type=list method=POST><tr><td>Banked Experence: <b>',entScape($bankedList[0]),'</b></td></tr>';
	echo '<tr><td>Experience:<br><input type=text name=exp /></td></tr><tr><td>D-Coins:<br><input type=text name=dcoins /></td></tr><tr><td><input name=type type=submit value=List /><input type=hidden name=token value="',$_SESSION['token'],'"></td></tr></form></table>';

}
elseif($_GET['type'] == 'listing')
{
	$acctQuery = msquery("SELECT amount, free_amount from cash.dbo.user_cash where user_no = '%s'", $_SESSION['user_no']);
	$isdshop = mssql_num_rows($acctQuery);
	if ($isdshop == '1')
	{
	if($_GET['action'] == 'deletelisting' && !empty($_GET['aid']) && ctype_digit($_GET['aid']))
{
	$auQuery = msquery("Select * FROM %s.dbo.blist where auctionID = '%s'", $ini['MSSQL']['extrasDB'], $_GET['aid']);
	$isAU = mssql_num_rows($auQuery);
	if ($isAU == '1')
	{
		$auInfo = mssql_fetch_array($auQuery);
		if ($auInfo['aid'] == $_SESSION['user_no'])
		{
			$ip=$_SERVER['REMOTE_ADDR'];
			msquery("DELETE FROM %s.dbo.blist where auctionID = '%s'", $ini['MSSQL']['extrasDB'], $auInfo['auctionID']);
			$bankQuery = msquery("SELECT exp from %s.dbo.userExt where user_no = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
			$bankInfo = mssql_fetch_array($bankQuery);
			$exp = $bankInfo[0] + $auInfo['exp'];
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
	echo 'Auction no longer exists to delete.';
	}
}
if($_GET['action'] == 'buylisting' && !empty($_GET['aid']) && ctype_digit($_GET['aid']))
{
	$auQuery = msquery("Select * FROM %s.dbo.blist where auctionID = '%s'", $ini['MSSQL']['extrasDB'], $_GET['aid']);
	$isAU = mssql_num_rows($auQuery);
	if ($isAU == '1')
	{
		$auInfo = mssql_fetch_array($auQuery);
		if ($auInfo['aid'] != $_SESSION['user_no'])
		{
			$acctQuery = msquery("SELECT amount, free_amount from cash.dbo.user_cash where user_no = '%s'", $_SESSION['user_no']);
			$acctCoins = mssql_fetch_array($acctQuery);
			if (($acctCoins['amount'] + $acctCoins['free_amount']) >= $auInfo['coins'])
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
	else
	{
	echo 'Auction no longer exists to buy.';
	}
}
		$listQuery = msquery("SELECT * from %s.dbo.blist", $ini['MSSQL']['extrasDB']);
		$acctCoins = mssql_fetch_array($acctQuery);
		$totalCoins = $acctCoins[0] + $acctCoins[1];
		echo '<table><tr><td colspan="3">Your D-Coins: <b>',entscape($totalCoins),'</b></td></tr><tr><td><b><u>Experience</u></b></td><td><b><u>Price</u></b></td></tr>';
		while ($listings = mssql_fetch_array($listQuery))
		{
			echo '<tr><td>',entScape($listings[2]),'</td><td>',entScape($listings[3]),'</td>';
			if ($listings[1] == $_SESSION['user_no'])
			{
				echo '<td><a style=color:red; href=?do=',entScape($_GET['do']),'&type=listing&action=deletelisting&aid=',entScape($listings[0]),'>Delete</a></td></tr>';
			}
			elseif($listings[1] <> $_SESSION['user_no'] && $listings[3] <= $totalCoins)
			{
				echo '<td><a href=?do=',entScape($_GET['do']),'&type=listing&action=buylisting&aid=',entScape($listings[0]),'>Buy</a></td></tr>';
			}
			else
			{
			echo '</tr>';
			}
		}
		echo '</table>';
	}
	elseif ($isdshop == '0')
	{
		echo 'You have not visited the d-shop yet in-game. You cannot participate in buying experience!';
	}
	else
	{
		echo 'D-Shop error has occured. Please contact your administrator and tell them, "the shit has hit the fan".';
	}
}
else
{
	unset($_SESSION['token']);
	echo 'Invalid Action!';
}
echo '</table>';
?>
