<?php

require_once "fnc_photo_upload.php";
require_once "fnc_general.php";
require_once "../../config.php";
require_once "classes/Photoupload.class.php";

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

$file_type = null;
$photo_error = null;
$photo_file_size_limit = 1.5 * 1024 * 1024;
$photo_name_prefix = "vp_";
$photo_file_name = null;
$normal_photo_max_w = 800;
$normal_photo_max_h = 450;

if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["photo_submit"]) 
	and isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])) {
	
	$photo = new Photoupload($_FILES["photo_input"]);
	$photo->check_size();
	$photo->create_image();
	$photo->create_filename();
	$photo->normal_photo_max_h = 300;
	$photo->normal_photo_max_w = 300;
	$photo->resize_photos();
	$photo->save_photos($GLOBALS["profile_photo_upload_location"], null, $GLOBALS["original_upload_location"]);

	$photo_error = $photo->error;

	if (empty($photo_error)) {
		$photo_error = "Fail edukalt üles laetud!";
		$userid = $_SESSION["user_id"];

		$db_connection = connect_db();

		$stmt = $db_connection->prepare("SELECT filename FROM vp_userprofilephotos WHERE userid = ?");
		echo $db_connection->error;

		$stmt->bind_param("i", $userid);
		$stmt->bind_result($filename_db);
		$stmt->execute();

		if ($stmt->fetch()) {
			$stmt->close();

			$stmt = $db_connection->prepare("UPDATE vp_userprofilephotos SET filename = ? WHERE userid = ?");
			echo $db_connection->error;
		} else {
			$stmt->close();

			$stmt = $db_connection->prepare("INSERT INTO vp_userprofilephotos (filename, userid) VALUES (?, ?)");
			echo $db_connection->error;
		}

		$stmt->bind_param("si", $photo->filename, $userid);
		$stmt->execute();
		
		$stmt->close();
		$db_connection->close();
	}

	unset($photo);

	/*
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
	*/
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
	<input type="submit" name="photo_submit" id="photo_submit" value="Lae üles">
	<span><?php echo $photo_error; ?></span>
</form>

<?php require_once "footer.php"; ?>