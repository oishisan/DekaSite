<?php
requireExtras();
echo '<table><tr>';
$dQuery = msquery("SELECT * FROM %s.dbo.site_download ORDER by sid ASC", $ini['MSSQL']['extrasDB'])->fetchAll();
if(count($dQuery) > 0)
{
	foreach($dQuery as $dFetch)
	{
		echo '
		<tr><td>',entScape($dFetch['name']),'</td></tr>
		<tr><td>Version: ',entScape($dFetch['version']),'</td></tr>
		<tr><td>',entScape($dFetch['descr'],true,true),'</td></tr>
		<tr><td><a href="',entScape($dFetch['link']),'" target="_blank">Download</a></td></tr>';
	}
}
else
{
	echo '<tr><td>No downloads available yet. Please check back later.</td></tr>';
}
echo '</table>';
?>
