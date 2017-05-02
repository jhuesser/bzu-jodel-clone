<?php
	session_start();
	//Set default values for head & load it
	$title = "Create Post | SocialDomayn";
	$stylesheet = "jodel.css";
	include 'functions/header.php';
	//Load API functions
	include 'functions/apicalls.php';
	$config = include('config.php');
	include 'functions/jodelmeta.php';
	$apiroot = $config->apiUrl;
	$userid = $_SESSION['userid'];
	$post = $_GET['comment'];
	$colorOfPost = getColorOfPost($apiroot, $post);


	if(!isset($_SESSION['userid'])) {
		die('You need to <a href="login.php">login</a> first');
	}
	if(!isset($_GET['comment'])){
		die('You need to select a post');
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
		$jodel = $_GET['comment'];
		//encode special chars to avoid injection
		$comment = htmlspecialchars($_POST['comment'], ENT_QUOTES);
		//set color as local value
		$color = $_POST['color'];
		//insert new post in DB, $postfields as JSON with all data
		$postfields = "{\n  \"jodlerIDFK\": \"$userid\",\n  \"colorIDFK\": \"$color\",\n  \"jodelIDFK\": \"$jodel\",\n  \"comment\": \"$comment\"\n";
		$callurl = $apiroot . "comments";
		$posted = postCall($callurl, $postfields);
		//update the authors karma for creating a post
		$karma = $karma + $config->karma_calc['post_comment'];
		$postfields = "{\n  \n  \"karma\": $karma\n}";
		$callurl = $apiroot . "jodlers/" . $userid;
		$karmaupdated = putCall($callurl, $postfields);
		//redirect to post overview
		header('Location: https://jodel.domayntec.ch/jodels.php');
	}

?>

<div id="top"></div>

<ul class="nav justify-content-center">
	<li class="nav-item">
		<a class="nav-link" href="comments.php?showcomment=<?php echo $post;?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
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

<form action="?post=1" method="POST">
	<div class="form-group">
		<label for="comment">Enter your message</label>
		<textarea class="form-control" rows="10" name="comment" placeholder="Your post" style="color:white;background-color:#<?php echo $colorOfPost;?>"></textarea>
	</div>
	<!-- save the color in a hidden field -->
	<input type="hidden" name="color" value="<?php echo $colornmb;?>">
	<button type="submit" class="btn btn-warning">Submit</button>
</form>
<!-- end post form -->