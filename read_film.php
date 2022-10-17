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

    // loon andmebaasiga uhenduse
    // server, kasutaja, parool, andmebaas
    $db_connection = new mysqli($server_host, $server_user_name, $server_password, $database);

    // maaran suhtlemisel kasutatava kooditabeli
    $db_connection->set_charset("utf8");

    // valmistame ette andmete saatmise SQL käsu
    $stmt = $db_connection->prepare("SELECT pealkiri, aasta, kestus, zanr, tootja, lavastaja FROM film");
    echo $db_connection->error;

    // seome saadavad andmed muutujatega
    $stmt->bind_result($pealkiri_db, $aasta_db, $kestus_db, $zanr_db, $tootja_db, $lavastaja_db);

    // taidame kasu
    $stmt->execute();

    // kui saan uhe kirje
    // if ($stmt->fetch()) { }

    // kui tuleb teadmata arv kirjeid
    $film_html = null;
    while ($stmt->fetch()) { 
        // <h3>Kevade</h3>
        // <ul>
        //     <li>Valmimisaasta: 1969</li>
        //     <li>Kestus: 84 minutit</li>
        //     <li>Žanr: komöödia, draama</li>
        //     <li>Tootja: TallinnFilm</li>
        //     <li>Lavastaja: Arvo Kruusement</li>
        // </ul>

        $film_html .= "<h3>" .$pealkiri_db ."</h3>\n";
        $film_html .= "<ul>\n";
        $film_html .= "<li>Valmimisaasta: " .$aasta_db ."</li>\n";
        $film_html .= "<li>Kestus: " .$kestus_db ." minutit</li>\n";
        $film_html .= "<li>Žanr: " .$zanr_db ."</li>\n";
        $film_html .= "<li>Tootja: " .$tootja_db ."</li>\n";
        $film_html .= "<li>Lavastaja: " .$lavastaja_db ."</li>\n";
        $film_html .= "</ul>\n";
    }

    // sulgeme käsu
    $stmt->close();

    // sulgeme andmebaasi uhenduse
    $db_connection->close();
?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Filmide lapang</title>
</head>

<body>

<img src="pics/vp_banner_gs.png" alt="bänner">
<?php echo $film_html; ?>

<ul>
	<li><a href="?logout=1">Logi välja</a></li>
</ul>

</body>

</html>