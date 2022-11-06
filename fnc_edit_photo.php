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