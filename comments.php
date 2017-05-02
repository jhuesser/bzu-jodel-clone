<?php

session_start();
//Set default values for head & load it
$title = "Posts | SocialDomayn";
$stylesheet = "jodel.css";
include 'functions/header.php';
//Load API functions
include 'functions/apicalls.php';
$config = include('config.php');
include 'functions/votes.php';
$apiroot = $config->apiUrl;

//check if user is logged in. if not, redirect to login page
if(!isset($_SESSION['userid'])) {
 header('Location: https://jodel.domayntec.ch/login.php');
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
$_SESSION['karma'] = $karma;
$_SESSION['acctype'] = $accstate;

if(isset($_GET['showcomment'])){
	$postID = $_GET['showcomment'];


} else {
	die("You need to select a post");
}

if(isset($_GET['upvotecomment'])){
	voteComment($config, $_GET['upvotecomment'], "up");
	
}


if(isset($_GET['downvotecomment'])){
	voteComment($config, $_GET['downvotecomment'], "down");
}

if(isset($_GET['upvotejodel'])){
	voteJodel($config, $_GET['upvotejodel'], "up");
}

if(isset($_GET['downvotejodel'])){
	voteComment($config, $_GET['downvotejodel'], "down");
}


?>
<div id="top"></div>
<ul class="nav justify-content-center">
		<li class="nav-item">
			<a class="nav-link" href="jodels.php#<?php echo $postID; ?>">Back (replace with a fancy icon)</a>
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
$jodelUrl = "https://jodel.domayntec.ch/api.php/jodels?transform=1";
$commentsUrl = "https://jodel.domayntec.ch/api.php/comments?transform=1";
$filter = "&filter=jodelIDFK,eq,$postID";


$jodeljson = getCall("https://jodel.domayntec.ch/api.php/jodeldata?transform=1&filter=jodelID,eq,$postID");
		$jodels = json_decode($jodeljson,true);
		foreach($jodels['jodeldata'] as $jodel) {
			$colorhex = $jodel['colorhex'];?>
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
				$now = date('Y-m-d H:i:s');
				
				$now = date_create_from_format('Y-m-d H:i:s', $now);
				$postdate = $jodel['createdate'];
				$postdate = date_create_from_format('Y-m-d H:i:s', $postdate);
				$interval = date_diff($postdate, $now);
				//years
				$timeago = $interval->format('%y');
				if ($timeago == 0){
					//months
					$timeago = $interval->format('%m');
					if ($timeago == 0){
						//days
						$timeago = $interval->format('%a');
						if ($timeago == 0){
							//hours
							$timeago = $interval->format('%h');
							if ($timeago == 0){
								//minutes
								$timeago = $interval->format('%i');
								if ($timeago == 0){
									//seconds
									$timeago = $interval->format('%s');
									if ($timeago == 0){
										$timeago = "just now";
									} else {
										$timeago = $timeago . " s";
									}
								} else {
									$timeago = $timeago . " m";
								}
							} else {
								$timeago = $timeago . " h";
							}
						} else {
							$timeago = $timeago . " d";
						}
					} else {
						$timeago = $timeago . " M";
					}
				} else {
					$timeago = $timeago . " Y";
				}
			
				?>
				<?php echo " ";?><i class="fa fa-clock-o" aria-hidden="true"></i><?php echo $timeago;?>
				<?php echo " " ;?><i class="fa fa-comment" aria-hidden="true"></i><?php echo $jodel['comments_cnt'];?>
				<?php if ($jodel['account_state'] == 4){echo '<i class="adminmark fa fa-check-square" aria-hidden="true"></i>';}?>

</blockquote>
  </div>
</div><?php
		}


$comments = getCall($commentsUrl . $filter);

$postdata = json_decode($comments, true);
foreach($postdata['comments'] as $comment){
	for($i=0; $i < count($comment['commentID']); $i++){
		$jodelID = $comment['jodelIDFK'];
		
		


		?><div class="card card-inverse mb-3 text-center" style="background-color: #<?php echo $colorhex;?>;">
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
				$now = date('Y-m-d H:i:s');
				
				$now = date_create_from_format('Y-m-d H:i:s', $now);
				$postdate = $comment['timestamp'];
				$postdate = date_create_from_format('Y-m-d H:i:s', $postdate);
				$interval = date_diff($postdate, $now);
				//years
				$timeago = $interval->format('%y');
				if ($timeago == 0){
					//months
					$timeago = $interval->format('%m');
					if ($timeago == 0){
						//days
						$timeago = $interval->format('%a');
						if ($timeago == 0){
							//hours
							$timeago = $interval->format('%h');
							if ($timeago == 0){
								//minutes
								$timeago = $interval->format('%i');
								if ($timeago == 0){
									//seconds
									$timeago = $interval->format('%s');
									if ($timeago == 0){
										$timeago = "just now";
									} else {
										$timeago = $timeago . " s";
									}
								} else {
									$timeago = $timeago . " m";
								}
							} else {
								$timeago = $timeago . " h";
							}
						} else {
							$timeago = $timeago . " d";
						}
					} else {
						$timeago = $timeago . " M";
					}
				} else {
					$timeago = $timeago . " Y";
				}
			
				?>
				<?php echo " ";?><i class="fa fa-clock-o" aria-hidden="true"></i><?php echo $timeago;?>
				<?php echo " " ;?><i class="fa fa-comment" aria-hidden="true"></i><?php echo $post['comments_cnt'];?>
				<?php if ($comment['account_state'] == 4){echo '<i class="adminmark fa fa-check-square" aria-hidden="true"></i>';}?>
			
		</blockquote>
  </div>
</div><?php



	}
}
}