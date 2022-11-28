<?php

require_once "fnc_edit_photo.php";
require_once "fnc_general.php";
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

$photo_error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["photo_submit"])) {
	// foto muutmine
	$alt = $_POST["alt_input"];
	$privacy = $_POST["privacy_input"];
	$photo_id = $_POST["photo_input"];

	if (!modify_own_photo_data($alt, $privacy, $photo_id, $_SESSION["user_id"])) {
		header("Location: gallery_own.php?page=" .$_SESSION["page"]);
	} else {
		header("Location: edit_photo_data.php?id=" .$photo_id);
	}
} 
else if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["photo_delete"])) {
	// foto kustutamine
	$photo_id = $_POST["photo_input"];

	if (!delete_own_photo($photo_id, $_SESSION["user_id"])) {
		header("Location: gallery_own.php?page=" .$_SESSION["page"]);
	} else {
		header("Location: photo_deleted.php");
	}
}

if (isset($_GET["id"]) and !empty($_GET["id"]) and filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
	$photo_data = read_own_photo_data($_GET["id"]);
	$alt = $photo_data["alt"];
	$privacy = $photo_data["privacy"];
} else {
	header("Location: gallery_own.php?page=" .$_SESSION["page"]);
}

require_once "header.php";

$nimi_html = "<p>Sisse logitud: " .$_SESSION["firstname"] ." " .$_SESSION["lastname"] ."</p>";

?>

<hr>
<?php echo $nimi_html; ?>

<ul>
	<li><a href="?logout=1">Logi välja</a></li>
	<li><a href="home.php">Avalehele</a></li>
	<li><a href="gallery_own.php?page=<?php echo $_SESSION["page"]; ?>">Tagasi oma fotode lapangule</a></li>
</ul>

<hr>

<img src="<?php echo $normal_upload_location .$photo_data["filename"]; ?>" alt="<?php echo $alt; ?>">

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<label for="alt_input">Alternatiivtekst (alt):</label>
	<input type="text" name="alt_input" id="alt_input" placeholder="alternatiivtekst ..." value="<?php echo $alt; ?>">
	<br>
	<input type="radio" name="privacy_input" id="privacy_input_1" value="1" <?php if ($privacy == 1) echo "checked"; ?>>
	<label for="privacy_input_1">Privaatne (ainult ise näen)</label>
	<br>
	<input type="radio" name="privacy_input" id="privacy_input_2" value="2" <?php if ($privacy == 2) echo "checked"; ?>>
	<label for="privacy_input_2">Sisse loginud kasutajatele</label>
	<br>
	<input type="radio" name="privacy_input" id="privacy_input_3" value="3" <?php if ($privacy == 3) echo "checked"; ?>>
	<label for="privacy_input_3">Avalik (kõik näevad)</label>
	<br>
	<input type="hidden" name="photo_input" value="<?php echo $_GET["id"]; ?>">
	<input type="submit" name="photo_submit" id="photo_submit" value="Muuda">
	<input type="submit" name="photo_delete" id="photo_delete" value="Kustuta foto">
	<span><?php echo $photo_error; ?></span>
</form>

<?php require_once "footer.php"; ?>