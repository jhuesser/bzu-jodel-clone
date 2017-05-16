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
	if(!isset($_SESSION['userid']) || !isset($_SESSION['caps_add_color'])) {
		header('Location: ' . $config->baseUrl . '/login.php');
	}

	//set up working variables
	$userid = $_SESSION['userid'];
	$apiroot = $config->apiUrl;
	$baseurl = $config->baseUrl;


	if(isset($_GET['type']) && isset($_GET['approve']) || isset($_GET['deny']) || isset($_GET['idc'])){
		$type = $_GET['type'];

		if($type == "post"){
			$middle = "jodeldata";
			$filter = "filter=jodelID,eq,";
		} elseif($type = "comments"){
			$middle = "comments";
			$filter = "filter=commentID,eq,";
		}

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

		switch($action){
			case "approve":
				$scorejson = getCall($apiroot . $middle . "?transform=1&" . $filter . $post);
				$scorearray = json_decode($scorejson,true);
				foreach($scorearray[$middle] as $scorehandler){
					$score = $scorehandler['score'];
				}
				$newscore = $score + $config->postmeta['mod_approve'];
				$putfields = "{\n  \"score\": \"$newscore\"\n}";
				if($middle = "jodeldata"){
					$middle = "jodels";
				}
				$approved = putCall($apiroot . $middle . "/" . $post, $putfields);
				header('Location: ' . $baseurl . 'user/mod.php');
			
			break;
			case "deny":
			$scorejson = getCall($apiroot . $middle . "?transform=1&" . $filter . $post);
				$scorearray = json_decode($scorejson,true);
				foreach($scorearray[$middle] as $scorehandler){
					$score = $scorehandler['score'];
				}
				$newscore = $score - $config->postmeta['mod_deny'];
				$putfields = "{\n  \"score\": \"$newscore\"\n}";
				if($middle = "jodeldata"){
					$middle = "jodels";
				}
				$approved = putCall($apiroot . $middle . "/" . $post, $putfields);
				header('Location: ' . $baseurl . 'user/mod.php');

			break;
			case "idc":
			//TODO: handling of "i don't know'.
			
			break;
		}
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
		//get JSON of all colors, save it in PHP array
		$reporturl = $apiroot . "reports?transform=1";
		$reportjson = getCall($reporturl);
		$reports = json_decode($reportjson, true);

		foreach($reports['reports'] as $report){
			if($report['jodelDFK'] != null){
				$type = "post";
				$callurl = $apiroot . "jodeldata?transform=1&filter=jodelID,eq," . $report['jodelDFK'];
			} elseif($report['commentIDFK'] != null){	
				$type = "comment";	
				$callurl = $apiroot . "comments?transform=1&filter=commentID,eq," . $report['commentIDFK'];	
			}
			
			$contentjson = getCall($callurl);
			$contentarray = json_decode($contentjson, true);

			$reasonjson = getCall($apiroot . "abuse?transform=1&filter=abuseID,eq," . $report['abuseIDFK']);
			$reasonarray = json_decode($reasonjson, true);
			foreach($reasonarray['abuse'] as $reasonhandler){
				$reason = $reasonhandler['abusedesc'];
			}

			if($type == "post"){

				foreach($contentarray['jodeldata'] as $post){
				?>

				<div class="reason">
					<?php echo "This post is reported beacause of " . $reason . ".";?>
				</div>
				<div class="card card-inverse mb-3 text-center" id="<?php echo $post['jodelID'];?>" style="background-color: #<?php echo $post['colorhex'];?>;">
  				<div class="card-block">
    				<blockquote class="card-blockquote">
					<?php
							//post isn't downvoted
		 						echo $post['jodel'];?>
		 						<!-- voting and number of votes -->
								<div class="jodelvotes">
									<a href="#"<i class="fa fa-angle-up" aria-hidden="true"></i></a><br>
									<?php echo $post['votes_cnt'] . "<br>";?>
									<a href="#"<i class="fa fa-angle-down" aria-hidden="true"></i></a>
								</div>
								<div class="clear"></div>
								<!-- end voting and number of votes -->
								<!-- post metadata -->
								<div class="jodelmeta">
									<?php
										$timeago = jodelage($post['createdate']);
									?>
									<?php echo " ";?><i class="fa fa-clock-o" aria-hidden="true"></i><span id="<?php echo 'time-' . $post['jodelID'];?>"><?php echo $timeago;?></span>
									<?php echo " " ;?><a href="comments.php?showcomment=<?php echo $post['jodelID'];?>"><i class="fa fa-comment" aria-hidden="true"></i><?php echo $post['comments_cnt'];?></a>
									<?php if ($post['account_state'] == 4){echo '<i class="adminmark fa fa-check-square" aria-hidden="true"></i>';}?>
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



			}}


		}
	?>

	