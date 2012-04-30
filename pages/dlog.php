<?php
echo 'If no end time is given, it will use the current time.<form action="?do=',entScape($_GET['do']),'" method="POST">
Start time: <input type="text" name="stime" /><br>
End time: <input type="text" name="etime" /><br>
<input type="submit" name="search" value="Search">
</form>';
if(!empty($_POST['stime']) && isset($_POST['search']))
{
	if(empty($_POST['etime'])) 
	{
		$_POST['etime'] = date('M j Y g:iA');
	}
	else
	{
		$_POST['etime'] = date('M j Y g:iA',strtotime($_POST['etime']));
	}
	$_POST['stime'] = date('M j Y g:iA',strtotime($_POST['stime']));
	echo entScape($_POST['stime']),' - ',entScape($_POST['etime']);
	$dQuery = msquery("SELECT character_name,product,intime FROM cash.dbo.user_use_log where intime >= convert(datetime, '%s') and intime <= convert(datetime, '%s') ORDER BY intime DESC", $_POST['stime'], $_POST['etime'])->fetchAll();
	if(count($dQuery) > 0)
	{
		echo '<table><tr><th>Character Name</th><th>Item Name</th><th>Date</th></tr>';
		foreach($dQuery as $dFetch)
		{
			echo '<tr><td>',entScape($dFetch['character_name']),'</td>';
			echo '<td>',entScape($dFetch['product']),'</td>';	
			echo '<td>',entScape($dFetch['intime']),'</td></tr>';
		}
			echo "</table>";
	}
	else
	{
		echo '<br>No logs were found for the specified time period.';
	}
}
?>
