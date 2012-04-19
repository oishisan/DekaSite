<?php
if(array_key_exists('side', $_SESSION['aSites']))
{	echo '<ul id="sidelist">';
	foreach ($_SESSION['aSites']['side'] as $key=>$val)
	{
		echo '<li><a href="?do=',$key,'">',$val[1],'</a></li>';
	}
	echo '</ul>';
}
?>
