<?php

require_once "../fnc_general.php";

require_once "../classes/SessionManager.class.php";
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

function read_all_pidulased() {
    $select_html = '<option value="" disabled>Vali õpilane</option>' ."\n";

    $db_connection = connect_db();

    $stmt = $db_connection->prepare("SELECT id, firstname, lastname, studentcode FROM vp_pidu WHERE cancelled IS NULL AND paid IS NULL");
    echo $db_connection->error;

    $stmt->bind_result($id_db, $firstname_db, $lastname_db, $studentcode_db);
    $stmt->execute();

    while ($stmt->fetch()) {
        $select_html .= '<option value="' .$id_db .'">' .$firstname_db ." " .$lastname_db ." " .$studentcode_db ."</option>\n";
    }
    
    $stmt->close();
    $db_connection->close();

    return $select_html;
}

function mark_pidulane_as_paid($id) {
    $notice = null;
    $current_time = new DateTime("now");
    $current_time = $current_time->format('Y-m-d H:i:s');

    $db_connection = connect_db();

    $stmt = $db_connection->prepare("UPDATE vp_pidu SET paid = ? WHERE id = ?");
    
    $stmt->bind_param("si", $current_time, $id);
    
    if (!$stmt->execute()) {
        echo $stmt->error;
        echo $db_connection->error;
        $notice = "Midagi läks viltu.";
    } else {
        $notice = "Pidulane määratud maksnuks!";
    }

    $stmt->close();
    $db_connection->close();

    return $notice;
}

$notice = null;

// kontrollime sisestust
if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["student_submit"])) {
    if (isset($_POST["student_select"]) and !empty($_POST["student_select"])) {
        $id = $_POST["student_select"];
    } else {
        $notice = "Palun vali pidulane!";
    }

    if (empty($notice)) {
        $notice = mark_pidulane_as_paid($id);
    }
}



?>

<!DOCTYPE html>
<html lang="et">
    <head>
        <meta charset="utf-8">
	
    </head>
    <body>
	
	<hr>
    <h2>Pralle administraatori leht</h2>
		
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	    <select id="student_select" name="student_select">
            <?php
            echo read_all_pidulased();
            ?>
        </select>
        <input type="submit" id="student_submit" name="student_submit" value="Määra õpilane maksnuks">
	    <span><?php echo $notice; ?></span>
    </form>

	<p>Tagasi <a href="home.php">avalehele</a></p>

<?php require_once "../footer.php"; ?>