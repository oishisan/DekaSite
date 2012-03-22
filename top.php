<?php
if($_SESSION['auth'] == 0 && array_key_exists('guest', $ini['whitelist.top']))
{
	foreach ($ini['whitelist.top']['guest'] as $val)
	{
		echo '<a href="?do=',splitWV($val,2),'">',splitWV($val,1),'</a> | ';
	}
}
if($_SESSION['auth'] == 1 && array_key_exists('member', $ini['whitelist.top']))
{
	foreach ($ini['whitelist.top']['member'] as $val)
	{
		echo '<a href="?do=',splitWV($val,2),'">',splitWV($val,1),'</a> | ';
	}
}
?>
