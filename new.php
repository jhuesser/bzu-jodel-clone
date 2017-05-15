<?php
	session_start();
	//Set default values for head & load it
	$title = "Create Post | SocialDomayn";
	$stylesheet = "jodel.css";
	include 'functions/header.php';
	//Load API functions
	require 'functions/apicalls.php';
	require 'functions/jodelmeta.php';
	$config = require('config.php');
	$apiroot = $config->apiUrl;
	$userid = $_SESSION['userid'];

	if(!isset($_SESSION['userid'])) {
		die('You need to <a href="login.php">login</a> first');
	}

	//Get userdata
	$callurl = $apiroot . "jodlers?transform=1&filter=jodlerID,eq," . $userid;
	$userjson = getCall($callurl);
	$user = json_decode($userjson, true);
	foreach($user['jodlers'] as $jodler){
		//get karma and account state
		$karma = $jodler['karma'];
		$accstate = $jodler['account_state'];
	}
	//set user data in session values
	$_SESSION['karma'] = $karma;
	$_SESSION['acctype'] = $accstate;

	if(isset($_GET['post'])){
		//new post created
		//encode special chars to avoid injection
		$jodel = htmlspecialchars($_POST['jodel'], ENT_QUOTES);
		//set color as local value
		$color = $_POST['color'];
		//insert new post in DB, $postfields as JSON with all data
		$postfields = "{\n  \"jodlerIDFK\": \"$userid\",\n  \"colorIDFK\": \"$color\",\n  \"jodel\": \"$jodel\"\n}";
		$callurl = $apiroot . "jodels";
		$posted = postCall($callurl, $postfields);
		//update the authors karma for creating a post
		$karma = $karma + $config->karma_calc['create_jodel'];
		$postfields = "{\n  \n  \"karma\": $karma\n}";
		$callurl = $apiroot . "jodlers/" . $userid;
		$karmaupdated = putCall($callurl, $postfields);
		//redirect to post overview
		header('Location: ' . $config->baseUrl . 'jodels.php');
	}

?>

<!-- main menu -->
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
<!-- end main menu -->
<?php
	$colorhex = getRandomColor();
?>

<!-- post form -->
<form action="?post=1" method="POST">
	<div class="form-group">
		<label for="jodel">Enter your message</label>
		<textarea class="form-control" rows="10" name="jodel" placeholder="Your post" style="color:white;background-color:#<?php echo $colorhex;?>"></textarea>
	</div>
	<!-- save the color in a hidden field -->
	<input type="hidden" name="color" value="<?php echo $colornmb;?>">
	<button type="submit" class="btn btn-warning">Submit</button>
</form>
<!-- end post form -->

<?php include 'functions/footer.php';

