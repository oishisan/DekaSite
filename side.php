<?php
if(array_key_exists('side', $_SESSION['aSites']))
{
	foreach ($_SESSION['aSites']['side'] as $key=>$val)
		{
			echo '<a href="?do=',$key,'">',$val[1],'</a><br><br>';
		}
}
?>
