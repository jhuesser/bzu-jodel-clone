<?php

session_start();
//Set default values for head & load it
$title = "Login | SocialDomayn";
$stylesheet = "login.css";
include 'functions/header.php';
//Load API functions
include 'functions/apicalls.php';
$config = include('config.php');

//If user sent loginform, set values
if(isset($_GET['login'])) {
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
        if($captachresp['success'] == false)
        {
			//captcha is not valid
          $errorMessage = $config->app_msgs['captcha_fail'];
        }
        else
        {
			//check if username is registered
$caller = "https://jodel.domayntec.ch/api.php/jodlers?transform=1&filter=jodlerHRID,eq," . $username;
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
	
	}

 //check password
 if ($user !== false && password_verify($password, $passwordDB)) {
	//Login successfull
 	$_SESSION['userid'] = $userid;
	 $_SESSION['username'] = $jodlerHRID;
 	
 	header('Location: https://jodel.domayntec.ch/jodels.php');
	 
	// echo '<a href="index.php">openindex</a>';
 } else {
	 //Login failed
 $errorMessage = $config->app_msgs['login_fail'];
 
}

}
if(isset($errorMessage)) {
	//display login message
 ?>

 <div class="alert alert-danger" role="alert">
  
  <strong>Holy guacamole!</strong> <?php echo $errorMessage;?>
</div>
<?php
}

}

?>
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