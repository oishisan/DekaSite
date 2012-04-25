<?php
if(isset($_SESSION['aSites']['top']) && array_key_exists('top', $_SESSION['aSites']))
{
	foreach ($_SESSION['aSites']['top'] as $key=>$val)
	{
		if($_GET['do'] != $key)
		{
			echo '<a href="?do=',$key,'">',$val[1],'</a>';
		}
		else
		{
		echo '<span id="currentp">',$val[1],'</span>';
		}
	}
}
?>
