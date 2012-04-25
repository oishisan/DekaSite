<?php
echo '<form action="?do=',entScape($_GET['do']),'" method="POST"><select name="type">';
foreach($ini['Other']['ticket.manage.'.$_SESSION['auth']] as $ttype)
{
	echo '<option value="',entScape($ttype),'">',entScape($ttype),'</option>';
}
echo '</select> <input type="submit" value="Search" name="search"/></form>';
if(isset($_POST['search']) && isset($_POST['type']))
{
	if(in_array($_POST['type'], $ini['Other']['ticket.manage.'.$_SESSION['auth']]))
	{
		$tQuery = msquery("select tid, status, user_id, title from %s.dbo.tickets join account.dbo.user_profile on account.dbo.user_profile.user_no = %s.dbo.tickets.owner where type = '%s' order by status asc, topen asc", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $_POST['type']);
		if (mssql_num_rows($tQuery) > 0)
		{
			echo '<table><tr><th>Ticket</th><th>Account</th><th>Status</th></tr>';
			while($tFetch = mssql_fetch_array($tQuery))
			{
				if((int)$tFetch['status'] === 0)
				{
					$tFetch['status'] = 'Closed';
				}
				elseif((int)$tFetch['status'] === -1)
				{
					$tFetch['status'] = 'Locked';
				}
				else
				{
					$tFetch['status'] = 'Open';
				}
				echo '<tr><td><a href="?do=',entScape($_GET['do']),'&action=view&id=',entScape($tFetch['tid']),'">',entScape($tFetch['title']),'</a></td><td>',entScape($tFetch['user_id']),'</td><td>',entScape($tFetch['status']),'</td></tr>';
			}
			echo '</table>';
		}
		else
		{	
			echo 'No tickets available for ',entScape($_POST['type']),'.';
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
	$tFetch = mssql_fetch_array($tQuery);
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
				msquery("update %s.dbo.tickets set status = '0' where tid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['id']);
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
					$nQuery = msquery("select rid from %s.dbo.ticket_post where tid = '%s'", $ini['MSSQL']['extrasDB'], $_GET['id']);
					msquery("INSERT into %s.dbo.ticket_post values ('%s', '%s', '%s', '%s', getdate())", $ini['MSSQL']['extrasDB'], $_GET['id'], mssql_num_rows($nQuery), $_SESSION['webName'], $_POST['reply']);
				}
			}
			$pQuery = msquery("select poster, owner, post, rdate, user_id from %s.dbo.ticket_post join %s.dbo.tickets on %s.dbo.tickets.tid = %s.dbo.ticket_post.tid join account.dbo.user_profile on account.dbo.user_profile.user_no = %s.dbo.tickets.owner where %s.dbo.ticket_post.tid = '%s' order by rid asc", $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'],$ini['MSSQL']['extrasDB'], $ini['MSSQL']['extrasDB'], $_GET['id']);	
			while($pFetch = mssql_fetch_array($pQuery))
			{
				if($pFetch['poster'] == $_SESSION['webName']) $pFetch['poster'] = 'you';
				if($pFetch['poster'] == $pFetch['owner']) $pFetch['poster'] = $pFetch['user_id'];
				echo '<div id="tpost">Written by ',entScape($pFetch['poster']),'<br>At ',entScape($pFetch['rdate']),'<br>',entScape($pFetch['post'],true),'</div>';
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
