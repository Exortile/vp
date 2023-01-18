<?php

require_once "fnc_general.php";
require_once "../../config.php";

function read_user_profile($userid) {
    $user_profile_data = [];

    $db_connection = connect_db();

    $stmt = $db_connection->prepare("SELECT description, bgcolor, txtcolor FROM vp_userprofiles WHERE userid = ?");
    echo $db_connection->error;

    $stmt->bind_param("i", $userid);
    $stmt->bind_result($description_db, $bgcolor_db, $txtcolor_db);
    $stmt->execute();

    if ($stmt->fetch()) {
        $user_profile_data["description"] = $description_db;
        $user_profile_data["bgcolor"] = $bgcolor_db;
        $user_profile_data["txtcolor"] = $txtcolor_db;
    } else {
        $user_profile_data["description"] = null;
        $user_profile_data["bgcolor"] = "#DDDDDD";
        $user_profile_data["txtcolor"] = "#333399";
    }

    $stmt->close();
    $db_connection->close();

    return $user_profile_data;
}

function write_user_profile($userid, $description, $bgcolor, $txtcolor) {
    $db_connection = connect_db();

    $stmt = $db_connection->prepare("SELECT description FROM vp_userprofiles WHERE userid = ?");
    echo $db_connection->error;

    $stmt->bind_param("i", $userid);
    $stmt->bind_result($description_db);
    $stmt->execute();

    if ($stmt->fetch()) {
        $stmt->close();

        $stmt = $db_connection->prepare("UPDATE vp_userprofiles SET description = ?, bgcolor = ?, txtcolor = ? WHERE userid = ?");
        echo $db_connection->error;

        $stmt->bind_param("sssi", $description, $bgcolor, $txtcolor, $userid);
    } else {
        $stmt->close();

        $stmt = $db_connection->prepare("INSERT INTO vp_userprofiles (userid, description, bgcolor, txtcolor) VALUES (?, ?, ?, ?)");
        echo $db_connection->error;

        $stmt->bind_param("isss", $userid, $description, $bgcolor, $txtcolor);
    }
    
    if (!$stmt->execute()) {
        echo $stmt->error;
    }

    $stmt->close();
    $db_connection->close();
}

function read_user_profile_photo($userid, $firstname, $lastname) {
    $img_html = null;

    $db_connection = connect_db();

    $stmt = $db_connection->prepare("SELECT filename FROM vp_userprofilephotos WHERE userid = ?");
    echo $db_connection->error;

    $stmt->bind_param("i", $userid);
    $stmt->bind_result($filename_db);
    $stmt->execute();

    if ($stmt->fetch()) {
        $img_html .= '<img src="' .$GLOBALS["profile_photo_upload_location"] .$filename_db .'" alt="' .$firstname ." " .$lastname ." profiilipilt" .'">' ."\n";
    }

    $stmt->close();
    $db_connection->close();

    return $img_html;
}