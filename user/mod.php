<?php
	session_start();
	//Include functions & meta data
	require '../functions/apicalls.php';
	require '../functions/jodelmeta.php';
	$config = require('../config.php');
	$title = "Moderation | SocialDomayn";
	$stylesheet = "jodel.css";
	include '../functions/header.php';

	//check if user is logged in & has required caps
	$mycaps = $_SESSION['my_caps'];
	if(!isset($_SESSION['userid']) || $mycaps['mod_posts' == false]) {
		header('Location: ' . $config->baseUrl . 'user.php');
	}

	//set up working variables
	$userid = $_SESSION['userid'];
	$apiroot = $config->apiUrl;
	$baseurl = $config->baseUrl;

	//if content is moderated
	if(isset($_GET['type']) && isset($_GET['approve']) || isset($_GET['deny']) || isset($_GET['idc'])){
		//get type of the content
		$type = $_GET['type'];

		//set up parts of API URL and filter, depens on post type
		if($type == "post"){
			$middle = "jodeldata";
			$filter = "filter=jodelID,eq,";
		} elseif($type = "comments"){
			$middle = "comments";
			$filter = "filter=commentID,eq,";
		}
		//get operation to perform & content ID
		if(isset($_GET['approve'])){
			$action = "approve";
			$post = $_GET['approve'];
		}
		if(isset($_GET['deny'])){
			$action = "deny";
			$post = $_GET['deny'];

		}
		if(isset($_GET['idc'])){
			$action = "idc";
			$post = $_GET['idc'];

		}

		//get score of content
		$scorejson = getCall($apiroot . $middle . "?transform=1&" . $filter . $post);
		$scorearray = json_decode($scorejson,true);
		foreach($scorearray[$middle] as $scorehandler){
			$score = $scorehandler['score'];
		}

		//execute action
		switch($action){
			//content is approved
			case "approve":
				//calclate new score
				$newscore = $score + $config->postmeta['mod_approve'];
				$putfields = "{\n  \"score\": \"$newscore\"\n}";
				//change API URLe from view to table (for POST calls)
				if($middle = "jodeldata"){
					$middle = "jodels";
				}
				//register new score & mod report to DB
				$approved = putCall($apiroot . $middle . "/" . $post, $putfields);
				$postfields = "{\n  \"jodlerIDFK\": \"$userid\",\n  \"jodelIDFK\": \"$post\"\n}";
				$moded = postCall($apiroot . "moderated", $postfields);
				break;
			case "deny":
				//deny content
				//calculate new score
				$newscore = $score - $config->postmeta['mod_deny'];
				$putfields = "{\n  \"score\": \"$newscore\"\n}";
				//switch view --> table
				if($middle = "jodeldata"){
					$middle = "jodels";
				}
				//save to DB
				$denied = putCall($apiroot . $middle . "/" . $post, $putfields);
				$postfields = "{\n  \"jodlerIDFK\": \"$userid\",\n  \"jodelIDFK\": \"$post\"\n}";
				$moded = postCall($apiroot . "moderated", $postfields);

				

			break;
			case "idc":
				//mod doesn't know what to do, just save modreport in DB
				$postfields = "{\n  \"jodlerIDFK\": \"$userid\",\n  \"jodelIDFK\": \"$post\"\n}";
				$moded = postCall($apiroot . "moderated", $postfields);
				break;
		}
		//if score has changed
		if(isset($newscore)){
			//if new score is equal or below required minimum, delete it
			if($newscore <= $config->postmeta['post_deleted_score']){
				$deleted = deleteCall($apiroot. $middle . "/" . $post);
			} elseif($newscore >= $config->postmeta['post_approved_score']){
				//if new score is equal or above required maximum
				//get all reports of this post
				$reportsOfPost = getCall($apiroot . "/reports?transform=1&filter=jodelDFK,eq," . $post);
				$reportsOfPostArray = json_decode($reportsOfPost, true);
				foreach($reportsOfPostArray['reports'] as $report){
					//delete every report of this post
					$deleted = deleteCall($apiroot . "reports/" . $report['reportID']);
				}
			}
		}
		header('Location: ' . $baseurl . 'user/mod.php');
	}

?>
<div id="top"></div>
<!-- main menu -->
<ul class="nav justify-content-center">
	<li class="nav-item">
		<a class="nav-link" href="../user.php"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
	</li>
	<li class="nav-item">
    <a class="nav-link" href="javascript:window.location.reload();"><i class="fa fa-refresh" aria-hidden="true"></i></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="../jodels.php"><i class="fa fa-comments-o" aria-hidden="true"></i></a>
  </li>
  
