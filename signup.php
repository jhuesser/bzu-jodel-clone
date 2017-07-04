<?php

	session_start();
	//Load functions, config and pass metadata
	$title = "Sign up | SocialDomayn";
	$stylesheet = "login.css";
	include 'functions/header.php';
	require 'functions/apicalls.php';
	$config = require('config.php');
	$apiroot = $config->apiUrl;

	$mainaction = true;

	if(isset($_GET['register'])) {
		$mainaction = false;
		//User wants to register
		//set local values for easy handling
 		$error = false;
		$username = $_POST['username'];
 		$password = $_POST['password1'];
 		$password2 = $_POST['password2'];
		if(isset($_POST['g-recaptcha-response']))
			//captcha is solved
          	$captcha=$_POST['g-recaptcha-response'];
			if(!$captcha){
				//captcha is not solved
         	 	$errorMsg = $config->app_msgs['catpcha_not_solved'];
          
        	}
			$captachresp =json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$config->recaptcha_secret . "&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
        	if($captachresp['success'] == false){
				//captcha is false
          		$errorMsg = $config->app_msgs['captcha_fail'];
        	} else {
          		if(strlen($password) == 0) {
	 			//password is not set
 				$errorMsg = $config->app_msgs['set_passwd'];
 				$error = true;
	 		}
 			if($password != $password2) {
 				$errorMsg = $config->app_msgs['passwd_mismatch'];
 				$error = true;
 			}
			//check if username is already taken
 			if(!$error) { 
				$caller =  $apiroot . "jodlers?transform=1&filter=jodlerHRID,eq," . $username;
				$resp = getCall($caller);
				if($resp !== '{"jodlers":[]}') {
	 				//recived empty JSON
	 				//TODO: make this nice (code styling, json handling)
					$errorMsg = $config->app_msgs['nametaken'];
 					$error = true;
 				} 
 			}
 			//no errors, user can be registered
 			if(!$error) { 
 				//hash password & salt it
 				$password_hash = password_hash($password, PASSWORD_DEFAULT);
				//remove special chars, to avoid injection
				$username = htmlspecialchars($username, ENT_QUOTES);
				//register the user
 				$caller = $apiroot . "jodlers";
 				$postdata = "{\n  \"jodlerHRID\": \"$username\",\n  \"karma\": 50,\n  \"account_state\": 1,\n  \"passphrase\": \"$password_hash\"\n}";
				$userid = postCall($caller, $postdata);
				//response is ID of the new user
				if(is_numeric($userid)) { 
					//if ID is a number, user is created
					$successMsg = $config->app_msgs['acc_created'];
 					header('Location: ' . $config->baseUrl . 'login.php');
 					$showFormular = false;
 				} else {
	 				//something went wrong
 					$errorMsg = $config->app_msgs['general_error'];
 				}
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
if(isset($errorMsg)) {
			//Show error message
			?>
			<div class="alert alert-danger" role="alert">
  				<strong>Holy guacamole!</strong> <?php echo $errorMsg;?>
			</div>
			<?php
 		}
		if(isset($successMsg)) {
 			?>
			<div class="alert alert-success" role="alert">
  				<strong>Perfect!</strong> <?php echo $successMsg;?>
			</div>
			<?php
	}?>

<div class="alert alert-warning" role="alert">
  <strong>Warning!</strong> This is a test environment. Every user that is not approved by the admins / developer gets banned.
</div>
<!-- signup form -->
<div class="container">
	<div class="wrapper">
		<form action="?register=1" method="post" name="signupform" class="form-signup">       
		    <h3 class="form-signin-heading"><?php echo $config->login_strings['title_signup'];?></h3>
			  <hr class="colorgraph"><br>
			  
			  <input type="text" class="form-control" name="username" placeholder="<?php echo $config->login_strings['username'];?>" required="true" autofocus="true" />
			  <input type="password" class="form-control" name="password1" placeholder="<?php echo $config->login_strings['paswd'];?>" required="true"/>
			  <input type="password" class="form-control" name="password2" placeholder="<?php echo $config->login_strings['repeat_paswd'];?>" required="true"/>  
			  <div class="g-recaptcha" data-sitekey="<?php echo $config->recaptcha_sitekey; ?>"></div>    		  
			 
			  <button class="btn btn-lg btn-block"  name="Submit" value="Signup" type="Submit"><?php echo $config->login_strings['signup'];?></button>  			
		</form>
		<!-- link to login page -->
		<p><?php echo $config->login_strings['acc_exists']; ?></p>			
	</div>
</div>
<!-- end form -->
<?php include 'functions/footer.php';
		}