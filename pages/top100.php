<?php
echo '<table><tr><td colspan="7"><a href="?do=',entScape($_GET['do']),'">Highest levels</a> | <a href="?do=',entScape($_GET['do']),'&type=pk">PKers</a> | <a href="?do=',entScape($_GET['do']),'&type=pvp">PvPers</a></td></tr>';
if (empty($_GET['type']))
{
	$pplist = msquery("select top 100 character.dbo.user_character.character_name, character.dbo.user_character.wLevel, character.dbo.user_character.dwExp, character.dbo.guild_info.guild_name from character.dbo.user_character left join account.dbo.user_profile on character.dbo.user_character.user_no = account.dbo.user_profile.user_no left join character.dbo.guild_char_info on character.dbo.user_character.character_name = character.dbo.guild_char_info.character_name left join character.dbo.guild_info on character.dbo.guild_char_info.guild_code = character.dbo.guild_info.guild_code where account.dbo.user_profile.login_tag <> 'N' and (character.dbo.guild_info.guild_name is null or character.dbo.guild_info.guild_name <> '%s') order by character.dbo.user_character.wLevel Desc, character.dbo.user_character.dwExp desc", $ini['Other']['toprank.exempt']);
	$count = 1;
	echo '<tr><td>Rank</td><td>Name</td><td>Guild</td><td>Level</td></tr>';
	while($list = mssql_fetch_array($pplist))
	{
		echo '<td>',$count,'</td><td>',entScape($list['character_name']),'</td>';
		$search = strstr($list['guild_name'], "<color=");
		if ($search != false)
		{
			if (preg_match ('/<color=(.*)>(.*)/', $list['guild_name'], $matchesarray))
			{
				if(!preg_match('/[a-fA-F0-9]{6}/', $matchesarray[1]))
				{
					echo '<td>',entScape($list['guild_name']),'</td>';	
				}
				else
				{
					echo '<td><span style="color:',entScape($matchesarray[1]),';">',entScape($matchesarray[2]),'</span></td>';
				}
			}
		}
		else
		{				
			echo '<td>',entScape($list['guild_name']),'</td>';
		}
		echo '<td>',entScape($list['wLevel']),'</td></tr>';
		$count ++;
	}
	echo '</table>';
}
elseif($_GET['type'] == 'pk')
{
	$pplist = msquery("select top 100 character.dbo.user_character.character_name as wChar , wPKCount, character.dbo.guild_info.guild_name from character.dbo.user_character left join account.dbo.user_profile on account.dbo.user_profile.user_no = character.dbo.user_character.user_no left join character.dbo.guild_char_info on character.dbo.user_character.character_name = character.dbo.guild_char_info.character_name left join character.dbo.guild_info on character.dbo.guild_char_info.guild_code = character.dbo.guild_info.guild_code WHERE wPKCount > '0' and account.dbo.user_profile.login_tag = 'Y' and (character.dbo.guild_info.guild_name is null or character.dbo.guild_info.guild_name <> '%s') order by wPKCount desc", $ini['Other']['toprank.exempt']);
	$count = 1;
	echo '<tr><td>Rank</td><td>Name</td><td>Guild</td><td>PK Points</td></tr><tr>';
	while($list = mssql_fetch_array($pplist))
	{
		echo '<td>',$count,'</td> <td>',entScape($list['wChar']),'</td>';
		$search = strstr($list['guild_name'], "<color=");
		if ($search != false)
		{
			if (preg_match ('/<color=(.*)>(.*)/', $list['guild_name'], $matchesarray))
			{
				if(!preg_match ('/[a-fA-F0-9]{6}/', $matchesarray[1]))
				{
					echo '<td>',entScape($list['guild_name']),'</td>';
					
				}
				else
				{
					echo '<td><span style="color:',entScape($matchesarray[1]),';">',entScape($matchesarray[2]),'</span></td>';
				}
			}
		}
		else
		{
			echo '<td>',entScape($list['guild_name']),'</td>';
		}
		echo'<td>',entScape( $list['wPKCount']),'</td></tr><tr>';
		$count ++;
	}
	echo '</tr></table>';
}
else
{
	$pplist = msquery("select top 100 character.dbo.user_character.character_name as wChar ,dwPVPpoint, wWinRecord, wLoseRecord, wLevel, character.dbo.guild_info.guild_name, round(((case when wLoseRecord=0 then 9999 else cast(wWinRecord as decimal)/cast(wLoseRecord as decimal)end)),3) as ratio from character.dbo.user_character left join account.dbo.user_profile on account.dbo.user_profile.user_no = character.dbo.user_character.user_no left join character.dbo.guild_char_info on character.dbo.user_character.character_name = character.dbo.guild_char_info.character_name left join character.dbo.guild_info on character.dbo.guild_char_info.guild_code = character.dbo.guild_info.guild_code WHERE (wWinRecord > 0 or wLoseRecord > 0) and account.dbo.user_profile.login_tag = 'Y' and (character.dbo.guild_info.guild_name is null or character.dbo.guild_info.guild_name <> '%s') order by dwPVPpoint desc, ratio desc, wLoseRecord asc, wWinRecord desc", $ini['Other']['toprank.exempt']);
	$count = 1;
	echo '<tr><td>Rank</td><td>Name</td><td>Guild</td><td>Points</td><td>Wins</td><td>Losses</td><td>W/L Ratio</td></tr><tr>';
	while($list = mssql_fetch_array($pplist) )
	{
		echo '<td>',$count,'</td><td>',entScape($list['wChar']),'</td>';
		$search = strstr($list['guild_name'], "<color=");
		if ($search != false)
		{
			if (preg_match ('/<color=(.*)>(.*)/', $list['guild_name'], $matchesarray))
			{
				if(!preg_match ('/[a-fA-F0-9]{6}/', $matchesarray[1]))
				{
					echo '<td>',entScape($list['guild_name']),'</td>';					
				}
				else
				{
					echo '<td><span style="color:',entScape($matchesarray[1]),';">',entScape($matchesarray[2]),'</span></td>';
				}
			}
		}
		else
		{
			echo '<td>',entScape($list['guild_name']),'</td>';
		}
		echo '<td>',entScape($list['dwPVPpoint']),'</td><td>',entScape($list['wWinRecord']),'</td><td>',entScape($list['wLoseRecord']),'</td><td>';
		if ($list['wLoseRecord'] == 0)
		{
			echo '<B>Undefeated!</B>';
		}
		else
		{
			echo entScape(round($list['ratio'], 2));
		}
		echo '</td></tr><tr>';
		$count++;		
	}
	echo '</tr></table>';
}
?>
