<?php
echo '<table>';
if ($_GET['action'] == "add")
{
	msquery("INSERT INTO %s.dbo.site_download (link, name, version, descr) VALUES ('%s','%s','%s','%s')", $ini['MSSQL']['extrasDB'], $_POST['link'], $_POST['name'], $_POST['version'], $_POST['descr']);
}

if ($_GET['action'] == "remove"){
	msquery("DELETE FROM %s.dbo.site_download WHERE sid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['sid']);
}

if ($_GET['action'] == "edit")
{
	msquery("UPDATE %s.dbo.site_download SET name = '%s',version = '%s',link = '%s',descr = '%s' WHERE sid = '%s'", $ini['MSSQL']['extrasDB'], $_POST['name'], $_POST['version'], $_POST['link'], $_POST['descr'], $_GET['sid']);
}

if ($_GET['part'] == "edit")
{
	$dQuery = msquery("SELECT * FROM %s.dbo.site_download WHERE sid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['sid']);
	$dFetch = $dQuery->fetch();
	echo '<table><form method="POST" action="?do=',entScape($_GET['do']),'&action=edit&sid=',entScape($_GET['sid']),'">
	<tr><td>Name:<br><input type="text" name="name" value="',entScape($dFetch['name']),'" /></td></tr>
	<tr><td>Version:<br><input type="text" name="version" value="',entScape($dFetch['version']),'" /></td></tr>
	<tr><td>Link:<br><input type="text" name="link" value="',entScape($dFetch['link']),'" /></td></tr>
	<tr><td><textarea name="descr">',entScape($dFetch['descr']),'</textarea></td></tr>
	<tr><td><input type="submit" value="Edit" /></td></tr></form>';
}
elseif ($_GET['part'] == "new")
{
	echo '<table><form method="POST" action="?do=',entScape($_GET['do']),'&action=add">
	<tr><td>Name:<br><input type="text" name="name" /></td></tr>
	<tr><td>Version:<br><input type="text" name="version" /></td></tr>
	<tr><td>Link:<br><input type="text" name="link" /></td></tr>
	<tr><td>Description:<br><textarea name="descr"></textarea></td></tr>
	<tr><td><input type="submit" value="Add" /></td></tr>
	</form>';
}
else
{
	echo '<a href="?do=',entScape($_GET['do']),'&part=new">New Download</a><table><tr><th>Name:</th><th>Version:</th></tr>';
	$dQuery = msquery("SELECT * FROM %s.dbo.site_download ORDER BY sid DESC", $ini['MSSQL']['extrasDB'])->fetchAll();
	foreach($dQuery as $dFetch)
	{
		echo '<tr><td><a href="?do=',entScape($_GET['do']),'&part=edit&sid=',entScape($dFetch['sid']),'">[',entScape($dFetch['name']),']</a></td>
		<td>',entScape($dFetch['version']),'</td>
		<td><a href="?do=',entScape($_GET['do']),'&action=remove&sid=',entScape($dFetch['sid']),'">Remove</a></td></tr>';
	}
}

echo '</table>';
?>
