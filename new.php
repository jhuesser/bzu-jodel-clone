<?php

session_start();
//Set default values for head & load it
$title = "Create Post | SocialDomayn";
$stylesheet = "jodel.css";
include 'functions/header.php';
//Load API functions
include 'functions/apicalls.php';
$config = include('config.php');
$apiroot = $config->apiUrl;
//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];

if(!isset($_SESSION['userid'])) {
 die('You need to <a href="login.php">login</a> first');
}

$callurl = $apiroot . "jodlers?transform=1&filter=jodlerID,eq," . $userid;
$userjson = getCall($callurl);
$user = json_decode($userjson, true);
foreach($user['jodlers'] as $jodler){
	$karma = $jodler['karma'];
	$accstate = $jodler['account_state'];
	
}
$_SESSION['karma'] = $karma;
$_SESSION['acctype'] = $accstate;

if(isset($_GET['post'])){
	$jodel = htmlspecialchars($_POST['jodel'], ENT_QUOTES);
	$color = $_POST['color'];
	
	$postfields = "{\n  \"jodlerIDFK\": \"$userid\",\n  \"colorIDFK\": \"$color\",\n  \"jodel\": \"$jodel\"\n}";
	$callurl = $apiroot . "jodels";
	$posted = postCall($callurl, $postfields);
	
	$karma = $karma + $config->karma_calc['create_jodel'];
	$postfields = "{\n  \n  \"karma\": $karma\n}";
	$callurl = $apiroot . "jodlers/" . $userid;
	$karmaupdated = putCall($callurl, $postfields);

	header('Location: https://jodel.domayntec.ch/jodels.php');

}

?>
<div id="top"></div>

<ul class="nav justify-content-center">
		<li class="nav-item">
			<a class="nav-link" href="jodels.php"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
		</li>
		<li class="nav-item">
    <a class="nav-link" href="javascript:window.location.reload();"><i class="fa fa-refresh" aria-hidden="true"></i></a>
  </li>
  
  <li class="nav-item">
    <a class="nav-link" href="user.php"><i class="fa fa-user" aria-hidden="true"></i><?php echo $_SESSION['karma'];?></a>
  </li>
  
</ul>
<div class="test"></div>

<?php

	$callurl = $apiroot . "colors?transform=1";
	$allcolorsjson = getCall($callurl);
	$allcolors = json_decode($allcolorsjson, true);
	$nmbofcolors = count($allcolors['colors']);

	$colornmb = rand(1, $nmbofcolors);
	$callurl = $apiroot . "colors?transform=1&filter=colorID,eq," . $colornmb;
	$colors = getCall($callurl);
	$color = json_decode($colors, true);
	foreach($color['colors'] as $col){
		$colorname= $col['colordesc'];
		$colhex = $col['colorhex'];
	}

	?>


	<form action="?post=1" method="POST">
	<div class="form-group">
	<label for="jodel">Enter your message</label>
	<textarea class="form-control" rows="10" name="jodel" placeholder="Your post" style="color:white;background-color:#<?php echo $colhex;?>"></textarea>
	</div>
	<input type="hidden" name="color" value="<?php echo $colornmb;?>">
	<button type="submit" class="btn btn-warning">Submit</button>
	
		
		</form>



