<?php
/*
CSS page specific IDs
---------------------
#l-*    Where * is use the page's do action to style that link

CSS page specific classes
-------------------------
.currentp       The text of the current page
*/
if(isset($_SESSION['aSites']['side']) && array_key_exists('side', $_SESSION['aSites']))
{	echo '<ul>';
	foreach ($_SESSION['aSites']['side'] as $key=>$val)
	{
		if($_GET['do']!= $key)
		{
			echo '<li><a href="?do=',entScape($key),'" id="l-',entScape($key),'">',entScape($val[1]),'</a></li>';
		}
		else
		{
			echo '<li><span class="currentp" id="l-',entScape($key),'">',entScape($val[1]),'</span></li>';
		}
	}
	echo '</ul>';
}
?>
