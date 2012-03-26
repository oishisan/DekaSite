<?php
if(!empty($_GET['do']))
{
	if(array_key_exists($_GET['do'], $_SESSION['aSites']['top']))
	{
		include $_SESSION['aSites']['top'][$_GET['do']][0];
	}
	elseif(array_key_exists($_GET['do'], $_SESSION['aSites']['side']))
	{
		include $_SESSION['aSites']['side'][$_GET['do']][0];
	}
}
?>
