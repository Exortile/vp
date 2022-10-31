<?php
    require_once "../../config.php";
    require_once "fnc_general.php";
    // kõik muutujad, mis deklareeritud väljaspool funktsiooni, on globaalsed ja kättesaadavad massiivist $GLOBALS
    
    function sign_up($first_name, $last_name, $birth_date, $gender, $email, $password) {
        $notice = null;
        
        // loon andmebaasiga uhenduse
        // server, kasutaja, parool, andmebaas
        $db_connection = connect_db();

        $checkstmt = $db_connection->prepare("SELECT id FROM vp_users WHERE email = ?");
        echo $db_connection->error;

        $checkstmt->bind_param("s", $email);
        $checkstmt->bind_result($id_db);
        $checkstmt->execute();

        if (!$checkstmt->fetch()) {
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
        } else {
            $notice = "Sellise e-mailiga kasutaja juba on olemas!";
        }

        $checkstmt->close();

        // sulgeme andmebaasi uhenduse
        $db_connection->close();

        return $notice;
    }

    function sign_in($email, $password) {
        $login_success = false;
		// loon andmebaasiga uhenduse
		// server, kasutaja, parool, andmebaas
		$db_connection = connect_db();

		// valmistame ette andmete saatmise SQL käsu
		$stmt = $db_connection->prepare("SELECT password FROM vp_users WHERE email = ?");
		echo $db_connection->error;

		// seome SQL käsu oigete andmetega
		// andmetüübid: i - integer, d - decimal, s - string
		$stmt->bind_param("s", $email);
		$stmt->bind_result($password_db);
		$stmt->execute();

		if ($stmt->fetch()) {
			if (password_verify($password, $password_db)) {
                $login_success = true;
			}
		}
		// sulgeme käsu
		$stmt->close();

        if ($login_success) {
            $stmt = $db_connection->prepare("SELECT id, firstname, lastname FROM vp_users WHERE email = ?");
            echo $db_connection->error;

            // seome SQL käsu oigete andmetega
            // andmetüübid: i - integer, d - decimal, s - string
            $stmt->bind_param("s", $email);
            $stmt->bind_result($id_db, $firstname_db, $lastname_db);
            $stmt->execute();

            if ($stmt->fetch()) {
                // määran sessiooni muutujad
                $_SESSION["user_id"] = $id_db;
                $_SESSION["firstname"] = $firstname_db;
                $_SESSION["lastname"] = $lastname_db;
            }

            $stmt->close();
        }

		// sulgeme andmebaasi uhenduse
		$db_connection->close();

        return $login_success;
    }
?>