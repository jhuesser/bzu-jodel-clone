<?php
	session_start();
	include 'functions/jodelmeta.php';
	//Set default values for head & load it
	$title = "Posts | SocialDomayn";
	$stylesheet = "jodel.css";
	include 'functions/header.php';
	//Load API functions
	include 'functions/apicalls.php';
	$config = include('config.php');
	include 'functions/votes.php';
	$apiroot = $config->apiUrl;

	if(!isset($_SESSION['userid'])) {
 		header('Location: ' . $config->baseUrl . 'login.php');

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
		voteJodel($config, $_GET['upvotejodel'], "up");
		
	}

	//if jodels.php?downvotejodel=$jodelID ist called, downvote post
	if(isset($_GET['downvotejodel'])){
		voteJodel($config, $_GET['downvotejodel'], "down");
	
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
			?>
			<div class="card card-inverse mb-3 text-center" id="<?php echo $post['jodelID'];?>" style="background-color: #<?php echo $post['colorhex'];?>;">
  				<div class="card-block">
    				<blockquote class="card-blockquote">
						<?php
							if($post['votes_cnt'] < -5){
							//post is downvoted by the community.
							//TODO: set required downvotes to config
							//TODO: Don't display this posts in stream
							echo "This post was voted out by the community";
						?>
					</blockquote>
  				</div>
			</div>
						<?php
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
										$timeago = jodelage($post['createdate']);
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