<?php

	session_start();
	//Load functions, config and pass metadata
	$title = "Sign up | SocialDomayn";
	$stylesheet = "login.css";
	include 'functions/header.php';
	require 'functions/apicalls.php';
	$config = require('config.php');
	$apiroot = $config->apiUrl;

	if(isset($_GET['register'])) {
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
		}


	}
?>

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