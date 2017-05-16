<?php
	session_start();
	//Include functions & meta data
	require '../functions/apicalls.php';
	$config = require('../config.php');
	$title = "Add color | SocialDomayn";
	$stylesheet = "jodel.css";
	include '../functions/header.php';

	//check if user is logged in & has required caps
	if(!isset($_SESSION['userid']) || !isset($_SESSION['caps_add_color'])) {
		header('Location: ' . $config->baseUrl . '/login.php');
	}

	//set up working variables
	$userid = $_SESSION['userid'];
	$apiroot = $config->apiUrl;
