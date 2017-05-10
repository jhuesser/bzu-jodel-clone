<?php
	session_start();
	//Include functions & meta data
	include '../functions/apicalls.php';
	$config = include('../config.php');
	$apiroot = $config->apiUrl;
	include '../functions/jodelmeta.php';
	include '../functions/admintools.php';
	include '../functions/usermanipulation.php';
	$title = "Manage Users | SocialDomayn";
	$stylesheet = "jodel.css";
	include '../functions/header.php';

	//check if user is logged in & has required caps
	if(!isset($_SESSION['userid']) || !isset($_SESSION['caps_reset_paswd'])) {
		header('Location: ' . $config->baseUrl . 'https://jodel.domayntec.ch/login.php');
	}

	//set up working variables
	$userid = $_SESSION['userid'];
	$mycaps = $_SESSION['my_caps'];
	
	

	
	
	if(isset($_GET['ban'])){
		$updated = manipulateUser($_GET['ban'], 0, $mycaps, $apiroot);
		header('Location: ' . $config->baseUrl . 'user/usermgmt.php');
	}
	if(isset($_GET['active'])){
		$updated = manipulateUser($_GET['active'], 1, $mycaps, $apiroot);
		header('Location: ' . $config->baseUrl . 'user/usermgmt.php');
	}
	if(isset($_GET['mod'])){
		$updated = manipulateUser($_GET['mod'], 2, $mycaps, $apiroot);
		header('Location: ' . $config->baseUrl . 'user/usermgmt.php');
	}
	if(isset($_GET['admin'])){
		$updated = manipulateUser($_GET['admin'], 3, $mycaps, $apiroot);
		header('Location: ' . $config->baseUrl . 'user/usermgmt.php');
	}
	if(isset($_GET['superadmin'])){
		$updated = manipulateUser($_GET['superadmin'], 4, $mycaps, $apiroot);
		header('Location: ' . $config->baseUrl . 'user/usermgmt.php');
	}
	if(isset($updated)){
		if($updated == false){
			$_SESSION['errorMsg'] = "Something went wrong!";
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
	$jodlersurl = $apiroot . "jodlers?transform=1";
		$jodlersjson = getCall($jodlersurl);
		$jodlers = json_decode($jodlersjson, true);

		foreach($jodlers['jodlers'] as $jodler){
			$color = getRandomColor($apiroot);
			$acctype = getAccountType($config, $jodler['account_state']);
			//show all colors
			?><div class="card card-inverse mb-3 text-center" id="<?php echo $jodler['jodlerID'];?>" style="background-color: #<?php echo $color;?>;">
  		<div class="card-block">
    		<blockquote class="card-blockquote">
					<?php echo $jodler['jodlerID'] . "<br>" . $jodler['jodlerHRID'] . "<br>" . $acctype->typedesc . "<br>";
					if ($mycaps['ban'] == true){
						?><a href="?ban=<?php echo $jodler['jodlerID'];?>"><button type="button" class="btn btn-warning"><?php echo $config->app_vocabulary['baned'] ?></button></a><?php
					}
					if ($mycaps['promote_to_user'] == true){
						?><a href="?active=<?php echo $jodler['jodlerID'];?>"><button type="button" class="btn btn-warning"><?php echo $config->app_vocabulary['jodler'] ?></button></a><?php
					}
					if ($mycaps['promote_to_mod'] == true){
						?><a href="?mod=<?php echo $jodler['jodlerID'];?>"><button type="button" class="btn btn-warning"><?php echo $config->app_vocabulary['mod'] ?></button></a><?php
					}
					if ($mycaps['promote_to_admin'] == true){
						?><a href="?admin=<?php echo $jodler['jodlerID'];?>"><button type="button" class="btn btn-warning"><?php echo $config->app_vocabulary['admin'] ?></button></a><?php
					}
					if ($mycaps['promote_to_superadmin'] == true){
						?><a href="?superadmin=<?php echo $jodler['jodlerID'];?>"><button type="button" class="btn btn-warning"><?php echo $config->app_vocabulary['superadmin'] ?></button></a><?php
					}
						?>			
					
				</blockquote>
		</div> 
	</div>
	<?php
		}
		//include footer
		include '../functions/footer.php';
		