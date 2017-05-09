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
 header('Location: '. $config->baseUrl . 'login.php');
}

if(!isset($_GET['type']) && !isset($_GET['id'])){
	die("You need to select content");
}

//qurey the ID of the user
$userid = $_SESSION['userid'];
//Get data about the user & save it in $_SESSION values.
$userjson = getCall( $apiroot . "jodlers?transform=1&filter=jodlerID,eq," . $userid);
$user = json_decode($userjson, true);
foreach($user['jodlers'] as $jodler){
	$karma = $jodler['karma'];
	$accstate = $jodler['account_state'];	
}
//if($accsstate < 2){
//	die("You shall not pass!");
//}

$_SESSION['karma'] = $karma;
$_SESSION['acctype'] = $accstate;

$type = $_GET['type'];
$contentID = $_GET['id'];

if(isset($_GET['reason'])){
	$reason = $_GET['reason'];
	$reported = reportContent($config, $type, $contentID, $reason);
	header('Location: ' . $apiroot->baseUrl . 'jodels.php');
}

$abuseurl = $apiroot . "abuse?transform=1";
$absuejson = getCall($abuseurl);
$abusearray = json_decode($absuejson, true);

?>

<h1>Please select the reason for reporting this post</h1>
<div class="list-group">
<?php 
foreach($abusearray['abuse'] as $abuse){
	$abusedesc = $abuse['abusedesc'];
	
		$message = "?type=" . $type . "&id=" . $contentID . "&reason=" . $abuse['abuseID'];
		?>
		<a href="<?php echo $message ?>" class="list-group-item list-group-item-action"><?php echo $abusedesc; ?></a>

	<?php

}



?>

	
</div>
<?php
