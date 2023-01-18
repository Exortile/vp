<?php

require_once "fnc_user_profile.php";
require_once "../../config.php";

//session_start();
require_once "classes/SessionManager.class.php";
SessionManager::sessionStart("vp", 0, "/~pransten/vp/", "greeny.cs.tlu.ee");

// kontrollin, kas oleme sisse loginud
if (!isset($_SESSION["user_id"])) {
	header("Location: page.php");
	exit();
}

// logime välja
if (isset($_GET["logout"])) {
	session_destroy();
	header("Location: page.php");
	exit();
}

$profile_error = null;

$user_profile_data = read_user_profile($_SESSION["user_id"]);

$description = $user_profile_data["description"];
$bgcolor = $user_profile_data["bgcolor"];
$txtcolor = $user_profile_data["txtcolor"];

if (isset($_POST["profile_submit"])) {
	$description = $_POST["user_description"];
	$bgcolor = $_POST["bg_color_input"];
	$txtcolor = $_POST["txt_color_input"];

	write_user_profile($_SESSION["user_id"], $description, $bgcolor, $txtcolor);
}

$_SESSION["user_bg_color"] = $bgcolor;
$_SESSION["user_txt_color"] = $txtcolor;

require_once "header.php";

$nimi_html = "<p>Sisse logitud: " .$_SESSION["firstname"] ." " .$_SESSION["lastname"] ."</p>";
$profiilipilt_html = "<p>Teie kena profiilipilt:</p>\n" .read_user_profile_photo($_SESSION["user_id"], $_SESSION["firstname"], $_SESSION["lastname"]);

?>

<hr>
<?php 
echo $nimi_html; 
echo $profiilipilt_html;
?>

<ul>
	<li><a href="?logout=1">Logi välja</a></li>
	<li><a href="home.php">Avalehele</a></li>
</ul>

<hr>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<label for="bg_color_input">Taustavärv:</label>
	<input type="color" name="bg_color_input" id="bg_color_input">
	<br>
	<label for="txt_color_input">Tekstivärv:</label>
	<input type="color" name="txt_color_input" id="txt_color_input">
	<br>
	<label for="user_description">Lühikirjeldus:</label>
	<br>
	<textarea name="user_description" id="user_description" rows="5" cols="51" placeholder="Minu lühikirjeldus"></textarea>
	<br>
	<input type="submit" name="profile_submit" id="profile_submit" value="Muuda">
	<span><?php echo $profile_error; ?></span>
</form>

<?php require_once "footer.php"; ?>