<?php
if(!empty($_GET['do']) && array_key_exists($_GET['do'], $_SESSION['aSites']))
{
	$page = $_SESSION['aSites'][$_GET['do']];
}
else
{
	$page = $_SESSION['aSites']['home'];
}
include $page;
?>
