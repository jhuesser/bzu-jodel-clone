<?php
	session_start();
	//Set default values for head & load it
	$title = "Login | SocialDomayn";
	$stylesheet = "login.css";
	include 'functions/header.php';
	//Load API functions
	require 'functions/apicalls.php';
	$config = require('config.php');
	$apiroot = $config->apiUrl;

	$mainaction = true;
	//If user sent loginform, set values
	if(isset($_GET['login'])) {
		$mainaction = false;
 		$username = $_POST['username'];
 		$password = $_POST['password'];
 		//check if captcha is solved
 		if(isset($_POST['g-recaptcha-response']))
        	$captcha=$_POST['g-recaptcha-response'];
		if(!$captcha){
			//is not solved
         	$errorMessage = $config->app_msgs['catpcha_not_solved'];
          }
		//check if solved captcha is valid
 		$captachresp =json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$config->recaptcha_secret . "&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
        if($captachresp['success'] == false){
			//captcha is not valid
        	$errorMessage = $config->app_msgs['captcha_fail'];
        } else {
			//check if username is registered
			$caller = $apiroot . "jodlers?transform=1&filter=jodlerHRID,eq," . $username;
			$resp = getCall($caller);
 			if($resp == '{"jodlers":[]}') {
	 			//user is not registered
				$user = false;
 			}
 			$data=json_decode($resp, true);
 			//read password hash from DB
			foreach($data['jodlers'] as $item){
				$passwordDB = $item['passphrase'];
				$userid = $item['jodlerID'];
				$jodlerHRID = $item['jodlerHRID'];
				$accountstate = $item['account_state'];
	
			}
			//check password
			if ($user !== false && $accountstate !== 0 && password_verify($password, $passwordDB)) {
				//Login successfull
 				$_SESSION['userid'] = $userid;
				$_SESSION['username'] = $jodlerHRID;
				header('Location: ' . $config->baseUrl . 'jodels.php');
	 			// echo '<a href="index.php">openindex</a>';
			 } elseif (isset($accountstate) && $accountstate == 0){
				$errorMessage = "User no longer in system.";
			 } 
			 else {
	 			//Login failed
				$errorMessage = $config->app_msgs['login_fail'];
			}
		}
		

	}
	if($mainaction == true){

?>
<a class="forker" target="_blank" href="https://github.com/jhuesser/bzu-jodel-clone"><img class="forker" style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/652c5b9acfaddf3a9c326fa6bde407b87f7be0f4/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_orange_ff7600.png"></a>
<div id="top"></div>
<!-- main menu -->
<ul class="nav ">
	<li class="nav-item">
		<img src="img/domaynW.png" alt="DomaynTec Logo" width="30%">
	</li>
  
</ul>
<!-- end main menu -->
<div class="test"></div>
<?php
	if(isset($errorMessage)) {
			//display login message
			?>
 			<div class="alert alert-danger" role="alert">
  				<strong>Holy guacamole!</strong> <?php echo $errorMessage;?>
			</div>
			<?php
		}
?>
<div class="alert alert-warning" role="alert">
  <strong>Warning!</strong> This is a test environment. Every user that is not approved by the admins / developer gets banned.
</div>
<!-- login form -->
<div class="container">
	<div class="wrapper">
		<form action="?login=1" method="post" name="Login_Form" class="form-signin">       
		    <h3 class="form-signin-heading"><?php echo $config->login_strings['title_login'];?></h3>
			  <hr class="colorgraph"><br>
			  
			  <input type="text" class="form-control" name="username" placeholder="<?php echo $config->login_strings['username'];?>" required="true" autofocus="true" />
			  <input type="password" class="form-control" name="password" placeholder="<?php echo $config->login_strings['paswd'];?>" required="true"/> 
			  <div class="g-recaptcha" data-sitekey="<?php echo $config->recaptcha_sitekey; ?>"></div>    		  
			 
			  <button class="btn btn-lg btn-block"  name="Submit" value="Login" type="Submit"><?php echo $config->login_strings['login'];?></button>  			
		</form>
		<!-- link to sign up page -->
		<p><?php echo $config->login_strings['create_acc']; ?></p>			
	</div>
</div>

<!-- end login form -->
<?php
include 'functions/footer.php';
	}