<?php
	session_start();
	//Include functions & meta data
	require '../functions/apicalls.php';
	$config = require('../config.php');
	$apiroot = $config->apiUrl;
	require '../functions/jodelmeta.php';
	$title = "Reset Password | SocialDomayn";
	$stylesheet = "jodel.css";
	include '../functions/header.php';

	//check if user is logged in & has required caps
	$mycaps = $_SESSION['my_caps'];
	if(!isset($_SESSION['userid']) || $mycaps['reset_paswd'] == false) {
		header('Location: ' . $config->baseUrl . 'user.php');
	}

	//set up working variables
	$userid = $_SESSION['userid'];
	

	if (isset($_GET['resetpasswd'])){
		$user2reset = $_POST['user'];
		$newpasswd = $_POST['passwd'];
		$password_hash = password_hash($newpasswd, PASSWORD_DEFAULT);
		$postfields = "{\n  \"passphrase\": \"$password_hash\"\n}";
		$callurl = $apiroot . "jodlers/" . $user2reset;
		putCall($callurl, $postfields);
		header('Location: ' . $config->baseUrl . 'user/resetpasswd.php');
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
	
		$jodlersurl = $apiroot . "jodlers?transform=1";
		$jodlersjson = getCall($jodlersurl);
		$jodlers = json_decode($jodlersjson, true);

		foreach($jodlers['jodlers'] as $jodler){
			$colors = getRandomColor();
			$color = $colors['colorhex'];
			//show all colors
			?><div class="card card-inverse mb-3 text-center" id="<?php echo $jodler['jodlerID'];?>" style="background-color: #<?php echo $color;?>;">
  		<div class="card-block">
    		<blockquote class="card-blockquote">
					<?php echo $jodler['jodlerHRID'] . "\n" . $jodler['jodlerID'];
					?>
					<div class="jodelvotes">
						<!--delete button -->
						<form action="?resetpasswd=1" method="POST">
							<div class="form-group row">
  								<label for="passwd-text-input" class="col-2 col-form-label">New password</label>
  								<div class="col-10">
    								<input class="form-control" type="password" placeholder="New password" name="passwd" id="passwd-text-input">
  								</div>
								  <input type="hidden" name="user" value="<?php echo $jodler['jodlerID']; ?>">
							</div>
							<button type="submit" class="btn btn-warning">Reset</button>
						</form>
					</div>
					<div class="clear"></div>
				</blockquote>
		</div> 
	</div>
	<?php
		}
		//include footer
		include '../functions/footer.php';
		