<?php
if(array_key_exists('top', $_SESSION['aSites']))
{
	$x = count($_SESSION['aSites']['top']) - 1;
	$xi = 0;
	foreach ($_SESSION['aSites']['top'] as $key=>$val)
	{
		if($xi <> $x)
		{
			echo '<a href="?do=',$key,'">',$val[1],'</a> | ';
				
		}
		else
		{
			echo '<a href="?do=',$key,'">',$val[1],'</a>';
		}
		$xi++;
	}
}
?>
