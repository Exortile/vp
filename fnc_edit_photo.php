<?php

require_once "../../config.php";
require_once "fnc_general.php";

function read_own_photo_data($id) {
    $photo_data = [];
    $db_connection = connect_db();

    $stmt = $db_connection->prepare("SELECT filename, alttext, privacy FROM vp_photos WHERE id = ?");
    echo $db_connection->error;

    $stmt->bind_param("i", $id);
    $stmt->bind_result($filename_db, $alttext_db, $privacy_db);
    $stmt->execute();

    if ($stmt->fetch()) {
        $photo_data["filename"] = $filename_db;
        $photo_data["alt"] = $alttext_db;
        $photo_data["privacy"] = $privacy_db;
    }

    $stmt->close();
    $db_connection->close();

    return $photo_data;
}

function modify_own_photo_data($alt, $privacy, $photo_id, $session_userid) {
    $hasPermission = true;
    
    $db_connection = connect_db();

    $userstmt = $db_connection->prepare("SELECT userid FROM vp_photos WHERE id = ?");
    echo $db_connection->error;

    $userstmt->bind_param("i", $photo_id);
    $userstmt->bind_result($userid_db);
    $userstmt->execute();

    if ($userstmt->fetch()) {
        if ($userid_db == $session_userid) {
            $userstmt->close();
            
            $stmt = $db_connection->prepare("UPDATE vp_photos SET alttext = ?, privacy = ? WHERE id = ?");
            echo $db_connection->error;

            $stmt->bind_param("sii", $alt, $privacy, $photo_id);
            $stmt->execute();
            echo $stmt->error;

            $stmt->close();
        } else {
            $userstmt->close();
            $hasPermission = false;
        }
    } else {
        $userstmt->close();
    }
    
    $db_connection->close();

    return $hasPermission;
}

function delete_own_photo($photo_id, $session_userid) {
    $hasPermission = true;

    $db_connection = connect_db();

    $userstmt = $db_connection->prepare("SELECT userid FROM vp_photos WHERE id = ?");
    echo $db_connection->error;

    $userstmt->bind_param("i", $photo_id);
    $userstmt->bind_result($userid_db);
    $userstmt->execute();

    if ($userstmt->fetch()) {
        if ($userid_db == $session_userid) {
            $userstmt->close(); 

            $stmt = $db_connection->prepare("UPDATE vp_photos SET deleted = now() WHERE id = ?");
            echo $db_connection->error;

            $stmt->bind_param("i", $photo_id);
            $stmt->execute();
            echo $stmt->error;

            $stmt->close();
        } else {
            $userstmt->close();
            $hasPermission = false;
        }
    } else {
        $userstmt->close();
    }

    $db_connection->close();

    return $hasPermission;
}