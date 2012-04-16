<?php
ob_start();
include 'config\core.php';
requireExtras();
$eQuery = msquery("SELECT * FROM %s.dbo.event where eEnd>getdate() ORDER by eStart ASC", $ini['MSSQL']['extrasDB']);
echo '<html><title>',entScape($ini['Other']['site.title']),' Events</title><head><style type="text/css">';
        include $ini['Other']['site.css'];
echo '</style></head><body><table>';
$count = mssql_num_rows($eQuery);
if ($count > 0)
{
	while($eFetch = mssql_fetch_array($eQuery))
	{
		echo '<tr><td><span>',entScape($eFetch['eName']),'</span>
		<br><i>Hosted by ',entScape($eFetch['eHost']),' ';
		if((strtotime($eFetch['eStart'])<=strtotime(date("n/j/o g:i A"))) && (strtotime($eFetch['eEnd'])>= strtotime(date("n/j/o g:i A"))))
		{
		echo '<b>RIGHT NOW!</b>';
		}
		else
		{
		echo 'during ',entScape($eFetch['eStart']),' - ',entScape($eFetch['eEnd']);
		}
		echo'</i><br>',entScape($eFetch['eDesc']),'<br><br></td></tr>';
	}
}	
else
{
	echo '<tr><td>No upcoming events posted.</td></tr>';
}
echo '</table></body></html>';
ob_end_flush();
?>
