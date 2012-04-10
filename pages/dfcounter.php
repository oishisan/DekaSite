<?php
$hour_wars = array();
$query = msquery("Select sort_cd from character.dbo.cm_bcd_item order by orderby_no asc");
while ($fetch = mssql_fetch_array($query))
{
$hour_wars[] = $fetch['sort_cd'];
}
$search = array_search(0, $hour_wars);
if ($search !== false)
{
$hour_wars[$search] = 24;
}
sort($hour_wars);
$hour_now = date('G');
$hour_next = 0;
foreach($hour_wars as $hour_war)
{
	if($hour_war > $hour_now)
	{
		$hour_next = $hour_war;
		break;
	}
}

$wartime = (mktime($hour_next, 0, 0) - time());
?>

<script type="text/javascript">

countdown_x1500 = <?php print $wartime; ?>;
function convert_to_time_x1500(secs_x1500)
{
	secs_x1500 = parseInt(secs_x1500);
	hh_x1500 = secs_x1500 / 3600;
	hh_x1500 = parseInt(hh_x1500);
	mmt_x1500 = secs_x1500 - (hh_x1500 * 3600);
	mm_x1500 = mmt_x1500 / 60;
	mm_x1500 = parseInt(mm_x1500);
	ss_x1500 = mmt_x1500 - (mm_x1500 * 60);
	
	if (hh_x1500 > 23)
	{
		dd_x1500 = hh_x1500 / 24;
		dd_x1500 = parseInt(dd_x1500);
		hh_x1500 = hh_x1500 - (dd_x1500 * 24);
	}
	else
	{ dd_x1500 = 0; }
	
	if (ss_x1500 < 10) { ss_x1500 = "0"+ss_x1500; }
	if (mm_x1500 < 10) { mm_x1500 = "0"+mm_x1500; }
	if (hh_x1500 < 10) { hh_x1500 = "0"+hh_x1500; }
	if (dd_x1500 == 0)
	{ return (hh_x1500+" hour(s) "+mm_x1500+" minute(s) "+ss_x1500)+" second(s)"; }
	else
	{
		if (dd_x1500 > 1)
		{ return (dd_x1500+" days "+hh_x1500+" hour(s) "+mm_x1500+" minute(s) "+ss_x1500+" second(s)"); }
		else
		{ return (dd_x1500+" day "+hh_x1500+" hour(s) "+mm_x1500+" minute(s) "+ss_x1500+" second(s)");  }
	}
}

function do_cd_x1500()
{
	if (countdown_x1500 < 0)
	{
		document.getElementById('nationwarx1500').innerHTML = "<b>is in progress!</b>";
	}
	else
	{
		document.getElementById('nationwarx1500').innerHTML = convert_to_time_x1500(countdown_x1500);
		setTimeout('do_cd_x1500()', 1000);
	}
	countdown_x1500 = countdown_x1500 - 1;
}

document.write("<span id='nationwarx1500'></span>\n");
do_cd_x1500();

</script>
