<?php
session_start();
//Set default values for head & load it
$title = "Report | SocialDomayn";
$stylesheet = "jodel.css";
include 'functions/header.php';
//Load API functions
include 'functions/apicalls.php';
include 'functions/admintools.php';
$config = include('config.php');
$apiroot = $config->apiUrl;

if(!isset($_SESSION['userid'])) {
 header('Location: https://jodel.domayntec.ch/login.php');
}

if(!isset($_GET['type']) && !isset($_GET['id'])){
	die("You need to select content");
}

//qurey the ID of the user
$userid = $_SESSION['userid'];
//Get data about the user & save it in $_SESSION values.
$userjson = getCall("https://jodel.domayntec.ch/api.php/jodlers?transform=1&filter=jodlerID,eq,$userid");
$user = json_decode($userjson, true);
foreach($user['jodlers'] as $jodler){
	$karma = $jodler['karma'];
	$accstate = $jodler['account_state'];	
}
if($accsstate < 2){
	die("You shall not pass!");
}

$_SESSION['karma'] = $karma;
$_SESSION['acctype'] = $accstate;

$type = $_GET['type'];
$contentID = $_GET['id'];

