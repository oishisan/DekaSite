<?php
echo '<table><form action="?do=',entScape($_GET['do']),'"method="POST">
<tr><td>Character Name:<br><input type="text" name="charname"></td></tr>
<tr><td><input type="checkbox" name="box[]" value="delfq">Delete Finished Quests<br>
<input type="checkbox" name="box[]" value="delqip">Delete Quest In-Progress<br>
<input type="checkbox" name="box[]" value="delinv">Delete Inventory<br>
<input type="checkbox" name="box[]" value="delmail">Delete Mail<br>
<input type="checkbox" name="box[]" value="delshop">Delete Shop<br>
<input type="checkbox" name="box[]" value="delskill">Delete Skills<br>
<input type="checkbox" name="box[]" value="delskillbar">Delete Skillbar<br>
<input type="checkbox" name="box[]" value="delstorage">Delete Storage<br>
<input type="checkbox" name="box[]" value="delei">Delete Equipped Items</td></tr>
<tr><td><input type="submit" name="select" value="Delete"></td></tr></form></table>';
if(!empty($_POST['select']) && !empty($_POST['charname'])) 
{
	$cQuery = msquery("SELECT character_no, count(character_no) as num FROM character.dbo.user_character WHERE character_name = '%s' group by character_no", $_POST['charname']);
	$cFetch = mssql_fetch_array($cQuery);
	if(empty ($_POST['charname']) || $cFetch['num'] != 1) 
	{
		echo "<br>Could not find the character name.";
	}			 
	else
	{
		if (in_array('delfq', $_POST['box']) == true)
		{
			if(msquery("DELETE FROM character.dbo.User_Quest_Done WHERE character_no = '%s'", $cFetch['character_no']))
			{
				echo '<br>Completed quests have been deleted.';
			}
		}
		if (in_array('delqip', $_POST['box']) == true)
		{
			if(msquery("DELETE FROM character.dbo.User_Quest_Doing WHERE character_no = '%s'", $cFetch['character_no']))
			{
				echo '<br>On-going quests have been deleted.';
			}
		}
		if (in_array('delinv', $_POST['box']) == true)
		{
			if(msquery("DELETE FROM character.dbo.user_bag WHERE character_no = '%s'", $cFetch['character_no']))
			{
				echo '<br>Inventory items have been deleted.';
			}
		}
		if (in_array('delmail', $_POST['box']) == true)
		{
			if(msquery("DELETE FROM character.dbo.USER_POSTBOX WHERE character_no = '%s'", $cFetch['character_no']))
			{
				echo '<br>Mailbox items has been deleted.';
			}
		}
		if (in_array('delshop', $_POST['box']) == true)
		{
			if(msquery("DELETE FROM character.dbo.user_storage WHERE character_no = '%s'", $cFetch['character_no']))
			{
				echo '<br>Personal shop items have been deleted.';
			}
		}
		if (in_array('delskill', $_POST['box']) == true)
		{		
			if(msquery("DELETE FROM character.dbo.user_skill WHERE character_no = '%s'", $cFetch['character_no']))
			{
				echo '<br>Skills have been deleted.';
			}
		}
		if (in_array('delskillbar', $_POST['box']) == true)
		{
			if(msquery("DELETE FROM character.dbo.user_slot WHERE character_no = '%s'", $cFetch['character_no']))
			{
				echo '<br>Skillbar has been deleted.';
			}
		}
		if (in_array('delstorage', $_POST['box']) == true)
		{
			if(msquery("DELETE FROM character.dbo.user_storage WHERE character_no = '%s'", $cFetch['character_no']))
			{
				echo '<br>Storage items have been deleted.';
			}
		}
		if (in_array('delei', $_POST['box']) == true)
		{
			if(msquery("DELETE FROM character.dbo.user_suit WHERE character_no = '%s'", $cFetch['character_no']))
			{
				echo '<br>Equipped items have been deleted.';
			}
		}
	}
}
?>

