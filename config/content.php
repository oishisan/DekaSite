<?php
if(!empty($_GET['do']))
{
	if(array_key_exists($_GET['do'], $_SESSION['aSites']['top']))
	{
		echo '<div id="c-',entScape($_GET['do']),'">';
		include $_SESSION['aSites']['top'][$_GET['do']][0];
		echo '</div>';
	}
	elseif(array_key_exists($_GET['do'], $_SESSION['aSites']['side']))
	{
		echo '<div id="c-',entScape($_GET['do']),'">';
		include $_SESSION['aSites']['side'][$_GET['do']][0];
		echo '</div>';

	}
}
?>
