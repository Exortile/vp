<?php

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

$db_connection = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);

// maaran suhtlemisel kasutatava kooditabeli
$db_connection->set_charset("utf8");

$stmt = $db_connection->prepare("SELECT filename, alttext FROM vp_photos WHERE privacy >= 2 AND deleted IS NULL");
echo $db_connection->error;

# $stmt->bind_param("i", 2);
$stmt->bind_result($filename_db, $alttext_db);
$stmt->execute();

$img_html = null;

while ($stmt->fetch()) {
    # <img src="pildi url" alt="alteranatiivtekst">
    if (empty($alttext_db)) {
        $alttext = "Galeriipilt";
    } else {
        $alttext = $alttext_db;
    }

    $img_html .= '<img src="' .$normal_upload_location .$filename_db .'" alt="' .$alttext .'">';
    $img_html .= "<br>\n";

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

<?php echo $img_html; ?>