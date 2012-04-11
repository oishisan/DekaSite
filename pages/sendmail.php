<?php
echo '<table><form action="?do=',entScape($_GET['do']),'" method="POST">
	<tr><td>To: <input type="text" name="charname" maxlength="20"></td></tr>
	<tr><td>From: <input type=text name="from" maxlength="50"></td></tr>
	<tr><td>Subject: <input type="text" name="subject" maxlength="50" /></td></tr>
	<tr><td>Item ID: <input type="text" name="itemid" maxlength="10"></td></tr>
	<tr><td>Dil: <input type="text" name="dil" maxlength="9"></td></tr>
	<tr><td>Message<br><textarea name="message" cols="45" rows="5"></textarea></td></tr>
	<tr><td colspan="2"><input type="submit" name="send" value="Send"></td></tr>
	</form></table>';

if(isset($_POST['send'])) 
{	
	$cQuery = msquery("SELECT character_no, count(character_no) as num FROM character.dbo.user_character WHERE character_name = '%s' group by character_no", $_POST['charname']);
	$cFetch = mssql_fetch_array($cQuery);
	if(empty($_POST['charname']) || $cFetch['num'] < 1) 
	{
		echo 'Could not find the character name.';
	} 
	elseif(!ctype_digit($_POST['itemid']) && !empty($_POST['itemid'])) 
	{
		echo 'Item ID must be a positive integer.';
	} 
	elseif(!ctype_digit($_POST['dil']) && !empty($_POST['dil'])) 
	{
		echo 'Dil must be a positive integer.';
	} 
	else
	{
		if (empty($_POST['dil'])) $_POST['dil'] = '0';
		if (empty($_POST['itemid'])) $_POST['itemid'] = '0';
		if (empty($_POST['from'])) $_POST['from'] = '[Anonymous]';
		msquery("EXEC character.dbo.SP_POST_SEND_OP '%s','%s',1,'%s','%s','%s','%s',0", $cFetch['character_no'], $_POST['from'], $_POST['subject'], $_POST['message'], $_POST['itemid'], $_POST['dil']);
		echo 'Mail has been sent successfully to ',entScape($_POST['charname']),'.';
	}
}

?>
