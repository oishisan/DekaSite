<?php
if(array_key_exists('side', $_SESSION['aSites']))
{	echo '<ul id="sidelist">';
	foreach ($_SESSION['aSites']['side'] as $key=>$val)
	{
		if($_GET['do']!= $key)
		{
			echo '<li><a href="?do=',$key,'">',$val[1],'</a></li>';
		}
		else
		{
			echo '<li><span id="currentp">',$val[1],'</span></li>';
		}
	}
	echo '</ul>';
}
?>
