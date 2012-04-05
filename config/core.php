﻿<?php
$ini = parse_ini_file('config.ini', true); //Parse config.ini

//Connect to the database
$ms_con = mssql_pconnect($ini['MSSQL']['host'],$ini['MSSQL']['user'],$ini['MSSQL']['password']);

//Check if database connection can be established
if ($ms_con == false)
{
	echo 'Database connection could not be established';
	exit(0);
}

/*
Function to escape MSSQL strings and deny queries with greater than 100 characters.

Parameters:
	$str	The string that is to be escaped
	
Return value:
	string	The escaped string
*/
function mssql_escape($str)
{
	return preg_replace('/\'/','\'\'', $str);
}

/*
Function to query mssql safely

Parameters:
	1st		The query formatted as per sprintf.
	nth		Data to be sanitized if needed
*/
function msquery()
{
	$args = func_get_args();
	$num = func_num_args();
	if($num > 1)
	{
		$array = array();
		for($i=1;$i < $num; $i++)
		{
			$array[] = mssql_escape($args[$i]);
		}
		return mssql_query(vsprintf($args[0], $array),$GLOBALS['ms_con']);
	}
	else
	{
		return mssql_query($args[0],$GLOBALS['ms_con']);
	}
}

/*
Function to convert html special characters.

Parameters:
	$str	String to be converted

Return value:
	string	The converted string
*/
function entScape($str)
{
	return htmlentities($str, ENT_QUOTES | ENT_HTML401);
}

/*
Function to split whitelist string values

Parameters:
	$str	Whitelist string
	$type	The paramter you want from the whitelist value.

Return values:
	type = 0 returns php page (default)
	type = 1 returns the title
	type = 2 returns action
*/
function splitWV($str, $type = 0)
{
	$str = explode(',',$str);
	switch($type){
	case 0:
		return $str[0];
	case 1:
		return $str[1];
	case 2:
		$str = explode('/',$str[0]);
		if (count($str) == 1)
		{
			$str = explode('.', $str[0]);
		}
		else
		{
			$str = explode('.', $str[count($str) - 1]);
		}
		return $str[0];
	}
}

/*
Function to build the array for whitelisted pages.

Parameters:
	$settings	The ini array
	$allow		The accepted pages array
*/
function authPages(&$settings, &$allow)
{
	$allow = NULL;
	if($_SESSION['auth'] == $settings['Other']['lvl.guest'])
	{
		if(array_key_exists('guest', $settings['whitelist.top']))
		{
			foreach($settings['whitelist.top']['guest'] as $val)
			{
				$allow['top'][splitWV($val,2)][0] = splitWV($val);
				$allow['top'][splitWV($val,2)][1] = splitWV($val,1);
			}
		}
		if(array_key_exists('guest', $settings['whitelist.side']))
		{
			foreach($settings['whitelist.side']['guest'] as $val)
			{
				$allow['side'][splitWV($val,2)][0] = splitWV($val);
				$allow['side'][splitWV($val,2)][1] = splitWV($val,1);
			}
		}
	}
	else
	{
		if(array_key_exists('member', $settings['whitelist.top']))
		{
			foreach($settings['whitelist.top']['member'] as $val)
			{
				$allow['top'][splitWV($val,2)][0] = splitWV($val);
				$allow['top'][splitWV($val,2)][1] = splitWV($val,1);
			}
		}
		if(array_key_exists('member', $settings['whitelist.side']))
		{
			foreach($settings['whitelist.side']['member'] as $val)
			{
				$allow['side'][splitWV($val,2)][0] = splitWV($val);
				$allow['side'][splitWV($val,2)][1] = splitWV($val,1);
			}
		}
		if($settings['MSSQL']['extras'] == true)
		{
			//Add GM pages
			if($_SESSION['auth'] > $settings['Other']['lvl.member'])
			{
				if(array_key_exists('GM', $settings['whitelist.top']))
				{
					foreach($settings['whitelist.top']['GM'] as $val)
					{
						$allow['top'][splitWV($val,2)][0] = splitWV($val);
						$allow['top'][splitWV($val,2)][1] = splitWV($val,1);
					}
				}
				if(array_key_exists('GM', $settings['whitelist.side']))
				{
					foreach($settings['whitelist.side']['GM'] as $val)
					{
						$allow['side'][splitWV($val,2)][0] = splitWV($val);
						$allow['side'][splitWV($val,2)][1] = splitWV($val,1);
					}
				}
			}
			//Add Admin pages
			if($_SESSION['auth'] > $settings['Other']['lvl.GM'])
			{
				if(array_key_exists('Admin', $settings['whitelist.top']))
				{
					foreach($settings['whitelist.top']['Admin'] as $val)
					{
						$allow['top'][splitWV($val,2)][0] = splitWV($val);
						$allow['top'][splitWV($val,2)][1] = splitWV($val,1);
					}
				}
				if(array_key_exists('Admin', $settings['whitelist.side']))
				{
					foreach($settings['whitelist.side']['Admin'] as $val)
					{
						$allow['side'][splitWV($val,2)][0] = splitWV($val);
						$allow['side'][splitWV($val,2)][1] = splitWV($val,1);
					}
				}
			}
		}
	}
}

