<?php
//Destroy session variables and session
session_unset();
session_destroy();
//redirect to index
header("location:index.php");
?>
