<?php
	$server = 'shareddb-g.hosting.stackcp.net';
	$username = 'azanbertre';
	$password = 'A2m3aQY5Rnew';
	$database = 'forumdb-323515a5';

	$connection = mysqli_connect($server, $database, $password);
	if(!$connection)
	{
	    exit("Database Connection Failed");
	}
	$select_db = mysqli_select_db($connection, $database);
	if(!$select_db){
	  	exit("Database Connection Failed");
	}
?>

