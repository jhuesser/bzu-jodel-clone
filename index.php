<?php
	session_start();
	//If user is logged in -> go to jodels.php else -> goto login.php
	$config = require('config.php');
	if(!isset($_SESSION['userid'])) {
 		header('Location: ' . $config->baseUrl . 'login.php');
	} else {
		header('Location: ' . $config->baseUrl . 'jodels.php');
	}