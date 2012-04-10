<?php
$query = msquery("SELECT character_name ,dwPVPpoint, wWinRecord, wLoseRecord from character.dbo.user_character where user_no = '%s' order by dwPVPpoint desc, wWinRecord asc, wLoseRecord asc", $_SESSION['user_no']);
echo '<table>';
if (mssql_num_rows($query) > 0)
{
	echo '
	<tr>
		<td>Character</td>
		<td>Points</td>
		<td>Wins</td>
		<td>Losses</td>
		<td>W/L ratio</td>
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
			echo '<b>Undefeated!</b>';
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
