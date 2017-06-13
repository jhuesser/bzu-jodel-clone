<?php
	session_start();
	//Include functions & meta data
	require '../functions/apicalls.php';
	require '../functions/jodelmeta.php';
	$config = require('../config.php');
	$title = "Postmanagement | SocialDomayn";
	$stylesheet = "jodel.css";
	include '../functions/header.php';
	$mainaction = true;

	//check if user is logged in & has required caps
	$mycaps = $_SESSION['my_caps'];
	if(!isset($_SESSION['userid']) || $mycaps['edit_posts'] == false) {
		header('Location: ' . $config->baseUrl . 'user.php');
	}

	//set up working variables
	$userid = $_SESSION['userid'];
	$myusername = $_SESSION['username'];
	$apiroot = $config->apiUrl;
	$baseurl = $config->baseUrl;

	//get infos about current user
	$callurl = $apiroot . "jodlers?transform=1&filter=jodlerID,eq," . $userid;
	$userjson = getCall($callurl);
	$user = json_decode($userjson, true);
	foreach($user['jodlers'] as $jodler){
		$karma = $jodler['karma'];
		$accstate = $jodler['account_state'];
	}
	$_SESSION['karma'] = $karma;
	$_SESSION['acctype'] = $accstate;

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
//delete a post
if(isset($_GET['del'])){
	$mainaction = false;
	//get post to delete and setup delete URL for API, call it, redirect back
	$post2del = $_GET['del'];
	deletePost($post2del);
	header('Location: ' . $baseurl . 'user/postmgmt.php');
}

//update a post
if(isset($_GET['update'])){
	$mainaction = false;
	//get all values and do not allow injections
	$postid = $_POST['jodelID'];
	$author =  htmlspecialchars($_POST['author'], ENT_QUOTES);
	$score =  htmlspecialchars($_POST['score'], ENT_QUOTES); 
	$votes =   htmlspecialchars($_POST['votes'], ENT_QUOTES); 
	$jodel =   htmlspecialchars($_POST['jodel'], ENT_QUOTES); 
	$color =  htmlspecialchars( $_POST['color'], ENT_QUOTES);
	
	//setup call URL to update & post fields (JSON)
	$callurl = $apiroot . "jodels/" . $postid;
	$postfields = "{\n  \"jodlerIDFK\": \"$author\",\n  \"colorIDFK\": \"$color\",\n  \"jodel\": \"$jodel\",\n  \"score\": \"$score\",\n  \"votes_cnt\": \"$votes\"\n}";
	//update the post
	$updated = putCall($callurl, $postfields);
	//redirect back
	header('Location: ' . $baseurl. 'user/postmgmt.php');

}


	if($mainaction == true){

	?>
	<div class="container">
		<h1>
			<?php echo "Hello " . $_SESSION['username'];?>
		</h1>
	</div>

	<?php
	//get all posts and store them in an arry
	$jodelsUrl = $apiroot . "jodeldata?transform=1";
	$posts = getCall($jodelsUrl);
	$postdata = json_decode($posts, true);

	//get all users and store them in an array
	$usersurl = $apiroot . "jodlers?transform=1";
	$usersjson = getCall($usersurl);
	$users = json_decode($usersjson, true);
	//setup array for dropdown menu later
	$authors = array();
	foreach($users['jodlers'] as $user){
		//Save the username with its value in an array
		$authors[$user['jodlerID']] = $user['jodlerHRID'];
	}
	//do the same with the colors
	$colorurl = $apiroot . "colors?transform=1";
	$colorjson = getCall($colorurl);
	$colorsarray = json_decode($colorjson, true);
	$colors = array();
	foreach($colorsarray['colors'] as $color){
		$colors[$color['colorID']] = $color['colordesc'];
	}


	//process posts
	foreach($postdata['jodeldata'] as $post){
		//get author of the post
		$authorjson = getCall($usersurl . "&filter=jodlerID,eq," . $post['jodlerIDFK']);
		$authorOfPost = json_decode($authorjson, true);
		foreach($authorOfPost['jodlers'] as $author){
			$authorname = $author['jodlerHRID'];
			
		}
		//get colorID of the post
		$colorPrepare = getColorOfPost($post['jodelID']);
		$colorKey = $colorPrepare->colid;
		?>

		<!-- post form -->
			<div class="card card-inverse mb-3 text-center" id="<?php echo $post['jodelID'];?>" style="background-color: #<?php echo $post['colorhex'];?>;">
  				<div class="card-block">
    				<blockquote class="card-blockquote">
						<form action="?update=1" method="POST">
							<div class="form-group">
								<label for="postid">Post ID</label>
								<input type="text" class="form-control" name="jodelID" id="postid"  value="<?php echo $post['jodelID'];?>" readonly>
							</div>
							<div class="form-group">
								<label for="authorField">Author</label>
								<select class="form-control" id="authorField" name="author">
									<!-- first option is current author -->
									<option value="<?php echo $post['jodlerIDFK'];?>"><?php echo $authorname;?></option>
									<?php 
									foreach($authors as $authorID => $username){
										//don't show post author again
										if($post['jodlerIDFK'] != $authorID){
											echo '<option value="' . $authorID . '">' . $username . '</option>';
										}
									}
									?>
								</select>
							</div>
							<div class="form-group">
								<label for="jodel">Edit post</label>
								<textarea class="form-control" rows="5" name="jodel" placeholder="Your post" style="color:white;background-color:#<?php echo $post['colorhex'];?>"><?php echo $post['jodel'];?></textarea>
							</div>
							<div class="form-group">
								<label for="votes">Edit Votes</label>
						    	<input type="text" class="form-control" name="votes" id="votes"  value="<?php echo $post['votes_cnt'];?>">
							</div>
							<div class="form-group">
								<label for="score">Edit score</label>
						    	<input type="text" class="form-control" name="score" id="score"  value="<?php echo $post['score'];?>">
							</div>
							<div class="form-group">
								<label for="color">Change color</label>
								<select class="form-control" name="color" id="color">
									<option value="<?php echo $colorKey;?>"><?php echo $post['colordesc'] . '---' . $post['colorhex'];?></option>
									<?php 
									foreach($colors as $colorID => $colordesc){
										//same with colors
										if($colorKey != $colorID){
											echo '<option value="' . $colorID . '">' . $colordesc . '</option>';
										}
									}
									?>
								</select>
							</div>
							<button type="submit" class="btn btn-warning">Submit</button>
						</form>
						<a href="?del=<?php echo $post['jodelID'];?>"><button type="button" class="btn btn-warning">Delete</button></a>
								<!-- end post metadata -->
					</blockquote>
  				</div> <!-- end post card somewhere here -->
			</div><?php

	}}