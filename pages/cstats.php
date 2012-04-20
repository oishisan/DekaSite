<?php
$query = msquery("SELECT character_name ,dwPVPpoint, wWinRecord, wLoseRecord from character.dbo.user_character where user_no = '%s' order by dwPVPpoint desc, wWinRecord asc, wLoseRecord asc", $_SESSION['user_no']);
echo '<table>';
if (mssql_num_rows($query) > 0)
{
	echo '
	<tr>
		<th>Character</th>
		<th>Points</th>
		<th>Wins</th>
		<th>Losses</th>
		<th>W/L ratio</th>
	</tr>';
	while($list = mssql_fetch_array($query))
	{
		echo '
		<tr>
			<td>',entScape($list['character_name']),'</td>
			<td>',entScape($list['dwPVPpoint']),'</td>
			<td>',entScape($list['wWinRecord']),'</td>
			<td>',entScape($list['wLoseRecord']),'</td>
			<td>';
		if (($list['wLoseRecord'] == 0 && $list['wWinRecord'] > 0) || ($list['wWinRecord'] ==0 && $list['wLoseRecord']==0))
		{
			echo 'Undefeated!';
		}
		else
		{
			echo entScape(round($list['wWinRercord']/$list['wLoseRecord'], 2));
		}
		echo '</td></tr>';
	}
}
else
{
	echo '<tr><td>This account does not have any characters.</td></tr>';
}
echo '</table>';
?>
