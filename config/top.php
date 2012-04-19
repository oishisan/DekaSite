<?php
if(array_key_exists('top', $_SESSION['aSites']))
{
	foreach ($_SESSION['aSites']['top'] as $key=>$val)
	{
		echo '<a href="?do=',$key,'">',$val[1],'</a>';			
	}
}
?>
