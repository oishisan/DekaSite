<?php
if(array_key_exists('side', $_SESSION['aSites']))
{
	$x = count($_SESSION['aSites']['side']) - 1;
	$xi = 0;
	foreach ($_SESSION['aSites']['side'] as $key=>$val)
	{
		if($xi <> $x)
		{
			echo '<a href="?do=',$key,'">',$val[1],'</a><br><br>';
				
		}
		else
		{
			echo '<a href="?do=',$key,'">',$val[1],'</a>';
		}
		$xi++;
	}
}
?>
