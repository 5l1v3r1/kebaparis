<?php

	include('../classes.php');
	
	$myDB = new Database();
	$myDB->connect();
	
	$myuser = new user();
	
	$password = $_REQUEST["pw"];
	echo $password;
	
	if($myuser->changePassword($password) == true)
	{
		echo "TRUE";
	}
	else
	{
		echo "FALSE";
	}
	
	$myDB->quit();

?>