<?php
requireExtras();
echo '<table><tr>';
$dQuery = msquery("SELECT * FROM %s.dbo.site_download ORDER by sid ASC", $ini['MSSQL']['extrasDB']);
if(mssql_num_rows($dQuery) > 0)
{
	while($dFetch = mssql_fetch_array($dQuery))
	{
		echo '
		<tr><td>',entScape($dFetch['name']),'</td></tr>
		<tr><td>Version: ',entScape($dFetch['version']),'</td></tr>
		<tr><td>',entScape($dFetch['descr']),'</td></tr>
		<tr><td><a href="',entScape($dFetch['link']),'" target="_blank">Download</a></td></tr>';
	}
}
else
{
	echo '<tr><td>No downloads available yet. Please check back later.</td></tr>';
}
echo '</table>';
?>
