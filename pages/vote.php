<?php
requireExtras();
echo '<table>';
$vCount = count($ini['Other']['vote']);
if($vCount > 0)
{
	for ($i = 0; $i<$vCount;$i++)
	{
		$split = explode(',', $ini['Other']['vote'][$i]);
		echo '<tr><td><a href="?do=',entscape($_GET['do']),'&site=',entScape($i),'">',entScape($split[3]),' (',entScape($split[0]),' Coins)</a></td></tr>';
		$count++;
	}
	if(ctype_digit($_GET['site']) && $_GET['site'] < $vCount)
	{
		$cQuery = msquery("SELECT count(amount) as num FROM cash.dbo.user_cash WHERE user_no = '%s'", $_SESSION['user_no']);
		$cFetch = mssql_fetch_array($cQuery);
		$vInfo = explode(',',$ini['Other']['vote'][$_GET['site']]);
		if($cFetch['num'] == 1)
		{
			$cQuery = msquery("SELECT top 1 *, count(account) as num FROM %s.dbo.vote WHERE (ip='%s' or account='%s') and link = '%s' group by wDate, ip, account, link order by wDate desc", $ini['MSSQL']['extrasDB'], $_SERVER['REMOTE_ADDR'], $_SESSION['accname'], $vInfo[2]);			
			$cFetch = mssql_fetch_array($cQuery);
			switch($cFetch['num'])
			{
				case 0:
					msquery("INSERT INTO %s.dbo.vote VALUES ('%s','%s','%s',getdate())", $ini['MSSQL']['extrasDB'], $vInfo[2], $_SESSION['accname'], $_SERVER['REMOTE_ADDR']);
					msquery("UPDATE cash.dbo.user_cash SET amount = amount + '%s'WHERE user_no = '%s'", $vinfo[0], $_SESSION['user_no']);
					header('Location: '.$vInfo[2]);
					break;
				case 1:
					$timeleft = strtotime($cFetch['wDate']." +".$vInfo[1]." seconds");
					if ($timeleft <= strtotime(date("Y-m-d G:i")))
					{
						msquery("INSERT INTO %s.dbo.vote VALUES ('%s','%s','%s',getdate())", $ini['MSSQL']['extrasDB'], $vInfo[2], $_SESSION['accname'], $_SERVER['REMOTE_ADDR']);
						msquery("UPDATE cash.dbo.user_cash SET amount = amount + '%s'WHERE user_no = '%s'", $vinfo[0], $_SESSION['user_no']);
						header('Location: '.$vInfo[2]);

					}
					else
					{
						echo '<tr><td>You can\'t vote there anymore! Please try again on ',entScape(date('F jS H:i (\G\M\TO)',$timeleft)),'</td></tr>';

					}
					break;
			}
		}
		else
		{
			echo '<tr><td>You have not visited d-shop in-game.</td></tr>';
		}

	}
}
else
{
	echo '<tr><td>No sites available for voting. Please check back later.</td></tr>';
}
echo '</table>';
?>
