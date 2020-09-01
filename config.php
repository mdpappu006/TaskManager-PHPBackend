<?php  
	define("DBHOST", "localhost:3308");
	define("DBUSER", "root");
	define("DBPASS", "");
	define("DBNAME", "todotask");
	$db = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
?>