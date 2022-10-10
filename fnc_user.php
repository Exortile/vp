<?php
    require_once "../../config.php";
    // kõik muutujad, mis deklareeritud väljaspool funktsiooni, on globaalsed ja kättesaadavad massiivist $GLOBALS
    
    function sign_up($first_name, $last_name, $birth_date, $gender, $email, $password) {
        $notice = null;
        
        // loon andmebaasiga uhenduse
        // server, kasutaja, parool, andmebaas
        $db_connection = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);

        // maaran suhtlemisel kasutatava kooditabeli
        $db_connection->set_charset("utf8");

        // valmistame ette andmete saatmise SQL käsu
        $stmt = $db_connection->prepare("INSERT INTO vp_users (firstname, lastname, birthdate, gender, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        echo $db_connection->error;

        // krupteerime parooli
        $pwd_hash = password_hash($password, PASSWORD_DEFAULT);

        // seome SQL käsu oigete andmetega
        // andmetüübid: i - integer, d - decimal, s - string
        $stmt->bind_param("sssiss", $first_name, $last_name, $birth_date, $gender, $email, $pwd_hash);
        
        if ($stmt->execute()) {
            $notice = "Uus kasutaja loodud!";
        } else {
            $notice = "Kasutaja loomisel tekkis tehniline tõrge: " .$stmt->error;
        }

        // sulgeme käsu
        $stmt->close();

        // sulgeme andmebaasi uhenduse
        $db_connection->close();

        return $notice;
    }

    function sign_in($email, $password) {
        $login_success = false;
		// loon andmebaasiga uhenduse
		// server, kasutaja, parool, andmebaas
		$db_connection = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);

		// maaran suhtlemisel kasutatava kooditabeli
		$db_connection->set_charset("utf8");

		// valmistame ette andmete saatmise SQL käsu
		$stmt = $db_connection->prepare("SELECT id, password FROM vp_users WHERE email = ?");
		echo $db_connection->error;

		// seome SQL käsu oigete andmetega
		// andmetüübid: i - integer, d - decimal, s - string
		$stmt->bind_param("s", $email);
		$stmt->bind_result($id_db, $password_db);
		$stmt->execute();

		if ($stmt->fetch()) {
			if (password_verify($password, $password_db)) {
                $login_success = true;

                // määran sessiooni muutujad
                $_SESSION["user_id"] = $id_db;
			}
		}
		// sulgeme käsu
		$stmt->close();

		// sulgeme andmebaasi uhenduse
		$db_connection->close();

        return $login_success;
    }
?>