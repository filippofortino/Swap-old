<?php
	$db = new mysqli(HOST, USER, PASS, DATABASE);
	
	$feedback = "";
	
	if($db->connect_errno > 0)
		$feedback = 'Error connecting to the database ' . $db->connect_error;
?>