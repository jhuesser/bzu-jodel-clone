<?php
session_start();
include 'functions/apicalls.php';
$config = include('config.php');
$title = "My profile | SocialDomayn";
$stylesheet = "jodel.css";
include 'functions/header.php';
if(isset($_GET['logout'])) {
session_destroy();
 
header('Location: https://jodel.domayntec.ch/login.php');
}
$userid = $_SESSION['userid'];




?>
<div id="top"></div>

<ul class="nav justify-content-center">
		<li class="nav-item">
			<a class="nav-link" href="jodels.php"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
		</li>
		<li class="nav-item">
    <a class="nav-link" href="javascript:window.location.reload();"><i class="fa fa-refresh" aria-hidden="true"></i></a>
  </li>
  
  <li class="nav-item">
    <a class="nav-link" href="user.php"><i class="fa fa-user" aria-hidden="true"></i><?php echo $_SESSION['karma'];?></a>
  </li>
  <li class="nav-item">
  <a class="nav-link" href="?logout=1"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
  </li>
</ul>

<div class="test"></div>
<?php
if(isset($_SESSION['errorMsg'])) {
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
<div class="list-group">
  <a href="#" class="list-group-item list-group-item-action">My <?php echo $config->app_vocabulary['posts'];?></a>
  <a href="#" class="list-group-item list-group-item-action">My <?php echo $config->app_vocabulary['comments'];?></a>
  <a href="#" class="list-group-item list-group-item-action">My votes</a>
</div>


<?php

switch($_SESSION['acctype']){
	case 0:
		$accdesc = $config->app_vocabulary['baned'];
		break;
	case 1:
		$accdesc = $config->app_vocabulary['jodler'];
		$caps = $config->user_caps->user;
		break;
	case 2:
		$accdesc = $config->app_vocabulary['mod'];
		$caps = $config->user_caps->mod;
		break;
	case 3:
		$accdesc = $config->app_vocabulary['admin'];
		$caps = $config->user_caps->admin;
		break;
	case 4:
		$accdesc = $config->app_vocabulary['superadmin'];
		$caps = $config->user_caps->superadmin;
		break;
	default:
	$accdesc = "Well you are a funny type of user.";
}

echo "<h2>You are " . $accdesc . ". Here are your tools:</h2>";
?>


<div class="list-group">
<?php if($caps['mod_posts'] == true){
  echo '<a href="#" class="list-group-item list-group-item-action">Moderation</a>'; 
  $_SESSION['caps_mod_posts'] = true;
  $hascaps = true;
  }
  if($caps['reset_paswd'] == true){
  echo '<a href="#" class="list-group-item list-group-item-action">Reset user password</a>'; 
   $_SESSION['caps_reset_paswd'] = true;
  $hascaps = true;
  }
  if($caps['promote_to_mod'] == true || $caps['promote_to_admin'] == true  || $caps['promote_to_superadmin'] == true || $caps['promote_to_user'] == true || $caps['ban'] == true || $caps['delete_users'] == true || $caps['change_karma'] == true )  {
  echo '<a href="#" class="list-group-item list-group-item-action">Usermanagement</a>'; 
   $_SESSION['caps_promote_to_mod'] = true;
  $hascaps = true;
  }
  if($caps['delete_posts'] == true || $caps['change_post_score'] == true || $caps['change_votes'] == true || $caps['edit_posts'] == true){
  echo '<a href="#" class="list-group-item list-group-item-action">Postmanagement</a>'; 
   $_SESSION['caps_delete_posts'] = true;
  $hascaps = true;
  }
  if($caps['add_color'] == true){
  echo '<a href="user/colormgmt.php" class="list-group-item list-group-item-action">Add a color</a>'; 
   $_SESSION['caps_add_color'] = true;
  $hascaps = true;
  } 
  if($caps['delete_user_votes'] == true){
  echo '<a href="#" class="list-group-item list-group-item-action">Manage Votes</a>'; 
   $_SESSION['caps_delete_user_votes'] = true;
  $hascaps = true;
  }
if($caps['create_admin_notice'] == true){
  echo '<a href="#" class="list-group-item list-group-item-action">Create admin notice</a>'; 
   $_SESSION['caps_create_admin_notice'] = true;
  $hascaps = true;
  }
 

 if(!isset($hascaps)){
	 

echo '<div class="alert alert-warning" role="alert"><strong>Oh snap!</strong> You don\'t have any tools, go create good vibes and incerase your ' . $config->app_vocabulary['karma'] . ' to recive some.</div>';
	
 }
?>
</div>


