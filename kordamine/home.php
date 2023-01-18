<?php
require_once "/home/pransten/public_html/vp/fnc_general.php";
require_once "/home/pransten/public_html/vp/kordamine/fnc_user_register.php";
$notice = null;

    if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["studentcode_submit"])) {
        if (isset($_POST["studentcode_input"]) and !empty($_POST["studentcode_input"])) {
            $studentcode = test_input($_POST["studentcode_input"]);
            if ($studentcode != $_POST["studentcode_input"]) {
                $notice = "Õpilaskoodist eemaldati sobimatuid tähemärke. Palun kontrolli!";
            }
        } else {
            $notice = "Palun sisesta õpilaskood!";
        }
        
        if (isset($_POST["firstname_input"]) and !empty($_POST["firstname_input"])) {
            $firstname = test_input($_POST["firstname_input"]);
            if ($firstname != $_POST["firstname_input"]) {
                $notice = "Eesnimest eemaldati sobimatuid tähemärke.";
            }
        } else {
            $notice = "Palun sisesta oma eesnimi!";
        }
        
        if (isset($_POST["lastname_input"]) and !empty($_POST["lastname_input"])) {
            $lastname = test_input($_POST["lastname_input"]);
            if ($lastname != $_POST["lastname_input"]) {
                $notice = "Perekonnanimest eemaldati sobimatuid tähemärke.";
            }
        } else {
            $notice = "Palun sisesta oma perekonnanimi!";
        }

        if(empty($notice)){
            $notice = sign_up($studentcode, $firstname, $lastname);
        }
    }
    $person_count = count_pidu();
?>

<!DOCTYPE html>
<html lang="et">
  <head>
    <meta charset="utf-8">
	
  </head>
  <body>
	
	<hr>
    <h2>Registreeri end peole!</h2>
		
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	  <label for="studentcode_input">Õpilaskood:</label><br>
	  <input name="studentcode_input" id="studentcode_input" type="text">
      <br>
      <label for="firstname_input">Eesnimi:</label><br>
	  <input name="firstname_input" id="firstname_input" type="text">
      <br>
      <label for="lastname_input">Perekonnanimi:</label><br>
	  <input name="lastname_input" id="lastname_input" type="text">
	  <br>
      <input name="studentcode_submit" type="submit" value="Kinnita">
      <br>
      <p>Pidulaste arv:<?php echo $person_count; ?><p>
	</form>
    <span><?php echo $notice; ?></span>

<?php require_once "../footer.php"; ?>