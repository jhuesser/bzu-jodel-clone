<?php
	session_start();
	//If user is logged in -> go to jodels.php else -> goto login.php
	$config = include('config.php');
	if(!isset($_SESSION['userid'])) {
 		header('Location: https://jodel.domayntec.ch/login.php');
	} else {
		header('Location: https://jodel.domayntec.ch/jodels.php');
	}