<?php
$dQuery = msquery("SELECT TOP 50 character_name,product,intime FROM cash.dbo.user_use_log ORDER BY intime DESC");
echo '<table><tr><td>Character Name</td><td>Item Name</td><td>Date</td></tr>';
while($dFetch = mssql_fetch_array($dQuery))
{
	echo '<tr><td>',entScape($dFetch['character_name']),'</td>';
	echo '<td>',entScape($dFetch['product']),'</td>';	
	echo '<td>',entScape($dFetch['intime']),'</td></tr>';
}
	echo "</table>";
?>
