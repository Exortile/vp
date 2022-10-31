<?php

require_once "fnc_photo_upload.php";
require_once "fnc_general.php";
require_once "../../config.php";

session_start();

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

$file_type = null;
$photo_error = null;
$photo_file_size_limit = 1.5 * 1024 * 1024;
$photo_name_prefix = "vp_";
$photo_file_name = null;
$normal_photo_max_w = 800;
$normal_photo_max_h = 450;

if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["photo_submit"]) 
	and isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])) {
	
	// kontrollime kas on sobilik
	$file_type = check_file_type($_FILES["photo_input"]["tmp_name"]);
	if ($file_type == "maasikas") {
		$photo_error = "Valitud fail pole sobivat tüüpi!";
	}

	// failimaht
	if (empty($photo_error)) {
		if ($_FILES["photo_input"]["size"] >= $photo_file_size_limit) {
			$photo_error = "Valitud fail on liiga suur!";
		}
	}

	// genereerin failinime
	$photo_file_name = create_filename($photo_name_prefix, $file_type);

	if (empty($photo_error)) {
		// teeme pildi "väiksemaks"
		// loome pikslikogumi (justkui avame foto Photoshopis)
		$temp_photo = create_image($_FILES["photo_input"]["tmp_name"], $file_type);

		// muudame pildi suurust
		$normal_foto = resize_photo($temp_photo, $normal_photo_max_w, $normal_photo_max_h);
		$thumbnail_photo = resize_photo_thumbnail($temp_photo);

		if (save_photo($normal_foto, $GLOBALS["normal_upload_location"] .$photo_file_name, $file_type)) {
			if (save_photo($thumbnail_photo, $GLOBALS["thumbnail_upload_location"] .$photo_file_name, $file_type)) {
				// ajutine fail: $_FILES["photo_input"]["tmp_name"]
				if (move_uploaded_file($_FILES["photo_input"]["tmp_name"], $GLOBALS["original_upload_location"] .$photo_file_name)) {
					$db_connection = connect_db();

					$stmt = $db_connection->prepare("INSERT INTO vp_photos (userid, filename, alttext, privacy) VALUES (?, ?, ?, ?)");
					echo $db_connection->error;

					$stmt->bind_param("issi", $_SESSION["user_id"], $photo_file_name, $_POST["alt_input"], $_POST["privacy_input"]);
					$stmt->execute();
					
					$stmt->close();
					$db_connection->close();
				}
			}
		}
	}
}

require_once "header.php";

$nimi_html = "<p>Sisse logitud: " .$_SESSION["firstname"] ." " .$_SESSION["lastname"] ."</p>";

?>

<hr>
<?php echo $nimi_html; ?>

<ul>
	<li><a href="?logout=1">Logi välja</a></li>
	<li><a href="home.php">Avalehele</a></li>
</ul>

<hr>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
	<label for="photo_input">Vali pildifail:</label>
	<input type="file" name="photo_input" id="photo_input">
	<br>
	<label for="alt_input">Alternatiivtekst (alt):</label>
	<input type="text" name="alt_input" id="alt_input" placeholder="alternatiivtekst ...">
	<br>
	<input type="radio" name="privacy_input" id="privacy_input_1" value="1">
	<label for="privacy_input_1">Privaatne (ainult ise näen)</label>
	<br>
	<input type="radio" name="privacy_input" id="privacy_input_2" value="2">
	<label for="privacy_input_2">Sisse loginud kasutajatele</label>
	<br>
	<input type="radio" name="privacy_input" id="privacy_input_3" value="3">
	<label for="privacy_input_3">Avalik (kõik näevad)</label>
	<br>
	<input type="submit" name="photo_submit" id="photo_submit" value="Lae üles">
	<span><?php echo $photo_error; ?></span>
</form>

<?php require_once "footer.php"; ?>