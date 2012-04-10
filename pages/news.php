<?php
requireExtras();
$query = msquery("SELECT TOP %s * FROM %s.dbo.site_news ORDER by sid DESC", $ini['Other']['news.amount'], $ini['MSSQL']['extrasDB']);
echo '<table>';
$count = mssql_num_rows($query);
if ($count > 0)
{
	while($r = mssql_fetch_array($query))
	{
		echo '<tr><td>',entScape($r['title']),'
		<br>Written by ',entScape($r['wroteby']),' at ',entScape($r['wrotedate']),'<br>'.entScape($r['content']).'<br><br></td></tr>';
	}
}	
else
{
	echo '<tr><td>No news has been posted.</td></tr>';
}
echo '</table>';
?>
