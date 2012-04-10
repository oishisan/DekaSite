<?php
requireExtras();
$query = msquery("SELECT distinct Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) as IP from account.dbo.user_connlog_key where user_no = '%s'", $_SESSION['user_no']);
echo '<table><tr><td colspan="2">Game Account Login</td></tr><tr><td>IP</td><td>Login time</td></tr>';
while ($fetch = mssql_fetch_array($query))
{
	$query2 = msquery("SELECT max(login_time) as ltime from account.dbo.user_connlog_key where Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) = '%s'", $fetch['IP']);
	$fetch2 = mssql_fetch_array($query2);
	echo '<tr><td>',entScape($fetch['IP']),'</td><td>',entScape($fetch2['ltime']),'</td></tr>';
}
echo '</table>';

$query = msquery("SELECT distinct IP from %s.dbo.sessionlog where account = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['accname']);
echo '<br><br><table><tr><td colspan="2">Site IP Login Log</td></tr><tr><td>IP</td><td>Login time</td></tr>';
while ($fetch = mssql_fetch_array($query))
{
	$query2 = msquery("SELECT max(wTime) as wTime from %s.dbo.sessionlog where IP = '%s' and wAction = 'Login Success'", $ini['MSSQL']['extrasDB'], $fetch['IP']);
	$fetch2 = mssql_fetch_array($query2);
	echo '<tr><td>',entScape($fetch['IP']),'</td><td>',entScape($fetch2['wTime']),'</td></tr>';
}
echo '</table>';
?>
