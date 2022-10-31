<?php
    require_once "../../config.php";
    require_once "fnc_general.php";

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

    $error = false;
    $pealkiri_error = null;
    $aasta_error = null;
    $kestus_error = null;
    $zanr_error = null;
    $tootja_error = null;
    $lavastaja_error = null;

    if (isset($_POST["film_submit"])) {
        if (isset($_POST["title_input"]) and !empty($_POST["title_input"])) {
            $pealkiri = $_POST["title_input"];
        } else {
            $pealkiri_error = "Pealkiri on panemata!";
            $error = true;
        }

        if (isset($_POST["year_input"]) and !empty($_POST["year_input"]) 
        and $_POST["year_input"] >= 1912 and $_POST["year_input"] <= date("Y")) {
            $aasta = $_POST["year_input"];
        } else {
            $aasta_error = "Aasta on vale!";
            $error = true;
        }

        if (isset($_POST["duration_input"]) and !empty($_POST["duration_input"])) {
            $kestus = $_POST["duration_input"];
        } else {
            $kestus_error = "Kestus on panemata!";
            $error = true;
        }

        if (isset($_POST["genre_input"]) and !empty($_POST["genre_input"])) {
            $zanr = $_POST["genre_input"];
        } else {
            $zanr_error = "Zanr on panemata!";
            $error = true;
        }

        if (isset($_POST["studio_input"]) and !empty($_POST["studio_input"])) {
            $tootja = $_POST["studio_input"];
        } else {
            $tootja_error = "Tootja on panemata!";
            $error = true;
        }

        if (isset($_POST["director_input"]) and !empty($_POST["director_input"])) {
            $lavastaja = $_POST["director_input"];
        } else {
            $lavastaja_error = "Lavastaja on panemata!";
            $error = true;
        }

        if (!$error) {
            // loon andmebaasiga uhenduse
			// server, kasutaja, parool, andmebaas
			$db_connection = connect_db();

			// valmistame ette andmete saatmise SQL käsu
			$stmt = $db_connection->prepare("INSERT INTO film (pealkiri, aasta, kestus, zanr, tootja, lavastaja) VALUES (?, ?, ?, ?, ?, ?)");
			echo $db_connection->error;

			// seome SQL käsu oigete andmetega
			// andmetüübid: i - integer, d - decimal, s - string
			$stmt->bind_param("siisss", $pealkiri, $aasta, $kestus, $zanr, $tootja, $lavastaja);
			if ($stmt->execute()) {
				$pealkiri = null;
                $aasta = null;
                $kestus = null;
                $zanr = null;
                $tootja = null;
                $lavastaja = null;

                $_POST["title_input"] = null;
                $_POST["year_input"] = null;
                $_POST["duration_input"] = null;
                $_POST["genre_input"] = null;
                $_POST["studio_input"] = null;
                $_POST["director_input"] = null;
			}

			// sulgeme käsu
			$stmt->close();

			// sulgeme andmebaasi uhenduse
			$db_connection->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Filmide sisestus</title>
</head>

<body>

<img src="pics/vp_banner_gs.png" alt="bänner">
<form method="POST">
    <label for="title_input">Filmi pealkiri</label>
    <input type="text" name="title_input" id="title_input" placeholder="filmi pealkiri" value="<?php if (isset($_POST["title_input"])) echo $_POST["title_input"]; ?>">
    <span><?php echo $pealkiri_error; ?></span>
    <br>
    <label for="year_input">Valmimisaasta</label>
    <input type="number" name="year_input" id="year_input" min="1912" value="<?php if (isset($_POST["year_input"])) echo $_POST["year_input"]; ?>">
    <span><?php echo $aasta_error; ?></span>
    <br>
    <label for="duration_input">Kestus</label>
    <input type="number" name="duration_input" id="duration_input" min="1" value="<?php if (isset($_POST["duration_input"])) echo $_POST["duration_input"]; ?>" max="600">
    <span><?php echo $kestus_error; ?></span>
    <br>
    <label for="genre_input">Filmi žanr</label>
    <input type="text" name="genre_input" id="genre_input" placeholder="žanr" value="<?php if (isset($_POST["genre_input"])) echo $_POST["genre_input"]; ?>">
    <span><?php echo $zanr_error; ?></span>
    <br>
    <label for="studio_input">Filmi tootja</label>
    <input type="text" name="studio_input" id="studio_input" placeholder="filmi tootja" value="<?php if (isset($_POST["studio_input"])) echo $_POST["studio_input"]; ?>">
    <span><?php echo $tootja_error; ?></span>
    <br>
    <label for="director_input">Filmi režissöör</label>
    <input type="text" name="director_input" id="director_input" placeholder="filmi režissöör" value="<?php if (isset($_POST["director_input"])) echo $_POST["director_input"]; ?>">
    <span><?php echo $lavastaja_error; ?></span>
    <br>
    <input type="submit" name="film_submit" value="Salvesta">
</form>

<ul>
	<li><a href="?logout=1">Logi välja</a></li>
</ul>

</body>

</html>