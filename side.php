<?php
if($_SESSION['auth'] == 0 && array_key_exists('guest', $ini['whitelist.side']) )
{
        foreach ($ini['whitelist.side']['guest'] as $val)
        {
                echo '<a href="?do=',splitWV($val,2),'">',splitWV($val,1),'</a><br><br>';
        }
}
if($_SESSION['auth'] == 1 && array_key_exists('member', $ini['whitelist.side']))
{
	foreach ($ini['whitelist.side']['member'] as $val)
	{
		echo '<a href="?do=',splitWV($val,2),'">',splitWV($val,1),'</a><br><br>';
	}
}
?>
