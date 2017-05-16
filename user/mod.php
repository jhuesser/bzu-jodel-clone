<?php
	session_start();
	//Include functions & meta data
	require '../functions/apicalls.php';
	$config = require('../config.php');
	$title = "Add color | SocialDomayn";
	$stylesheet = "jodel.css";
	include '../functions/header.php';

	//check if user is logged in & has required caps
	if(!isset($_SESSION['userid']) || !isset($_SESSION['caps_add_color'])) {
		header('Location: ' . $config->baseUrl . '/login.php');
	}

	//set up working variables
	$userid = $_SESSION['userid'];
	$apiroot = $config->apiUrl;

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
	?>