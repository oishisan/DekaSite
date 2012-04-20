<?php
requireExtras();
$query = msquery("SELECT distinct Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) as IP from account.dbo.user_connlog_key where user_no = '%s'", $_SESSION['user_no']);
echo '<table><tr><th colspan="2">Game Account Login</th></tr><tr><th>IP</th><th>Login time</th></tr>';
while ($fetch = mssql_fetch_array($query))
{
	$query2 = msquery("SELECT max(login_time) as ltime from account.dbo.user_connlog_key where Cast(Cast(SubString(conn_ip, 1, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 2, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 3, 1) AS Int) As Varchar(3)) + '.' + Cast(Cast(SubString(conn_ip, 4, 1) AS Int) As Varchar(3)) = '%s'", $fetch['IP']);
	$fetch2 = mssql_fetch_array($query2);
	echo '<tr><td>',entScape($fetch['IP']),'</td><td>',entScape($fetch2['ltime']),'</td></td>';
}
echo '</table>';

$query = msquery("SELECT distinct IP from %s.dbo.sessionlog where account = '%s'", $ini['MSSQL']['extrasDB'], $_SESSION['accname']);
echo '<table><tr><th colspan="2">Site IP Login Log</th></tr><tr><th>IP</th><th>Login time</th></tr>';
while ($fetch = mssql_fetch_array($query))
{
	$query2 = msquery("SELECT max(wTime) as wTime from %s.dbo.sessionlog where IP = '%s' and wAction = 'Login Success'", $ini['MSSQL']['extrasDB'], $fetch['IP']);
	$fetch2 = mssql_fetch_array($query2);
	echo '<tr><td>',entScape($fetch['IP']),'</td><td>',entScape($fetch2['wTime']),'</td></tr>';
}
echo '</table>';
?>
