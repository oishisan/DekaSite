<?php
/*
CSS page specific IDs
---------------------
#tpost		Each reply in when viewing a ticket
#rName		The name of replier when viewing the ticket
#rDate		The text of the date when viewing the ticket
*/
requireExtras();
$auth = $_SESSION['auth'];
if($_SESSION['lTag'] == 'N') $auth = 'ban';

$cCount = count($ini['Other']['ticket.type.'.$auth]);
echo '<a href="?do=',entScape($_GET['do']),'">My tickets</a>';
if ($cCount > 0) echo'<a href="?do=',entScape($_GET['do']),'&action=new">New ticket</a>';
echo '<br>';
if($_GET['action'] == 'new' && $cCount > 0)
{
	echo '<form action="?do=',entScape($_GET['do']),'&action=npost" method="POST">Category: <select name="cat">';
	for($x = 0; $x < $cCount; $x++)
	{
		echo '<option value="',entScape($x),'">',entScape($ini['Other']['ticket.type.'.$auth][$x]),'</option>';
	}
	echo '</select><br>Title: <input type="text" maxlength="20" name="title" /><br>Details:<br><textarea cols="75" rows="10" name="details"></textarea><br><input type="submit" name="subt" value="Submit Ticket"></form>';
}
elseif($_GET['action'] == 'view' && isset($_GET['id']))
{
$tQuery = msquery("select status, rdate from %s.dbo.ticket_post join %s.dbo.tickets on %s.dbo.tickets.tid = %s.dbo.ticket_post.tid where owner = '%s' and %s.dbo.ticket_post.tid = '%s' order by rdate desc", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $_SESSION['user_no'],$ini['MSSQL']['extrasDB'], $_GET['id']);
$stFetch = $tQuery->fetchAll();
if(count($stFetch) > 0)
{
	if(isset($_POST['close']) && $stFetch[0]['status'] == 1)
	{
		msquery("update %s.dbo.tickets set status = '0' where tid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['id']);
		$stFetch[0]['status'] = 0;
	}
	if(isset($_POST['open']) && $stFetch[0]['status'] == 0)
	{
		msquery("update %s.dbo.tickets set status = '1' where tid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['id']);
		$stFetch[0]['status'] = 1;
	}
	if(isset($_POST['rsub']) && isset($_POST['reply']) && !empty($_POST['reply']))
	{
		if ($stFetch[0]['status'] == 0)
		{
			echo 'You cannot reply because the ticket is closed.';
		}
		elseif ($stFetch[0]['status'] == -1)
		{
			echo 'You cannot reply because the ticket is locked.';
		}
		else
		{
			$tError = 0;
			if(!isset($ini['Other']['ticket.replyWait.'.$auth])) $ini['Other']['ticket.replyWait.'.$auth] = 30;
			$timeleft = strtotime($stFetch[0]['rdate'].' +'.$ini['Other']['ticket.replyWait.'.$auth].' seconds');
			if ($timeleft > strtotime(date('Y-m-d G:i')))	
			{
				$tError = 1;	
			}
			if($tError == 0)
			{
				msquery("INSERT into %s.dbo.ticket_post values ('%s', '%s', '%s', '%s')", $ini['MSSQL']['extrasDB'], $_GET['id'], $_SESSION['user_no'], $_POST['reply'], date('n/j/Y g:i:s A'));
			}
			else
			{
				echo 'You can only reply once every ',entScape($ini['Other']['ticket.replyWait.'.$auth]),' second(s).';
			}
		}
	}
	$tQuery = msquery("select poster, owner, post, rdate from %s.dbo.ticket_post join %s.dbo.tickets on %s.dbo.tickets.tid = %s.dbo.ticket_post.tid where owner = '%s' and %s.dbo.ticket_post.tid = '%s' order by rdate asc", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $_SESSION['user_no'],$ini['MSSQL']['extrasDB'], $_GET['id']);	
	foreach($tQuery as $tFetch)
	{
		if($tFetch['poster'] == $_SESSION['user_no']) $tFetch['poster'] = 'You';
		echo '<div id="tpost"><span id="rName">',entScape($tFetch['poster']),'</span><br><span id="rDate">',entScape($tFetch['rdate']),'</span><br>',entScape($tFetch['post'],true),'</div>';
	}
	if($stFetch[0]['status'] == 1)
	{
		echo '<form action="?do=',entScape($_GET['do']),'&action=view&id=',entScape($_GET['id']),'"method="POST"><textarea cols="75" rows="10" name="reply"></textarea><br>
		<input type="submit" value="Reply" name="rsub"><input type="submit" value="Close" name="close" /></form>';
	}
	if($stFetch[0]['status'] == 0)
	{
		echo '<form action="?do=',entScape($_GET['do']),'&action=view&id=',entScape($_GET['id']),'"method="POST"><input type="submit" name="open" value="open" /></form>';
	}
}
else
{
	echo 'Invalid ticket.';
}

}
else
{
	if($_GET['action'] == 'npost' && isset($_POST['cat']) && isset($_POST['subt']))
	{
		if($cCount > 0 && (int)$_POST['cat'] < $cCount)
		{
			if(isset($_POST['details']) && !empty($_POST['details']) && isset($_POST['title']) && !empty($_POST['title']))
			{
				$timeQuery = msquery("select top 1 topen from %s.dbo.tickets where owner = '%s' order by topen desc", $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);	
				$timeFetch = $timeQuery->fetchAll();
				$tError = 0;
				if(count($timeFetch) > 0)
				{
					if(!isset($ini['Other']['ticket.newWait.'.$auth])) $ini['Other']['ticket.newWait.'.$auth] = 30;
					$timeleft = strtotime($timeFetch[0]['topen'].' +'.$ini['Other']['ticket.newWait.'.$auth].' seconds');
					if ($timeleft > strtotime(date('Y-m-d G:i')))	
					{
					 $tError = 1;	
					}
				}
				if($tError == 0)
				{
					$tid = uniqid(substr(session_id(),0,7));
					msquery("INSERT INTO %s.dbo.tickets (tid, type, owner, title, status, topen) values ('%s','%s', '%s','%s', '1', '%s')", $ini['MSSQL']['extrasDB'], $tid,$ini['Other']['ticket.type.'.$auth][$_POST['cat']], $_SESSION['user_no'], $_POST['title'], date('n/j/Y g:i:s A'));
					msquery("INSERT INTO %s.dbo.ticket_post (tid, poster, post, rdate) values ('%s', '%s', '%s', '%s')", $ini['MSSQL']['extrasDB'], $tid, $_SESSION['user_no'], $_POST['details'], date('n/j/Y g:i:s A'));
					echo 'Ticket submitted.<br>';
				}
				else
				{
					echo 'You can only submit a new ticket once every ',entScape($ini['Other']['ticket.newWait.'.$auth]),' second(s).';
				}
			}
			else
			{
				echo 'You cannot leave details or title empty!';
			}
		}
		else
		{
			echo 'Invalid category.<br>';
		}
	}
	$tQuery = msquery("select t.tid, type, user_id, status, title, rdate, (case when poster = t.owner then 'You' else poster end) as poster from %s.dbo.tickets t join (select tid, poster,rdate from %s.dbo.ticket_post tp) tp on tp.tid = t.tid join account.dbo.user_profile a on a.user_no = t.owner where rdate = (select max(rdate) from %s.dbo.ticket_post where tid = t.tid) and t.owner = '%s' order by rdate desc", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $_SESSION['user_no']);
	$tQuery = $tQuery->fetchAll();
	if(count($tQuery) > 0)
	{
		echo '<table><tr><th>Title</th><th>Type</th><th>Status</th><th>Last reply</th></tr>';
		foreach($tQuery as $tFetch)
		{
			$status = 'Open';
			if($tFetch['status'] == 0) $status = 'Closed';
			if($tFetch['status'] == -1) $status = 'Locked';
			echo '<tr><td><a href="?do=',entscape($_GET['do']),'&action=view&id=',entScape($tFetch['tid']),'">',entScape($tFetch['title']),'</a></td><td>',entScape($tFetch['type']),'</td><td>',entScape($status),'</td><td>',entScape($tFetch['poster']),'<br>',entScape($tFetch['rdate']),'</td></tr>';
		}
		echo '</table>';
	}
	else
	{
		echo 'You do not have any submitted tickets.';
	}
}
?>
