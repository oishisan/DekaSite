﻿<?php//global variables$ini = parse_ini_file('config.ini', true); //Parse config.ini//Connect to the database$ms_con = mssql_pconnect($ini['MSSQL']['host'],$ini['MSSQL']['user'],$ini['MSSQL']['password']);//Check if database connection can be establishedif ($ms_con == false){	exit(0);}/*Function to escape MSSQL strings and deny queries with greater than 100 characters.Parameters:	$str	The string that is to be escaped	$a	Optional paramteter to disable character length checking. Set to		any other value besides 0 to disable it.*/function mssql_escape($str, $a=0){	if (strlen($str) > 100 && $a=0)	{		Header('HTTP/1.1 403');		exit(0);	}	else	{		return preg_replace('/\'/','\'\'', $str);	}}/*Function to split whitelist string valuesParameters:	$str	Whitelist string	$type	The paramter you want from the whitelist value.Return values:	type = 0 returns php page (default)	type = 1 returns the title	type = 2 returns action*/function splitWV($str, $type = 0){	$str = explode(',',$str);	switch($type){	case 0:		return $str[0];	case 1:		return $str[1];	case 2:		$str = explode('/',$str[0]);		if (count($str) == 1)		{			$str = explode('.', $str[0]);		}		else		{			$str = explode('.', $str[count($str) - 1]);		}		return $str[0];	}}/*Function to build an array for page authenticationParameters:	$settings	The ini array	$allow		The accepted pages array*/function authPages(&$settings, &$allow){	if($_SESSION['auth'] == 0)	{		if(array_key_exists('guest', $settings['whitelist.top']))		{			foreach($settings['whitelist.top']['guest'] as $val)			{				$allow[splitWV($val,2)] = splitWV($val);			}		}		if(array_key_exists('guest', $settings['whitelist.side']))		{			foreach($settings['whitelist.side']['guest'] as $val)			{				$allow[splitWV($val,2)] = splitWV($val);			}		}	}	else	{		if(array_key_exists('member', $settings['whitelist.top']))		{			foreach($settings['whitelist.top']['member'] as $val)			{				$allow[splitWV($val,2)] = splitWV($val);			}		}		if(array_key_exists('member', $settings['whitelist.side']))		{			foreach($settings['whitelist.side']['member'] as $val)			{				$allow[splitWV($val,2)] = splitWV($val);			}		}		if($settings['MSSQL']['extras'] == true)		{			//Include GM Pages			if($_SESSION['auth'] > 1)			{			}			//Include Admin pages			if($_SESSION['auth'] > 2)			{			}		}	}}?>