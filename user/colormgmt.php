<?php
session_start();
include '../functions/apicalls.php';
$config = include('../config.php');
$title = "Add color | SocialDomayn";
$stylesheet = "jodel.css";
include '../functions/header.php';

if(!isset($_SESSION['userid']) || !isset($_SESSION['caps_add_color'])) {
 header('Location: https://jodel.domayntec.ch/login.php');
}

$userid = $_SESSION['userid'];
$apiurl = $config->apiUrl;

if(isset($_GET['addcolor'])){
	$colorname = $_POST['color-text-input'];
	$colorhex = $_POST['color-color-input'];
	$postfields = "{\n  \"colordesc\": \"$colorname\",\n  \"colorhex\": \"$colorhex\"\n}";
	$colorurl = $apiurl . "colors";
	postCall($colorurl, $postfields);
	header('Location: https://jodel.domayntec.ch/user/colormgmt.php');
}

if(isset($_GET['delcol'])){
	$colorid = $_GET['delcol'];
	$callurl = $apiurl . "colors/" . $colorid;
	$deletedColors = deleteCall($callurl);
	header('Location: https://jodel.domayntec.ch/user/colormgmt.php');
}

?>
<div id="top"></div>

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
</div>

<?php
$colorurl = $apiurl . "colors?transform=1";
$colorjson = getCall($colorurl);
$colors = json_decode($colorjson, true);

?>
<form action="?addcolor=1" method="POST">
<div class="form-group row">
  <label for="color-text-input" class="col-2 col-form-label">color name</label>
  <div class="col-10">
    <input class="form-control" type="text" placeholder="blue" name="colorname" id="color-text-input">
  </div>
</div>

<div class="form-group row">
  <label for="color-color-input" class="col-2 col-form-label">colorcode</label>
  <div class="col-10">
    <input class="form-control" type="color" placeholder="#0000ff" name="colorcode" id="color-color-input">
  </div>
</div>
  <button type="submit" class="btn btn-warning">Submit</button>
</form>
<div class="test"></div>

<?php

foreach($colors['colors'] as $color){
	?><div class="card card-inverse mb-3 text-center" id="<?php echo $color['colordesc'];?>" style="background-color: #<?php echo $color['colorhex'];?>;">
  <div class="card-block">
    <blockquote class="card-blockquote">
		<?php echo $color['colordesc'] . "\n" . $color['colorhex'];
		?>
		<div class="jodelvotes">
			
				<a href="?delcol=<?php echo $color['colorID'];?>"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
			</div>
			<div class="clear"></div>
		</blockquote>
		</div>
		</div>
		<?php
}



include '../functions/footer.php';