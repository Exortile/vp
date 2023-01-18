<?php

require_once "../fnc_general.php";

require_once "../classes/SessionManager.class.php";
SessionManager::sessionStart("vp", 0, "/~pransten/vp/", "greeny.cs.tlu.ee");

// kontrollin, kas oleme sisse loginud
if (!isset($_SESSION["user_id"])) {
	header("Location: ../page.php");
	exit();
}

// logime välja
if (isset($_GET["logout"])) {
	session_destroy();
	header("Location: ../page.php");
	exit();
}

function cancel_student_pidu($studentcode) {
    $notice = null;

    $db_connection = connect_db();

    $stmt = $db_connection->prepare("SELECT id from vp_pidu WHERE studentcode = ?");
    echo $db_connection->error;

    $stmt->bind_param("s", $studentcode);
    $stmt->bind_result($id_db);
    $stmt->execute();

    if ($stmt->fetch()) {
        $stmt->close();

        $current_time = new DateTime("now");
        $current_time = $current_time->format('Y-m-d H:i:s');

        $stmt = $db_connection->prepare("UPDATE vp_pidu SET cancelled = ? WHERE studentcode = ?");
        echo $db_connection->error;

        $stmt->bind_param("ss", $current_time, $studentcode);
        if (!$stmt->execute()) {
            echo $stmt->error;
            echo $db_connection->error;
            $notice = "Midagi läks viltu.";
        } else {
            $notice = "Teie prallele tuleku tühistamise soov on kätte saadud. No refund.";
        }
    } else {
        $notice = "Sellist õpilaskoodi ei leitud!";
    }

    $stmt->close();
    $db_connection->close();

    return $notice;
}

$notice = null;

// kontrollime sisestust
if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["studentcode_submit"])) {
    if (isset($_POST["studentcode_input"]) and !empty($_POST["studentcode_input"])) {
        $studentcode = test_input($_POST["studentcode_input"]);
        if ($studentcode != $_POST["studentcode_input"]) {
            $notice = "Õpilaskoodist eemaldati sobimatuid tähemärke. Palun kontrolli!";
        }
    } else {
        $notice = "Palun sisesta õpilaskood!";
    }
    
    if (empty($notice)) {
        $notice = cancel_student_pidu($studentcode);
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
    <h2>Tühista prallele tulek</h2>
		
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	  <label for="studentcode_input">Õpilaskood:</label><br>
	  <input name="studentcode_input" id="studentcode_input" type="text">
	  <input name="studentcode_submit" type="submit" value="Kinnita">
	  <span><?php echo $notice; ?></span>
	</form>

	<p>Tagasi <a href="../home.php">avalehele</a></p>

<?php require_once "../footer.php"; ?>