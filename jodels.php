<?php

session_start();
//Set default values for head & load it
$title = "Posts | SocialDomayn";
$stylesheet = "jodel.css";
include 'functions/header.php';
//Load API functions
include 'functions/apicalls.php';
$config = include('config.php');
$apiroot = $config->apiUrl;

if(!isset($_SESSION['userid'])) {
 header('Location: https://jodel.domayntec.ch/login.php');
}

//get ID of the user
$userid = $_SESSION['userid'];
//Get data about the user and save it in session values
$callurl = $apiroot . "jodlers?transform=1&filter=jodlerID,eq," . $userid;
$userjson = getCall($callurl);
$user = json_decode($userjson, true);
foreach($user['jodlers'] as $jodler){
	$karma = $jodler['karma'];
	$accstate = $jodler['account_state'];
}
$_SESSION['karma'] = $karma;
$_SESSION['acctype'] = $accstate;

//if joels.php?upvotejodel=$jodelID is called, upvote it
if(isset($_GET['upvotejodel'])){
	$jodel2upvote = $_GET['upvotejodel'];
	//Get the post to upvote and users who voted this post
	$callurl = $apiroot . "jodels?transform=1&filter=jodelID,eq," . $jodel2upvote;
	$jodeljson = getCall($callurl);
	$callurl = $apiroot . "jodelvotes?transform=1&filter=jodelIDFK,eq," . $jodel2upvote;
	$votejson = getCall($callurl);
	$votes = json_decode($votejson,true);
	//Check if ID of the user already voted this post
	foreach($votes['jodelvotes'] as $vote){
		if($vote['userIDFK'] == $userid){
			$voted = true;
		}
	}
	//If user hasn't voted for this post yet
	if(!$voted){
	$jodel = json_decode($jodeljson, true);
	//Get current votes, score and author of the post, add 1 to vote and score
	foreach($jodel['jodels'] as $post){
		$votes = $post['votes_cnt'];
		$score = $post['score'];
		$author = $post['jodlerIDFK'];
		$votes++;
		$score++;
	}
	//Update votes & score of post in DB
	$postfields = "{\n  \n  \"votes_cnt\": $votes,\n  \"score\": $score\n}";
	$callurl = $apiroot . "jodels/" . $jodel2upvote;
	$voted = putCall($callurl,$postfields);

	//Wirte to DB, that this user now voted on this post
	$postfields = "{\n  \n  \"userIDFK\": $userid,\n  \"jodelIDFK\": $jodel2upvote\n}";
	$callurl = $apiroot . "jodelvotes";
	$uservoted = postCall($callurl,$postfields);

	//Get current karma of post author
	$callurl = $apiroot . "jodlers?transform=1&filter=jodlerID,eq," . $author;
	$authorkarmajson = getCall($callurl);
	$authorkarma = json_decode($authorkarmajson, true);
	foreach($authorkarma['jodlers'] as $user){
		$karmaFromAuthor = $user['karma'];
	}

	//incerase karma of the author, update it in DB
	$karmaFromAuthor = $karmaFromAuthor + $config->karma_calc['get_upvote'];
	$postfields = "{\n  \n  \"karma\": $karmaFromAuthor\n}";
	$callurl = $apiroot . "jodlers/" . $author;
	$karmaupdated = putCall($callurl, $postfields);

	//incerase the karma of the voter (current user) and update it in DB
	$karma = $karma + $config->karma_calc['do_upvote'];
	$postfields = "{\n  \n  \"karma\": $karma\n}";
	$callurl = $apiroot . "jodlers/" . $userid;
	$karmaupdated = putCall($callurl, $postfields);

	} else {
		//user has already voted on this post
	$_SESSION['errorMsg'] = "Already voted";
}
//redirect again to jodels.php to show clean URL in browser
header('Location: https://jodel.domayntec.ch/jodels.php');
}

