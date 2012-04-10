<?php
if ($_GET['part'] == "new")
{
	echo '<table><form method="POST" action="?do=',entScape($_GET['do']),'&type=create"><tr><td>News Management</td></tr>
	<tr><td>Title:<br><input type="text" name="title" /></td></tr>
	<tr><td>Message:<br><textarea cols="75" rows="25" name="content"></textarea></td></tr>
	<tr><td><input type="submit" value="Create" /></td></tr></form></table>';
} 
elseif ($_GET['part'] == "edit")
{
	$query = msquery("SELECT * FROM %s.dbo.site_news WHERE sid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['sid']);
	while($r = mssql_fetch_array($query))
	{
		echo '<table><tr><td>News Management</td></tr>
		<form method="POST" action="?do=',entScape($_GET['do']),'&type=edit">
		<tr><td><input type="hidden" name="sid" value="',entScape($r['sid']),'" />Title:<br><input type=text name=title value="',entScape($r['title']),'" /></td></tr>
		<tr><td>Message:<br><textarea cols="75" rows="25" name="content">', entScape($r['content']),'</textarea></td></tr>
		<tr><td><input type="submit" value="Edit" /></td></tr></form></table>';
	}
}
else
{

	if ($_GET['type'] == "edit")
	{
		msquery("UPDATE %s.dbo.site_news SET content = '%s',title = '%s' WHERE sid = '%s'", $ini['MSSQL']['extrasDB'], $_POST['content'], $_POST['title'], $_POST['sid']);
		echo 'Edit done!';
	}
	if ($_GET['type'] == "delete")
	{	
		msquery("DELETE FROM %s.dbo.site_news WHERE sid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['sid']);
		echo 'Delete done!';
	}
	if ($_GET['type'] == "create")
	{
		$time = date("m/d/Y");
		msquery("INSERT INTO %s.dbo.site_news (title,wroteby,wrotedate,content) VALUES ('%s','%s','%s','%s')", $ini['MSSQL']['extrasDB'], $_POST['title'], $_SESSION['webName'], $time, $_POST['content']);
		echo 'News updated!';
	}
	echo '<table><tr><td colspan="5">News Management</td></tr><tr><td><a href="?do=',entScape($_GET['do']),'&part=new">Add News</a></td></tr><tr><td>Title:</td><td>Written By:</td><td>Date:</td></tr>';
	$query = msquery("SELECT * FROM %s.dbo.site_news ORDER BY sid DESC", $ini['MSSQL']['extrasDB']);
	while($r = mssql_fetch_array($query))
	{
		echo '<tr><td><a href="?do=',entScape($_GET['do']),'&part=edit&sid=',entScape($r['sid']),'">',entScape($r['title']),'</a></td><td>',entScape($r['wroteby']),'</td><td>',entScape($r['wrotedate']),'</td><td><a href="?do=newsupdate&type=delete&sid=',entScape($r['sid']),'">Delete</a></td></tr>';
	}
	echo '</table>';


}
?>
