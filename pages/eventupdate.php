<?php
if ($_GET['action'] == "edit")
{
	$sdate = strtotime($_POST['sdate']);
	$edate = strtotime($_POST['edate']);
	if($sdate <= $edate)
	{
		$sdate = date("n/j/o g:i A", $sdate);
		$edate = date("n/j/o g:i A", $edate);
		$date = date("n/j/o g:i A",strtotime($_POST['date']));
		msquery("UPDATE %s.dbo.event SET eDesc = '%s',eName = '%s', eStart = '%s', eEnd = '%s' WHERE eid = '%s'", $ini['MSSQL']['extrasDB'], $_POST['content'], $_POST['title'], $sdate, $edate,$_POST['eid']);
	}
	else
	{
		echo 'End date cannot be earlier than starting date!<br>';
	}
}
if ($_GET['action'] == "delete")
{	
	msquery("DELETE FROM %s.dbo.event WHERE eid = '%s'",$ini['MSSQL']['extrasDB'], $_GET['eid']);
}
if ($_GET['action'] == "create")
{
	$sdate = strtotime($_POST['sdate']);
	$edate = strtotime($_POST['edate']);
	if($sdate <= $edate)
	{
		$sdate = date("n/j/o g:i A", $sdate);
		$edate = date("n/j/o g:i A", $edate);
		msquery("INSERT INTO %s.dbo.event(eName,eHost,eStart,eEnd,eDesc) VALUES ('%s','%s','%s','%s','%s')", $ini['MSSQL']['extrasDB'], $_POST['title'], $_SESSION['webName'], $sdate, $edate, $_POST['content']);
	}
	else
	{
		echo 'End date cannot be earlier than starting date!<br>';
	}
}
if ($_GET['part'] == "new")
{
	echo '<table><form method="POST" action="?do=',entScape($_GET['do']),'&action=create">
	<tr><td>Name:<br><input type="text" name="title" /></td></tr>
	<tr><td>Start Date (MM/DD/YYYY HH:MM AM/PM):<br><input type="text" name="sdate" /></td></tr>
	<tr><td>End Date (MM/DD/YYYY HH:MM AM/PM):<br><input type="text" name="edate" /></td></tr>
	<tr><td>Description:<br><textarea name="content"></textarea></td></tr>
	<tr><td><input type="submit" value="create" /></td></tr></form></table>';
} 
elseif ($_GET['part'] == "edit")
{
	$eQuery = msquery("SELECT * FROM %s.dbo.event WHERE eid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['eid'])->fetchAll();
	echo '<table>';
	if (count($eQuery) == 1)
	{
		echo '<form method="POST" action="?do=',entScape($_GET['do']),'&action=edit">
		<tr><td><input type="hidden" name="eid" value="',entScape($eQuery[0]['eID']),'" />Name:<br>
		<input type="text" name="title" value="',entScape($eQuery[0]['eName']),'" /></td></tr>
		<tr><td>Start Date (MM/DD/YYYY HH:MM AM/PM)<br><input type="text" name="sdate" value="',entScape($eQuery[0]['eStart']),'" /></td></tr>
		<tr><td>End Date (MM/DD/YYYY HH:MM AM/PM)<br><input type="text" name="edate" value="',entScape($eQuery[0]['eEnd']),'" /></td></tr>
		<tr><td>Description:<br><textarea name="content">',entScape($eQuery[0]['eDesc']),'</textarea></td></tr>
		<tr><td><input type="submit" value="Edit" /></td></tr></form>';
	}
	else
	{
		echo '<tr><td>Cannot find event.</td></tr>';
	}
	echo '</table>';
}
else
{
	echo '<a href="?do=',entScape($_GET['do']),'&part=new">Add Event</a><table><tr><th>Event</th><th>Host</th><th>Start Date</th><th>End Date</th></tr>';
	$eQuery = msquery("SELECT eID, eName, eHost, eStart, eEnd FROM %s.dbo.event ORDER BY eStart desc, eEnd desc", $ini['MSSQL']['extrasDB'])->fetchAll();
	foreach($eQuery as $eFetch)
	{
		echo '<tr><td><a href="?do=',entScape($_GET['do']),'&part=edit&eid=',entScape($eFetch['eID']),'">[',entScape($eFetch['eName']),']</a></td><td>',entScape($eFetch['eHost']),'</td><td>',entScape($eFetch['eStart']),'</td><td>',entScape($eFetch['eEnd']),'</td><td><a href="?do=',entScape($_GET['do']),'&action=delete&eid=',entScape($eFetch['eID']),'">Delete</a></td></tr>';
	}
	echo '</table>';

}
?>
