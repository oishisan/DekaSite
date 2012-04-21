<?php
if(!empty($_GET['do']))
{
	if(array_key_exists($_GET['do'], $_SESSION['aSites']['top']))
	{
		echo '<div id="c-',entScape($_GET['do']),'"><div id="ptitle">',entScape($_SESSION['aSites']['top'][$_GET['do']][1]),'</div>';
		include $_SESSION['aSites']['top'][$_GET['do']][0];
		echo '</div>';
	}
	elseif(array_key_exists($_GET['do'], $_SESSION['aSites']['side']))
	{
		echo '<div id="c-',entScape($_GET['do']),'"><div id="ptitle">',entScape($_SESSION['aSites']['side'][$_GET['do']][1]),'</div>';
		include $_SESSION['aSites']['side'][$_GET['do']][0];
		echo '</div>';
	}
	else
	{
		if(isset($ini['Other']['site.default']))
		{
			if(array_key_exists($ini['Other']['site.default'], $_SESSION['aSites']['top']))
			{
				echo '<div id="c-',entScape($ini['Other']['site.default']),'"><div id="ptitle">',entScape($_SESSION['aSites']['top'][$ini['Other']['site.default']][1]),'</div>';				
				include $_SESSION['aSites']['top'][$ini['Other']['site.default']][0];
				echo '</div>';
			}
			elseif(array_key_exists($ini['Other']['site.default'], $_SESSION['aSites']['side']))
			{
				echo '<div id="c-',entScape($ini['Other']['site.default']),'"><div id="ptitle">',entScape($_SESSION['aSites']['side'][$ini['Other']['site.default']][1]),'</div>';				
				include $_SESSION['aSites']['side'][$ini['Other']['site.default']][0];
				echo '</div>';
			}
		}
	}
}
else
{
	if(isset($ini['Other']['site.default']))
	{
		if(array_key_exists($ini['Other']['site.default'], $_SESSION['aSites']['top']))
		{
			echo '<div id="c-',entScape($ini['Other']['site.default']),'"><div id="ptitle">',entScape($_SESSION['aSites']['top'][$ini['Other']['site.default']][1]),'</div>';
			include $_SESSION['aSites']['top'][$ini['Other']['site.default']][0];
			echo '</div>';
		}
		elseif(array_key_exists($ini['Other']['site.default'], $_SESSION['aSites']['side']))
		{
			echo '<div id="c-',entScape($ini['Other']['site.default']),'"><div id="ptitle">',entScape($_SESSION['aSites']['side'][$ini['Other']['site.default']][1]),'</div>';
			include $_SESSION['aSites']['side'][$ini['Other']['site.default']][0];
			echo '</div>';
		}
	}
}
?>
