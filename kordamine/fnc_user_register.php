<?php
    require_once "/home/pransten/config.php";
	
	function sign_up($studentcode, $firstname, $lastname){
		$notice = null;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
		$stmt = $conn->prepare("SELECT id FROM vp_pidu WHERE studentcode = ?");
		echo $conn->error;
		$stmt->bind_param("s", $studentcode);
		$stmt->bind_result($id_from_db);
		$stmt->execute();
		if($stmt->fetch()){
			$notice = "Selline kasutaja on juba olemas!";
		} else {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO vp_pidu (studentcode, firstname, lastname) values(?,?,?)");
			echo $conn->error;
			$stmt->bind_param("sss", $studentcode, $firstname, $lastname,);
			if($stmt->execute()){
				$notice = "Peole registreeritud";
			} else {
				$notice = "Midagi läks nüüd viltu! " .$stmt->error;
			}
		}
		$stmt->close();
		$conn->close();
		return $notice;
	}

	function count_pidu(){
		$person_count = 0;
		$conn = connect_db();
		$stmt = $conn->prepare("SELECT COUNT(id) FROM vp_pidu WHERE cancelled IS NULL");
		echo $conn->error;
		$stmt->bind_result($count_from_db);
		$stmt->execute();
		if($stmt->fetch()){
			$person_count = $count_from_db;
		}
		$stmt->close();
		$conn->close();
		return $person_count;
	}

	function count_paid(){
		$paid_count = 0;
		$db_connection = connect_db();
		$stmt = $db_connection->prepare("SELECT COUNT(id) FROM vp_pidu WHERE cancelled IS NULL AND paid IS NOT NULL");
		echo $db_connection->error;
		$stmt->bind_result($count_from_db);
		$stmt->execute();
		if($stmt->fetch()){
			$paid_count = $count_from_db;
		}
		$stmt->close();
		$db_connection->close();
		return $paid_count;
	}