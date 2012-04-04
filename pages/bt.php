<?php
if(isset($_POST['build']))
{
	echo 'Non-existing tabels built.<br>';
	// create sessionlog
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'sessionlog' and xtype = 'U') CREATE TABLE %s.dbo.sessionlog (wTime datetime, Account varchar(50), IP varchar(50), wAction varchar(50))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	if(isset($_POST['authN']) && isset($_POST['authID']))
	{
		// create auth table
		msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'auth' and xtype = 'U') CREATE TABLE %s.dbo.auth (account varchar(16), auth int, news varchar (16))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
		// insert inital ID
		msquery("INSERT INTO %s.dbo.auth values ('%s', '%s', '%s')", $ini['MSSQL']['extrasDB'], $_POST['authID'], $ini['Other']['lvl.Admin'], $_POST['authN']);
		echo 'Authorization initialized.<br>It is suggested that you move the Build Tables page to Administrators only level.<br>';
	}
	echo 'You may enable extras features.';
}
else
{
	if(mssql_select_db($ini['MSSQL']['extrasDB']))
	{
		echo 'This page builds non-existing tables in the database.<br><form action="?do=',entscape($_GET['do']),'" method="POST">';
		$aQuery = msquery("SELECT name FROM %s.dbo.sysobjects where name = 'auth' and xtype = 'U'", $ini['MSSQL']['extrasDB']);
		if(mssql_num_rows($aQuery) == 0)
		{
			echo 'Your authorization tables haven\'t been built. Please enter an account ID to be designated as the inital administrator.
			<br>User ID: <input type="text" name="authID" /><br>Site display: <input type="text" name="authN" /><br>';
		}
		echo'<input type="submit" name="build" value="Build Tables" /></form>';
	}
	else
	{
		echo 'The extras database you have specified does not exist. Please create this database with the full permission for the specified MSSQL user in the configuration.';
	}
}
?>