/*
Function that forces a content page to require extras to be enabled

This function is only required by guest and member pages that require
extras database. By design, authority levels above member need the extras
database.
*/
function requireExtras()
{
	if($GLOBALS['ini']['MSSQL']['extras'] == false)
	{
		echo 'This page requires DekaSite\'s extra content to be enabled.';
		include 'footer.php';
		exit(0);
	}
}

/*
Function to record actions into session log

Parameters:
	$action		The action to use in the database
*/
function sessionLog($action)
{
	if($GLOBALS['ini']['MSSQL']['extras'] == true)
	{
		msquery("INSERT INTO %s.dbo.sessionlog values (getdate(),'%s', '%s', '%s')",$GLOBALS['ini']['MSSQL']['extrasDB'],$_SESSION['accname'],$_SERVER['REMOTE_ADDR'], $action);
	}
}

//Login backend
if(!isset($_SESSION['auth']))
{
	$_SESSION['auth'] = $ini['Other']['lvl.guest'];
}
elseif ((isset($_POST['login']) && $_SESSION['auth'] == 0) || (isset($_SESSION['accname']) && $_SESSION['auth'] > $ini['Other']['lvl.guest']))
{
	if (isset($_POST['login']) && $_SESSION['auth'] == 0)
	{
		$accountInfo = msquery("SELECT user_no, COUNT(user_no) as num FROM account.dbo.user_profile WHERE user_id = '%s' AND user_pwd = '%s' AND login_tag = 'Y' GROUP BY user_no",$_POST['accname'],md5($_POST['accpass']));
	}
	else
	{
		$accountInfo = msquery("SELECT user_no, COUNT(user_no) as num FROM account.dbo.user_profile WHERE user_id = '%s' AND login_tag = 'Y' GROUP BY user_no",$_SESSION['accname']);
	}
	if ($accountInfo)
	{
		$getAccount = mssql_fetch_array($accountInfo);
		if ($getAccount['num'] == 1)
		{
			if (isset($_POST['login']) && $_SESSION['auth'] == 0) $_SESSION['accname'] = $_POST['accname'];
			$_SESSION['user_no'] = $getAccount[0];;
			if($ini['MSSQL']['extras'] == true)
			{
				$authQ = msquery("Select auth,news, count(auth) as num FROM %s.dbo.auth where account ='%s' group by auth,news",$ini['MSSQL']['extrasDB'],$_SESSION['accname']);
				$authA = mssql_fetch_array($authQ);
				if($authA['num'] == 1)
				{
					if($_SESSION['auth'] != $authA['auth'])
					{
						$_SESSION['auth'] = $authA['auth'];
						$_SESSION['news'] = $authA['news'];
					}
				}
				else
				{
					$_SESSION['auth'] = $ini['Other']['lvl.member'];
				}
			}
			else
			{
				$_SESSION['auth'] = $ini['Other']['lvl.member'];
			}
		}
		elseif ($getAcount['num'] == 0)
		{
			$errormsg = 'Invalid username or password.';
		}
		else
		{
			$errormsg = 'Account confliction occured! Please contact your administrator.';
		}
	}
	else
	{
		echo 'Unable to connect to the database.';
		exit(0);
	}
}
authPages($ini, $_SESSION['aSites']);
?>

