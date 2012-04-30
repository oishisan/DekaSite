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
$eQuery = msquery("SELECT * FROM %s.dbo.event where eEnd>'%s' ORDER by eStart ASC", $ini['MSSQL']['extrasDB'], date('n/j/Y g:i:s A'));
echo '<html><title>',entScape($ini['Other']['site.title']),' Events</title><head><style type="text/css">';
        include $ini['Other']['site.css'];
echo '</style></head><body><div id="cTime">Current time: ',entScape(date('M j o g:i A')),'</div>';
$eQuery = $eQuery->fetchAll();
if (count($eQuery) > 0)
{
	foreach($eQuery as $eFetch)
	{
		echo '<div id="eContainer"><span id="name">',entScape($eFetch['eName']),'</span><br>
		Hosted by <span id="host">',entScape($eFetch['eHost']),'</span><br>';
		$start = strtotime($eFetch['eStart']);
		$end = strtotime($eFetch['eEnd']);
		if( $start <=strtotime(date("n/j/o g:i A")) && $end>= strtotime(date("n/j/o g:i A")))
		{
		echo '<span id="now">Now until ',entScape(date('M j o g:i A',$end)),'</span><br>';
		}
		else
		{
		echo 'During <span id="time">',entScape(date('M j o g:i A',$start)),' - ',entScape(date('M j o g:i A',$end)),'</span><br>';
		}
		echo'<span id="desc">',entScape($eFetch['eDesc'],true,true),'</span></div>';
	}
}	
else
{
	echo '<div id="nEvent">No upcoming events posted.</div>';
}
include 'config/footer.php';
ob_end_flush();
?>
