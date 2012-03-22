<?php
//Check if logged in
if($_SESSION['auth'] > 0)
{
	//Destroy session variables and session
	session_unset();
	session_destroy();
	//redirect to index
	header("location:index.php");
}
else
{
	echo 'You\'re  not logged in!';
}
?>
