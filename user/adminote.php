<?php
	session_start();
	//Include functions & meta data
	require '../functions/apicalls.php';
	$config = require('../config.php');
	$title = "Add a notice | SocialDomayn";
	$stylesheet = "jodel.css";
	include '../functions/header.php';
	$mainaction = true;

	//check if user is logged in & has required caps
	$mycaps = $_SESSION['my_caps'];
	if(!isset($_SESSION['userid']) || $mycaps['create_admin_notice'] == false) {
		header('Location: ' . $config->baseUrl . 'user.php');
	}

	//set up working variables
	$userid = $_SESSION['userid'];
	$apiroot = $config->apiUrl;


	if(isset($_GET['post'])){
		$mainaction = false;
		$note = htmlspecialchars($_POST['notefield'], ENT_QUOTES);
		file_put_contents("notice.txt", "");
		file_put_contents("notice.txt", $note);
		
		header('Location: ' . $config->baseUrl . '/user/adminote.php');
	}

	if(isset($_GET['del'])){
		$mainaction = false;
		unlink('notice.txt');
		header('Location: ' . $config->baseURl . '/user/adminote.php');
	}

		if($mainaction == true){
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

	<?php

	if(file_exists('notice.txt')) {
		$hasNote = true;
		$notice = file_get_contents('notice.txt');

		echo 'Current note:';
		?>
		<div class="card card-inverse mb-3 text-center" style="background-color: grey;">
  			<div class="card-block">
    			<blockquote class="card-blockquote">
				<?php
					echo $notice;
				?>
				<div class="jodelvotes">
						<!--delete button -->
							<a href="?del=1"<i class="fa fa-trash-o" aria-hidden="true"></i></a>
					</div>
				</blockquote>
			</div>
		</div>
		<?php


	} else {
		$hasNote = false;
		?>
		<div class="alert alert-warning" role="alert">
 			There isn't a note yet. But you can create a new one.
		</div>
<?php
	}
?>
<form action="?post=1" method="POST">
	<div class="form-group">
    	<label for="notefield">Enter new notice</label>
    	<textarea class="form-control" id="notefield" name="notefield" rows="3"></textarea>
		
  	</div>
	  <?php if ($hasNote == true){
			?>
			<div class="alert alert-warning" role="alert">
				<strong>Warning!</strong> Your current notice will be overwritten!
			</div>
			<?php
		}
		?>
  		<button type="submit" class="btn btn-warning btn-lg btn-block">Submit</button>

</form>


	</div>
<?php
		}
//include footer
include '../functions/footer.php';
	