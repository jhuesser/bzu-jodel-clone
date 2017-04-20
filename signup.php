<?php

session_start();
$title = "Sign up | SocialDomayn";
$stylesheet = "login.css";
include 'functions/header.php';
include 'functions/apicalls.php';
$config = include('config.php');
?>


<?php

if(isset($_GET['register'])) {


 $error = false;
 $username = $_POST['username'];
 $password = $_POST['password1'];
 $password2 = $_POST['password2'];
if(isset($_POST['g-recaptcha-response']))
          $captcha=$_POST['g-recaptcha-response'];

        if(!$captcha){
          $errorMsg = $config->app_msgs['catpcha_not_solved'];
          
        }

 $captachresp =json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LdozRgTAAAAALNEFAspM4WP66rIrWxikjFSnHfK&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
        if($captachresp['success'] == false)
        {
          $errorMsg = $config->app_msgs['captcha_fail'];
        }
        else
        {
          
        
 if(strlen($password) == 0) {
 	$errorMsg = $config->app_msgs['set_passwd'];
 $error = true;
 }
 if($password != $password2) {
 	$errorMsg = $config->app_msgs['passwd_mismatch'];
 $error = true;
 }


 
 //Überprüfe, dass die E-Mail-Adresse noch nicht registriert wurde
 if(!$error) { 
 
	$caller = "https://jodel.domayntec.ch/api.php/jodlers?transform=1&filter=jodlerHRID,eq," . $username;
	$resp = getCall($caller);
 if($resp !== '{"jodlers":[]}') {
	$errorMsg = $config->app_msgs['nametaken'];
 $error = true;
 } 
 }
 
 //Keine Fehler, wir können den Nutzer registrieren
 if(!$error) { 
 $password_hash = password_hash($password, PASSWORD_DEFAULT);

 $username = htmlspecialchars($username, ENT_QUOTES);
 
 
 $caller ="https://jodel.domayntec.ch/api.php/jodlers";
 $postdata = "{\n  \"jodlerHRID\": \"$username\",\n  \"karma\": 50,\n  \"account_state\": 1,\n  \"passphrase\": \"$password_hash\"\n}";
 $userid = postCall($caller, $postdata);

 if(is_numeric($userid)) { 
	$successMsg = $config->app_msgs['acc_created'];
 header('Location: https://jodel.domayntec.ch/login.php');
 $showFormular = false;
 } else {
 	$errorMsg = $config->app_msgs['general_error'];
 }
 } 

 
}

if(isset($errorMsg)) {
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


<div class="container">
	<div class="wrapper">
		<form action="?register=1" method="post" name="signupform" class="form-signup">       
		    <h3 class="form-signin-heading"><?php echo $config->login_strings['title_signup'];?></h3>
			  <hr class="colorgraph"><br>
			  
			  <input type="text" class="form-control" name="username" placeholder="<?php echo $config->login_strings['username'];?>" required="true" autofocus="true" />
			  <input type="password" class="form-control" name="password1" placeholder="<?php echo $config->login_strings['paswd'];?>" required="true"/>
			  <input type="password" class="form-control" name="password2" placeholder="<?php echo $config->login_strings['repeat_paswd'];?>" required="true"/>  
			  <div class="g-recaptcha" data-sitekey="6LdozRgTAAAAAFBmt9zfZTm6wdh8kVkKXigPnYmq"></div>    		  
			 
			  <button class="btn btn-lg btn-block"  name="Submit" value="Signup" type="Submit"><?php echo $config->login_strings['signup'];?></button>  			
		</form>
		<p><?php echo $config->login_strings['acc_exists']; ?></p>			
	</div>
</div>