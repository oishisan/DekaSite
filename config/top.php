<?php
/*
CSS page specific IDs
---------------------
#l-*	Where * is use the page's do action to style that link

CSS page specific classes
-------------------------
.currentp	The text of the current page
*/
if(isset($_SESSION['aSites']['top']) && array_key_exists('top', $_SESSION['aSites']))
{
	foreach ($_SESSION['aSites']['top'] as $key=>$val)
	{
		if($_GET['do'] != $key)
		{
			echo '<a href="?do=',entScape($key),'" id="l-',entScape($key),'">',entScape($val[1]),'</a>';
		}
		else
		{
		echo '<span class="currentp" id="l-',entScape($key),'">',entScape($val[1]),'</span>';
		}
	}
}
?>
