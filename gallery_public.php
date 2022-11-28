<?php

require_once "../../config.php";
require_once "fnc_general.php";
require_once "fnc_gallery.php";

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

/*
$db_connection = connect_db();

$stmt = $db_connection->prepare("SELECT vp_photos.filename, vp_photos.alttext, vp_users.firstname, vp_users.lastname FROM vp_photos JOIN vp_users ON vp_photos.userid = vp_users.id WHERE vp_photos.privacy >= 2 AND vp_photos.deleted IS NULL GROUP BY vp_photos.id");
echo $db_connection->error;

# $stmt->bind_param("i", 2);
$stmt->bind_result($filename_db, $alttext_db, $firstname_db, $lastname_db);
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
    $img_html .= "\n";
    $img_html .= "<p>Üles laadis: " .$firstname_db ." " .$lastname_db ."</p>\n";

}

$stmt->close();
$db_connection->close();
*/





$privacy = 2;
$page = 1;
$limit = 4;
$photo_count = count_photos($privacy);

// kontrollime, mis lehel oleme ja kas selle numbriga leht on reaalne
if (!isset($_GET["page"]) or $_GET["page"] < 1) {
    $page = 1;
} else if (round($_GET["page"] - 1) * $limit >= $photo_count) {
    $page = ceil($photo_count / $limit);
} else {
    $page = $_GET["page"];
}

$style_sheets = ["style/gallery.css"];

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

<p>
<?php
    //Eelmine leht | Järgmine leht
    //<span>Eelmine leht</span> | <span><a href="?page=2">Järgmine leht</a></span>
    if($page > 1){
        echo '<span><a href="?page=' .($page - 1) .'">Eelmine leht</a></span>';
    } else {
        echo "<span>Eelmine leht</span>";
    }
    echo " | ";
    if($page * $limit < $photo_count){
        echo '<span><a href="?page=' .($page + 1) .'">Järgmine leht</a></span>';
    } else {
        echo "<span>Järgmine leht</span>";
    }
?>
</p>

<div class="gallery">
    <?php echo read_public_photo_thumbs($privacy, $page, $limit); ?>
</div>

<?php require_once "footer.php"; ?>