<?php
/*
CSS page specific IDs
---------------------
#eContainter	Container per event
#name		Title of the event
#cTime		Current time display
#host		Host of event
#now		Text when an event is in progress
#time		Text before event starts
#desc		Description of event
*/
ob_start();
include 'config\core.php';
requireExtras();
$eQuery = msquery("SELECT * FROM %s.dbo.event where eEnd>getdate() ORDER by eStart ASC", $ini['MSSQL']['extrasDB']);
echo '<html><title>',entScape($ini['Other']['site.title']),' Events</title><head><style type="text/css">';
        include $ini['Other']['site.css'];
echo '</style></head><body><div id="cTime">Current time: ',entScape(date("M j o g:i A")),'</div>';
$count = mssql_num_rows($eQuery);
if ($count > 0)
{
	while($eFetch = mssql_fetch_array($eQuery))
	{
		echo '<div id="eContainer"><span id="name">',entScape($eFetch['eName']),'</span><br>
		Hosted by <span id="host">',entScape($eFetch['eHost']),'</span><br>';
		if((strtotime($eFetch['eStart'])<=strtotime(date("n/j/o g:i A"))) && (strtotime($eFetch['eEnd'])>= strtotime(date("n/j/o g:i A"))))
		{
		echo '<span id="now">Now until ',entScape($eFetch['eEnd']),'</span><br>';
		}
		else
		{
		echo 'During <span id="time">',entScape($eFetch['eStart']),' - ',entScape($eFetch['eEnd']),'</span><br>';
		}
		echo'<span id="desc">',entScape($eFetch['eDesc'],true,true),'</span></div>';
	}
}	
else
{
	echo 'No upcoming events posted.';
}
include 'config/footer.php';
ob_end_flush();
?>
