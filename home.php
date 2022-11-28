<?php

require_once "classes/SessionManager.class.php";
SessionManager::sessionStart("vp", 0, "/~pransten/vp/", "greeny.cs.tlu.ee");

// session_start();

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

$last_visitor = "pole teada";
if (isset($_COOKIE["lastvisitor"]) and !empty($_COOKIE["lastvisitor"])) {
	$last_visitor = $_COOKIE["lastvisitor"];
}

// küpsised, enne veebilehe algust

// cookie nimi, väärtus, aegumine sekundites, kataloog serveris, domeen, kas https, kas juurdepääs ainult üle veebi
// https jaoks saab teha ka nii, kui pole kindel: isset($_SERVER["HTTPS"])
setcookie("lastvisitor", $_SESSION["firstname"] ." " .$_SESSION["lastname"], (86400 * 7), "/~pransten/vp/", "greeny.cs.tlu.ee", true, true);

// cookie kustutamine
// setcookie 	aegumine negatiivne: 	time() - 3000

require_once "header.php";

$nimi_html = "<p>Sisse logitud: " .$_SESSION["firstname"] ." " .$_SESSION["lastname"] ."</p>";
$kulastaja_html = "<p>Viimane külastaja: " .$last_visitor ."</p>";

?>

<hr>
<?php 
echo $nimi_html; 
echo $kulastaja_html;
?>

<ul>
	<li><a href="?logout=1">Logi välja</a></li>
	<li><a href="user_profile.php">Lappa oma profiili</a></li>
	<li><a href="read_film.php">Filmide lapang</a></li>
	<li><a href="write_film.php">Filmide lisamine</a></li>
	<li><a href="gallery_photo_upload.php">Fotode üleslaadimine</a></li>
	<li><a href="gallery_public.php">Fotode lapang</a></li>
	<li><a href="gallery_own.php">Oma fotode lapang</a></li>
</ul>
<?php require_once "footer.php"; ?>