<?php
/*
CSS page specific IDs
---------------------
#tpost		Each reply in when viewing a ticket
#rName		The name of replier when viewing the ticket
#rDate		The text of the date when viewing the ticket
*/
echo '<form action="?do=',entScape($_GET['do']),'" method="POST">Category: <select name="type">';
foreach($ini['Other']['ticket.manage.'.$_SESSION['auth']] as $ttype)
{
	echo '<option value="',entScape($ttype),'" ';
	if(isset($_POST['type']) && $ttype == $_POST['type']) echo 'selected="selected"';
	echo'>',entScape($ttype),'</option>';
}
echo '</select><br>
Status: <select name="status"><option value="1" ';
if(isset($_POST['status']) && (int)$_POST['status'] === 1) echo 'selected="selected"';
echo'>Open</option><option value="0" ';
if(isset($_POST['status']) && (int)$_POST['status'] === 0) echo 'selected="selected"';
echo'>Closed</option><option value="-1" ';
if(isset($_POST['status']) && (int)$_POST['status'] === -1) echo 'selected="selected"';
echo'>Locked</option></select><br>
Order: <select name="order"><option value="0 "';
if(isset($_POST['order']) && $_POST['order'] == 0) echo 'selected="selected"';
echo '>Newest reply</option><option value="1" '; 
if(isset($_POST['order']) && $_POST['order'] == 1) echo 'selected="selected"';
echo'>Oldest Tickets</option><option value="2" ';
if(isset($_POST['order']) && $_POST['order'] == 2) echo 'selected="selected"';
echo '>Newest Tickets</option></select><br>
<input type="submit" value="Search" name="search"/></form>';
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id']))
{
	$dQuery = msquery("select type, count(type) as num from %s.dbo.tickets where tid = '%s' group by type", $ini['MSSQL']['extrasDB'], $_GET['id']);
	$dFetch = $dQuery->fetch();
	if($dFetch['num'] == 1 && isset($ini['Other']['ticket.manage.'.$_SESSION['auth']]) && isset($ini['Other']['ticket.delete']) && in_array($dFetch['type'], $ini['Other']['ticket.manage.'.$_SESSION['auth']]) && in_array($_SESSION['auth'], $ini['Other']['ticket.delete']))
	{
		msquery("DELETE FROM %s.dbo.tickets where tid = '%s'; DELETE FROM %s.dbo.ticket_post where tid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['id'], $ini['MSSQL']['extrasDB'], $_GET['id']);
		echo 'Ticket deleted.';
	}
	else
	{
		echo 'Invalid ticket.';
	}
}
if(isset($_POST['search']) && isset($_POST['type']) && isset($_POST['status']) && isset($_POST['status']) && isset($_POST['order']))
{
	if(in_array($_POST['type'], $ini['Other']['ticket.manage.'.$_SESSION['auth']]))
	{
		$_POST['status'] = (int)$_POST['status'];
		switch ($_POST['status'])
		{
			case -1:
				break;
			case 0:
				break;
			default:
				$_POST['status'] = 1;

		}
		switch ($_POST['order'])
		{
			case 1:
				$_POST['order'] = 'topen asc';
				break;
			case 2:
				$_POST['order'] = 'topen desc';
				break;
			default:
				$_POST['order'] = 'rdate desc';

		}
		$tQuery = msquery("select t.tid, user_id, title, lby, rdate, (case when poster = t.owner then user_id else poster end) as poster from %s.dbo.tickets t join (select tid, poster,rdate from %s.dbo.ticket_post tp) tp on tp.tid = t.tid join account.dbo.user_profile a on a.user_no = t.owner where rdate = (select max(rdate) from %s.dbo.ticket_post where tid = t.tid) and type = '%s' and status = '%s' order by %s", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $_POST['type'], $_POST['status'], $_POST['order']);
		$tQuery = $tQuery->fetchAll();
		if (count($tQuery) > 0)
		{
			echo '<table><tr><th>By</th><th>Ticket</th><th>Last reply</th>';
			if($_POST['status'] === -1) echo '<th>Locked by</th>';
			echo '</tr>';
			foreach($tQuery as $tFetch)
			{
				echo '<tr><td>',entScape($tFetch['user_id']),'</td><td><a href="?do=',entScape($_GET['do']),'&action=view&id=',entScape($tFetch['tid']),'">',entScape($tFetch['title']),'</a></td><td>',entScape($tFetch['poster']),'<br>',$tFetch['rdate'],'</td>';
				if($_POST['status'] === -1) echo '<td>',entScape($tFetch['lby']),'</td>';
				if(isset($ini['Other']['ticket.delete']) && in_array($_SESSION['auth'], $ini['Other']['ticket.delete'])) echo '<td><a href="?do=',entScape($_GET['do']),'&action=delete&id=',entScape($tFetch['tid']),'">Delete</a></td>';
				echo '</tr>';
			}
			echo '</table>';
		}
		else
		{	
			echo 'No tickets available.';
		}
	}
	else
	{
		echo 'Category does not exist.';
	}
}
elseif($_GET['action'] == 'view' && isset($_GET['id']))
{
	$tQuery = msquery("select type, status, count(type) as num from %s.dbo.tickets where tid = '%s' group by type, status", $ini['MSSQL']['extrasDB'], $_GET['id']);
	$tFetch = $tQuery->fetch();
	if($tFetch['num'] == 1)
	{
		if(in_array($tFetch['type'], $ini['Other']['ticket.manage.'.$_SESSION['auth']]))
		{
			if(isset($_POST['close']) && $tFetch['status'] == 1)
			{
				msquery("update %s.dbo.tickets set status = '0' where tid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['id']);
				$tFetch['status'] = 0;
			}
			if(isset($_POST['open']) && $tFetch['status'] == 0)
			{
				msquery("update %s.dbo.tickets set status = '1' where tid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['id']);
				$tFetch['status'] = 1;
			}
			if(isset($_POST['lock']) && $tFetch['status'] == 0)
			{
				msquery("update %s.dbo.tickets set status = '-1', lby = '%s' where tid = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['webName'], $_GET['id']);
				$tFetch['status'] = -1;
			}
			if(isset($_POST['unlock']) && $tFetch['status'] == -1)
			{
				msquery("update %s.dbo.tickets set status = '0', lby = NULL where tid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['id']);
				$tFetch['status'] = 0;
			}
			if(isset($_POST['rsub']) && isset($_POST['reply']) && !empty($_POST['reply']))
			{
				if ($tFetch['status'] == 0)
				{
					echo 'You cannot reply because the ticket is closed.';
				}
				elseif ($tFetch['status'] == -1)
				{
					echo 'You cannot reply because the ticket is locked.';
				}
				else
				{
					msquery("INSERT into %s.dbo.ticket_post values ('%s', '%s', '%s', '%s')", $ini['MSSQL']['extrasDB'], $_GET['id'], $_SESSION['webName'], $_POST['reply'], date('n/j/Y g:i:s A'));
				}
			}
			$pQuery = msquery("select poster, owner, post, rdate, user_id from %s.dbo.ticket_post tp join %s.dbo.tickets t on t.tid = tp.tid join account.dbo.user_profile a on a.user_no = t.owner where tp.tid = '%s' order by rdate asc", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $_GET['id']);	
			$pQuery = $pQuery->fetchAll();
			foreach($pQuery as $pFetch)
			{
				if($pFetch['poster'] == $_SESSION['webName']) $pFetch['poster'] = 'You';
				if($pFetch['poster'] == $pFetch['owner']) $pFetch['poster'] = $pFetch['user_id'];
				echo '<div id="tpost"><span id="rName">',entScape($pFetch['poster']),'</span><br><span id="rDate">',entScape($pFetch['rdate']),'</span><br>',entScape($pFetch['post'],true),'</div>';
			}
			echo '<form action="?do=',entScape($_GET['do']),'&action=view&id=',entScape($_GET['id']),'" method="POST">';
			if($tFetch['status'] == 1)
			{
				 echo'<textarea cols="75" rows="10" name="reply"></textarea><br>
				<input type="submit" value="Reply" name="rsub"><input type="submit" value="Close" name="close" />';
			}
			if($tFetch['status'] == 0)
			{
				echo '<input type="submit" name="open" value="Open" /> <input type="submit" name="lock" value="Lock" />';
			}
			if($tFetch['status'] == -1)
			{
				echo '<input type="submit" name="unlock" value="Unlock" />';
			}
			echo '</form>';

		}
		else
		{
			echo 'You have no authority to manage this ticket.';
		}
	}
	else
	{
		echo 'Invalid ticket.';
	}
}
?>
