<?php
if(isset($_POST['build']))
{
	echo 'Non-existing tabels built.<br>';
	// create sessionlog table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'sessionlog' and xtype = 'U') CREATE TABLE %s.dbo.sessionlog (wTime datetime, Account varchar(50), IP varchar(50), wAction varchar(50))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create news table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'site_news' and xtype = 'U') CREATE TABLE %s.dbo.site_news (sid int PRIMARY KEY IDENTITY, title varchar (80) null, wroteby varchar (50), wrotedate varchar(50), content varchar (50) null)", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create downloads table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'site_download' and xtype = 'U') CREATE TABLE %s.dbo.site_download (sid int PRIMARY KEY IDENTITY, link varchar (500) null, name varchar(60) null, version varchar(50) null, descr varchar (1000))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create ban table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'banned' and xtype = 'U') CREATE TABLE %s.dbo.banned (wDate datetime null, accountname nvarchar (60) default('<none>'), reason nvarchar (765) default('no reason'), wBy varchar (50) default('<no one>'), type char (10))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create vote table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'vote' and xtype = 'U') CREATE TABLE %s.dbo.vote (link varchar (50), account varchar (50), ip varchar (50), wDate datetime)", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);	
	// create events table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'event' and xtype = 'U') CREATE TABLE %s.dbo.event (eID int PRIMARY KEY IDENTITY, eName varchar (50) null, eHost varchar (50), eStart datetime, eEnd datetime, eDesc varchar (50) null)", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);	
	// create experience banking table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'blist' and xtype = 'U') CREATE TABLE %s.dbo.blist (auctionID bigint PRIMARY KEY IDENTITY, aid varchar (50) collate database_default, exp bigint, coins int default(0))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create userExt table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'userExt' and xtype = 'U') CREATE TABLE %s.dbo.userExt (user_no varchar (20) collate database_default, user_id varchar (20) collate database_default, exp bigint default (0), dil bigint default (0))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// import existing ids to userExt
	$iQuery = msquery("select user_no, user_id from account.dbo.user_profile where not exists(select user_id from %s.dbo.userExt)", $ini['MSSQL']['extrasDB']);
	while($iFetch = mssql_fetch_array($iQuery))
	{
		msquery("INSERT INTO %s.dbo.userExt (user_no, user_id) values ('%s', '%s')", $ini['MSSQL']['extrasDB'], $iFetch['user_no'], $iFetch['user_id']);
	}
	if(isset($_POST['authN']) && isset($_POST['authID']))
	{
		// create auth table
		msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'auth' and xtype = 'U') CREATE TABLE %s.dbo.auth (account varchar(16), auth int, webName varchar (16))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
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
