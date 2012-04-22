<?php
/*
CSS page specific IDs
---------------------
#section	Contains 1 news story
#heading	Heading of the news
#wroteby	The name of the author
#date		The date
#news		The news contnent
*/
requireExtras();
$query = msquery("SELECT TOP %s * FROM %s.dbo.site_news ORDER by sid DESC", $ini['Other']['news.amount'], $ini['MSSQL']['extrasDB']);
$count = mssql_num_rows($query);
if ($count > 0)
{
	while($r = mssql_fetch_array($query))
	{
		echo '<div id="section"><span id="heading">',entScape($r['title']),'</span>
		<br>Written by <span id="wroteby">',entScape($r['wroteby']),'</span> at <span id="date">',entScape($r['wrotedate']),'</span><br><span id="news">'.entScape($r['content']).'</span></div>';
	}
}	
else
{
	echo 'No news has been posted.';
}
?>
