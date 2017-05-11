<?php

session_start();
//Set default values for head & load it
$title = "Comments | SocialDomayn";
$stylesheet = "jodel.css";
include 'functions/header.php';
//Load API functions
include 'functions/apicalls.php';
include 'functions/jodelmeta.php';
$config = include('config.php');
include 'functions/votes.php';
$apiroot = $config->apiUrl;
$baseurl = $config->baseUrl;

//check if user is logged in. if not, redirect to login page
if(!isset($_SESSION['userid'])) {
 header('Location: ' . $config->baseUrl . 'login.php');
}

//qurey the ID of the user
$userid = $_SESSION['userid'];
//Get data about the user & save it in $_SESSION values.
$userjson = getCall( $apiroot . "jodlers?transform=1&filter=jodlerID,eq,$userid");
$user = json_decode($userjson, true);
foreach($user['jodlers'] as $jodler){
	$karma = $jodler['karma'];
	$accstate = $jodler['account_state'];	
}
$_SESSION['karma'] = $karma;
$_SESSION['acctype'] = $accstate;

if(isset($_GET['showcomment'])){
	$postID = $_GET['showcomment'];


} else {
	die("You need to select a post");
}

if(isset($_GET['upvotecomment'])){
	voteComment( $_GET['upvotecomment'], "up");
	
}


if(isset($_GET['downvotecomment'])){
	voteComment( $_GET['downvotecomment'], "down");
}

if(isset($_GET['upvotejodel'])){
	voteJodel( $_GET['upvotejodel'], "up");
}

if(isset($_GET['downvotejodel'])){
	voteJodel( $_GET['downvotejodel'], "down");
}


?>

<div id="top"></div>
<ul class="nav justify-content-center">
		<li class="nav-item">
			<a class="nav-link" href="jodels.php#<?php echo $postID; ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
		</li>
		<li class="nav-item">
    <a class="nav-link" href="javascript:window.location.reload();"><i class="fa fa-refresh" aria-hidden="true"></i></a>
  </li>
  
  <li class="nav-item">
    <a class="nav-link" href="user.php"><i class="fa fa-user" aria-hidden="true"></i><?php echo $karma;?></a>
  </li>
</ul>
<div class="test"></div>
<?php
if(isset($_SESSION['errorMsg'])) {
 ?>

 <div class="alert alert-danger" role="alert">
  
  <strong>Holy guacamole!</strong> <?php echo $_SESSION['errorMsg'];?>
</div>
<?php

}
$jodelUrl = $apiroot . "jodels?transform=1";
$commentsUrl = $apiroot . "comments?transform=1";
$filter = "&filter=jodelIDFK,eq,$postID";

$caller = $apiroot . "jodeldata?transform=1&filter=jodelID,eq," . $postID;
$jodeljson = getCall($caller);
		$jodels = json_decode($jodeljson,true);
		foreach($jodels['jodeldata'] as $jodel) {
			$colorhex = $jodel['colorhex'];
			$jodelauthor = $jodel['jodlerIDFK'];
			?>
			
			<div class="card card-inverse mb-3 text-center" style="background-color: #<?php echo $colorhex;?>;">
  <div class="card-block">
    <blockquote class="card-blockquote">
<?php echo $jodel['jodel'];?>

<div class="jodelvotes">
			
				<a href="?showcomment=<?php echo $jodel['jodelID'];?>&upvotejodel=<?php echo $jodel['jodelID'];?>"<i class="fa fa-angle-up" aria-hidden="true"></i></a><br>
				<?php echo $jodel['votes_cnt'] . "<br>";?>
				<a href="?showcomment=<?php echo $jodel['jodelID'];?>&downvotejodel=<?php echo $jodel['jodelID'];?>"<i class="fa fa-angle-down" aria-hidden="true"></i></a>
			</div>
			<div class="jodelmeta">
				<?php
				$timeago = jodelage($jodel['createdate']);
			
				?>
				<?php echo " ";?><i class="fa fa-clock-o" aria-hidden="true"></i><?php echo $timeago;?>
				<a href="report.php?type=post&id=<?php echo $jodel['jodelID'];?>"><i class="fa fa-flag" aria-hidden="true"></i></a>
				<?php if ($jodel['account_state'] == 4){echo '<i class="adminmark fa user-circle" aria-hidden="true"></i>';}?>

</blockquote>
  </div>
</div><?php
		}


$comments = getCall($commentsUrl . $filter);

$postdata = json_decode($comments, true);
foreach($postdata['comments'] as $comment){
	for($i=0; $i < count($comment['commentID']); $i++){
		$jodelID = $comment['jodelIDFK'];
		
		$authorurl = $apiroot . "jodlers?transform=1&filter=jodlerID,eq," . $comment['jodlerIDFK'];
		$authorjson = getCall($authorurl);
		$author = json_decode($authorjson, true);
		foreach($author['jodlers'] as $user){
			$accstate = $user['account_state'];
		}



		?><div class="card card-inverse mb-3 text-center" id="<?php echo $comment['commentID'];?>" style="background-color: #<?php echo $colorhex;?>;">
  <div class="card-block">
    <blockquote class="card-blockquote">
		<?php
			if($comment['votes_cnt'] < -5){
				echo "This post was voted out by the community";
				?></blockquote>
  </div>
</div><?php
			} else{
		 echo $comment['comment'];?>
		<div class="jodelvotes">
			
				<a href="?showcomment=<?php echo $postID; ?>&upvotecomment=<?php echo $comment['commentID'];?>"<i class="fa fa-angle-up" aria-hidden="true"></i></a><br>
				<?php echo $comment['votes_cnt'] . "<br>";?>
				<a href="?showcomment=<?php echo $postID; ?>&downvotecomment=<?php echo $comment['commentID'];?>"<i class="fa fa-angle-down" aria-hidden="true"></i></a>
			</div>
			<div class="jodelmeta">
				<?php
			
				$timeago = jodelage($comment['timestamp']);
			
				?>
				<?php echo " ";?><i class="fa fa-clock-o" aria-hidden="true"></i><?php echo $timeago;?>
				<a href="report.php?type=comment&id=<?php echo $comment['commentID'];?>"><i class="fa fa-flag" aria-hidden="true"></i></a>
				<?php if ($accstate == 4){echo '<i class="adminmark fa fa-check-square" aria-hidden="true"></i>';}?>
				<?php if ($jodelauthor == $comment['jodlerIDFK']){echo '<i class="fa fa-trophy" aria-hidden="true"></i> OP';}?>
				
			
		</blockquote>
  </div>
</div><?php



	}
}
}
?>

<div class="newcomment"><a href="newcomment.php?comment=<?php echo $postID;?>">New comment</a></i></div>