//if jodels.php?downvotejodel=$jodelID ist called, downvote post
if(isset($_GET['downvotejodel'])){
	$jodel2downvote = $_GET['downvotejodel'];
	//get post and users who voted on this post
	$callurl = $apiroot . "jodles?transform=1&filter=jodelID,eq," . $jodel2downvote;
	$jodeljson = getCall($callurl);
	$callurl = $apiroot . "jodelvotes?transform=1&filter=jodelIDFK,eq," . $jodel2downvote;
	$votejson = getCall($callurl);
	$votes = json_decode($votejson,true);
	//check if user already voted on this post
	foreach($votes['jodelvotes'] as $vote){
		if($vote['userIDFK'] == $userid){
			$voted = true;
		}
	}
	if(!$voted){
		//if user has not voted,  get current votes & score, and lower 1.
	$jodel = json_decode($jodeljson, true);
	foreach($jodel['jodels'] as $post){
		$votes = $post['votes_cnt'];
		$score = $post['score'];
		$author = $post['jodlerIDFK'];
		$votes--;
		$score--;
	}
	//Update votes and score in DB
	$postfields = "{\n  \n  \"votes_cnt\": $votes,\n  \"score\": $score\n}";
	$callurl = $apiroot . "jodels/" . $jodel2downvote;
	$voted = putCall($callurl,$postfields);

	//wirte in DB, that user has voted on this post now
	$postfields = "{\n  \n  \"userIDFK\": $userid,\n  \"jodelIDFK\": $jodel2downvote\n}";
	$callurl = $apiroot . "jodelvotes";
	$uservoted = postCall($callurl,$postfields);

	//Get karma from author
	$callurl = $apiroot . "jodlers?transform=1&filter=jodlerID,eq," . $author;
	$authorkarmajson = getCall($callurl);
	$authorkarma = json_decode($authorkarmajson, true);
	foreach($authorkarma['jodlers'] as $user){
		$karmaFromAuthor = $user['karma'];
	}

	//lower karma of author 4, update it DB
	$karmaFromAuthor = $karmaFromAuthor - $config->karma_calc['get_downvote'];;
	$postfields = "{\n  \n  \"karma\": $karmaFromAuthor\n}";
	$callurl = $apiroot . "jodlers/" . $author;
	$karmaupdated = putCall($callurl, $postfields);

	//lower karma of voter (current user) 2, update in DB
	$karma = $karma - $config->karma_calc['do_downvote'];;
	$postfields = "{\n  \n  \"karma\": $karma\n}";
	$callurl = $apiroot . "jodlers/" . $userid;
	$karmaupdated = putCall($callurl, $postfields);

	} else {
	$_SESSION['errorMsg'] = "Already voted";

	}
	header('Location: https://jodel.domayntec.ch/jodels.php');
}

//If jodels.php?sort=$sort is called, post should be sorted
if(isset($_GET['sort'])){
$parameter = $_GET['sort'];
//check how posts should be sorted
switch ($parameter){
	case "latest":
		$sort = "latest";
		break;
	case "hot":
		$sort = "hot";
		break;
	case "popular":
		$sort = "popular";
		break;
	default:
		$sort = "latest";
}

}


?>
<!-- Top / Main Navigation -->
<div id="top"></div>
<ul class="nav justify-content-center">
		<!-- Refresh page -->
		<li class="nav-item">
    <a class="nav-link" href="javascript:window.location.reload();"><i class="fa fa-refresh" aria-hidden="true"></i></a>
  </li>
  	<!-- Latest posts -->
  <li class="nav-item">
    <a class="nav-link <?php if($sort == 'latest' || !isset($sort)){ echo 'active';}?>" href="?sort=latest"><?php echo $config->app_vocabulary['latest'];?></a>
  </li>
  <!-- Most comments -->
  <li class="nav-item">
    <a class="nav-link  <?php if($sort == 'hot'){ echo 'active';}?>" href="?sort=hot"><?php echo $config->app_vocabulary['hotest'];?></a>
  </li>
  <!-- most votes -->
  <li class="nav-item">
    <a class="nav-link  <?php if($sort == 'popular'){ echo 'active';}?>" href="?sort=popular"><?php echo $config->app_vocabulary['popular'];?></a>
  </li>
  <!-- user profile -->
  <li class="nav-item">
    <a class="nav-link" href="user.php"><i class="fa fa-user" aria-hidden="true"></i><?php echo $karma;?></a>
  </li>
