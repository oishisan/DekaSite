<?php
if(isset($_POST['build']))
{
	echo 'Non-existing tabels built.<br>';
	// create sessionlog table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'sessionlog' and xtype = 'U') CREATE TABLE %s.dbo.sessionlog (wTime datetime, Account varchar(50), IP varchar(50), wAction varchar(50))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create news table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'site_news' and xtype = 'U') CREATE TABLE %s.dbo.site_news (sid int PRIMARY KEY IDENTITY, title varchar (80) null, wroteby varchar (50), wrotedate varchar(50), content text null)", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create downloads table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'site_download' and xtype = 'U') CREATE TABLE %s.dbo.site_download (sid int PRIMARY KEY IDENTITY, link varchar (500) null, name varchar(60) null, version varchar(50) null, descr text null )", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create ban table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'banned' and xtype = 'U') CREATE TABLE %s.dbo.banned (wDate datetime null, accountname nvarchar (60) default('<none>'), reason varchar (50) default('no reason'), wBy varchar (50) default('<no one>'), type char (10))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create vote table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'vote' and xtype = 'U') CREATE TABLE %s.dbo.vote (link varchar (100), account varchar (50), ip varchar (50), wDate datetime)", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);	
	// create events table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'event' and xtype = 'U') CREATE TABLE %s.dbo.event (eID int PRIMARY KEY IDENTITY, eName varchar (50) null, eHost varchar (50), eStart datetime, eEnd datetime, eDesc text null)", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);	
	// create experience banking table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'blist' and xtype = 'U') CREATE TABLE %s.dbo.blist (auctionID bigint PRIMARY KEY IDENTITY, aid varchar (50) collate Chinese_PRC_CI_AS, exp bigint, coins int default(0))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create userExt table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'userExt' and xtype = 'U') CREATE TABLE %s.dbo.userExt (user_no varchar (20) collate Chinese_PRC_CI_AS, user_id varchar (20) collate Chinese_PRC_CI_AS, exp bigint default (0), dil bigint default (0))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create rebirth table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'rebirth' and xtype = 'U') CREATE TABLE %s.dbo.rebirth (character_no varchar (18) collate Chinese_PRC_CI_AS, rbtime datetime)", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create tickets table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'tickets' and xtype = 'U') CREATE TABLE %s.dbo.tickets (tid varchar (50) PRIMARY KEY, type varchar (50), owner varchar (50) collate Chinese_PRC_CI_AS, title varchar (20), status int, topen datetime, lby varchar (50) null)", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// create ticket_post table
	msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'ticket_post' and xtype = 'U') CREATE TABLE %s.dbo.ticket_post (tid varchar (50), poster varchar (50) collate Chinese_PRC_CI_AS, post text null, rdate datetime)", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
	// import existing ids to userExt
	$iQuery = msquery("select user_no, user_id from account.dbo.user_profile where not exists(select user_id from %s.dbo.userExt)", $ini['MSSQL']['extrasDB']);
	$iQuery = $iQuery -> fetchAll();
	while($iQuery as $iFetch)
	{
		msquery("INSERT INTO %s.dbo.userExt (user_no, user_id) values ('%s', '%s')", $ini['MSSQL']['extrasDB'], $iFetch['user_no'], $iFetch['user_id']);
	}
	if(isset($_POST['authN']) && isset($_POST['authID']) && isset($_POST['auth']))
	{
		// create auth table
		msquery("IF NOT EXISTS (SELECT name FROM %s.dbo.sysobjects WHERE name = 'auth' and xtype = 'U') CREATE TABLE %s.dbo.auth (account varchar(20), auth varchar (20), webName varchar (16))", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB']);
		// insert inital ID
		msquery("INSERT INTO %s.dbo.auth values ('%s', '%s', '%s')", $ini['MSSQL']['extrasDB'], $_POST['authID'], $_POST['auth'], $_POST['authN']);
		echo 'Authorization initialized.<br>It is suggested that you move the Build Tables page to Administrators only level.<br>';
	}
	echo 'You may enable extras features.';
}
else
{
	echo 'This page builds non-existing tables in the database.<br><form action="?do=',entscape($_GET['do']),'" method="POST">';
	$aQuery = msquery("SELECT name FROM %s.dbo.sysobjects where name = 'auth' and xtype = 'U'", $ini['MSSQL']['extrasDB']);
	if(count($aQuery->fetchAll()) == 0)
	{
		echo 'Your authorization tables haven\'t been built. Please enter an account ID to be designated as the inital administrator.
		<br>User ID: <input type="text" name="authID" /><br>Authority: <input type="text" name="auth" value="Admin" /><br>Web name: <input type="text" name="authN" /><br>';
	}
	echo'<input type="submit" name="build" value="Build Tables" /></form>';
	}
}
?>