</ul>
<!-- end main menu -->
<div class="test"></div>
<?php
	if(isset($_SESSION['errorMsg'])) {
		//show error msg
 		?>
		<div class="alert alert-danger" role="alert">
  		<strong>Holy guacamole!</strong> <?php echo $_SESSION['errorMsg'];?>
		</div>
		<?php
	}
	?>
	<div class="container">
		<h1>
			<?php echo "Hello " . $_SESSION['username'];?>
		</h1>
	</div>
	<?php
		//get JSON of all reports, save it in PHP array
		$reporturl = $apiroot . "reports?transform=1";
		$reportjson = getCall($reporturl);
		$reports = json_decode($reportjson, true);

		//get all posts which this mod already voted on
		$jodeljson = getCall($apiroot . "moderated?transform=1&filter=jodlerIDFK,eq," . $userid);
		$jodelarray = json_decode($jodeljson, true);
		$modposts = array();
		foreach($jodelarray['moderated'] as $moded){
			array_push($modposts, $moded['jodelIDFK']);
		}
		//get all comments this mod has already voted on
		$commentjson = getCall($apiroot . "moderated?transform=1&filter=commentIDFK,eq," . $userid);
		$commentarray = json_decode($commentjson, true);
		$modcom = array();
		foreach($commentarray['moderated'] as $moded){
			array_push($modcom, $moded['commentIDFK']);
		}

		//prepare to list reported comments
		foreach($reports['reports'] as $report){
			if($report['jodelDFK'] != null){
				//content is a post. get it.
				$type = "post";
				$callurl = $apiroot . "jodeldata?transform=1&filter=jodelID,eq," . $report['jodelDFK'];
			} elseif($report['commentIDFK'] != null){	
				//content is comment. get it.
				$type = "comment";	
				$callurl = $apiroot . "comments?transform=1&filter=commentID,eq," . $report['commentIDFK'];	
			}
			//actually here you get the content
			$contentjson = getCall($callurl);
			$contentarray = json_decode($contentjson, true);
			//get reason why content is reportet
			$reasonjson = getCall($apiroot . "abuse?transform=1&filter=abuseID,eq," . $report['abuseIDFK']);
			$reasonarray = json_decode($reasonjson, true);
			foreach($reasonarray['abuse'] as $reasonhandler){
				$reason = $reasonhandler['abusedesc'];
			}

			if($type == "post"){
				//print the post
				foreach($contentarray['jodeldata'] as $post){
					//but only if this mod didn't vote already
					if(!in_array($post['jodelID'], $modposts)){
						?>
						<div class="reason">
							<?php echo "This post is reported beacause of " . $reason . ".";?>
						</div>
						<div class="card card-inverse mb-3 text-center" id="<?php echo $post['jodelID'];?>" style="background-color: #<?php echo $post['colorhex'];?>;">
  							<div class="card-block">
    							<blockquote class="card-blockquote">
									<?php
									echo $post['jodel'];?>
		 							<!-- number of votes -->
									<div class="jodelvotes">
										<a href="#"<i class="fa fa-angle-up" aria-hidden="true"></i></a><br>
										<?php echo $post['votes_cnt'] . "<br>";?>
										<a href="#"<i class="fa fa-angle-down" aria-hidden="true"></i></a>
									</div>
									<div class="clear"></div>
									<!-- end number of votes -->
									<!-- post metadata -->
									<div class="jodelmeta">
										<?php
											$timeago = jodelage($post['createdate']);
										?>
										<?php echo " ";?><i class="fa fa-clock-o" aria-hidden="true"></i><span id="<?php echo 'time-' . $post['jodelID'];?>"><?php echo $timeago;?></span>
										<?php echo " " ;?><a href="comments.php?showcomment=<?php echo $post['jodelID'];?>"><i class="fa fa-comment" aria-hidden="true"></i><?php echo $post['comments_cnt'];?></a>
										<?php if ($post['account_state'] == 4){echo '<i class="adminmark fa fa-check-square" aria-hidden="true"></i>';}?>
									</div>
									<!-- end post metadata -->
								</blockquote>
  							</div> <!-- end post card somewhere here -->
						</div>
						<div class="mod-buttons">
							<a href="?deny=<?php echo $post['jodelID'];?>&type=<?php echo $type;?>"><i class="fa fa-times-circle mod-deny" aria-hidden="true"></i></a>
							<a href="?idc=<?php echo $post['jodelID'];?>&type=<?php echo $type;?>"><i class="fa fa-dot-circle-o mod-idc" aria-hidden="true"></i></a>
							<a href="?approve=<?php echo $post['jodelID'];?>&type=<?php echo $type;?>"><i class="fa fa-check-circle mod-approve" aria-hidden="true"></i></a>	
						</div>

					<?php
				
					} //not voted yet

					
					
			} //print post
			
			} elseif($type == "comment") {
			
			}


		}
	?>

	