</ul>
<!-- must check in stylesheet -->
<div class="test"></div>
<?php
if(isset($_SESSION['errorMsg'])) {
 ?>

 <div class="alert alert-danger" role="alert">
  
  <strong>Holy guacamole!</strong> <?php echo $_SESSION['errorMsg'];?>
</div>
<?php

}
 
//set up url where posts are stored
 $postsUrl = $apiroot . "jodeldata?transform=1";
//setup post filter (as selected in main menu)
 switch ($sort){
	 case "latest":
	 	$filter = "";
		 break;
	 case "hot":
	 	$filter = "&order=comments_cnt,desc";
		 break;
	case "popular":
		$filter = "&order=votes_cnt,desc";
		break;
	default:
		$filter = "";
 }
 
 //setup api call with filter
$jodelsUrl = $postsUrl . $filter;
$posts = getCall($jodelsUrl);
$postdata = json_decode($posts, true);

//process posts
//TODO: I guess this is a performance breaker
foreach($postdata['jodeldata'] as $post){
	for($i=0; $i < count($post['jodelID']); $i++){
		//get numbers of comments on post
		//TODO: take data from field comments_cnt in jodeldata, store them there too.
		$callurl = $apiroot . "comments?transform=1&filter=jodelIDFK,eq," . $post['jodelID'];
		$commentjson = getCall($callurl);
		$comments = json_decode($commentjson, true);
		foreach($comments['comments'] as $comment){
		$comcnt = count($comment['commentID']);
		
		}
		//setup layout
		?><div class="card card-inverse mb-3 text-center" id="<?php echo $post['jodelID'];?>" style="background-color: #<?php echo $post['colorhex'];?>;">
  <div class="card-block">
    <blockquote class="card-blockquote">
		<?php
			if($post['votes_cnt'] < -5){
				//post is downvoted by the community.
				//TODO: set required downvotes to config
				//TODO: Don't display this posts in stream
				echo "This post was voted out by the community";
				?></blockquote>
  </div>
</div><?php
			} else{
				//post isn't downvoted
		 echo $post['jodel'];?>
		 <!-- voting and number of votes -->
		<div class="jodelvotes">
			
				<a href="?upvotejodel=<?php echo $post['jodelID'];?>"<i class="fa fa-angle-up" aria-hidden="true"></i></a><br>
				<?php echo $post['votes_cnt'] . "<br>";?>
				<a href="?downvotejodel=<?php echo $post['jodelID'];?>"<i class="fa fa-angle-down" aria-hidden="true"></i></a>
			</div>
			<div class="clear"></div>
			<!-- end voting and number of votes -->
			
			<!-- post metadata -->
			<div class="jodelmeta">
				<?php
				//get current time
				$now = date('Y-m-d H:i:s');
				//format time
				$now = date_create_from_format('Y-m-d H:i:s', $now);
				//get publish date and time from post
				$postdate = $post['createdate'];
				//format publish date
				$postdate = date_create_from_format('Y-m-d H:i:s', $postdate);
				//get interval
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
				<?php echo " " ;?><a href="comments.php?showcomment=<?php echo $post['jodelID'];?>"><i class="fa fa-comment" aria-hidden="true"></i><?php echo $post['comments_cnt'];?></a>
				<?php if ($post['account_state'] == 4){echo '<i class="adminmark fa fa-check-square" aria-hidden="true"></i>';}?>
			<!-- end post metadata -->
		</blockquote>
  </div> <!-- end post card somewhere here -->
</div><?php
		}
}
	}	

?>
<!-- new post button -->
<div class="newpost"><a href="new.php"><i class="fa fa-plus-circle" aria-hidden="true"></a></i></div>

<?php
//include footer
include 'functions/footer.php';