<?php

require_once "../../config.php";

$id = null;
$type = "image/png";
$output = "pics/wrong.png";

if (isset($_GET["photo"]) and !empty($_GET["photo"])) {
    $id = filter_var($_GET["photo"], FILTER_VALIDATE_INT);
}

if (!empty($id)) {
    require_once "fnc_general.php";
    $privacy = 3;

    $db_connection = connect_db();

    $stmt = $db_connection->prepare("SELECT filename FROM vp_photos WHERE id = ? AND privacy = ? AND deleted IS NULL");
    echo $db_connection->error;

    $stmt->bind_param("ii", $id, $privacy);
    $stmt->bind_result($filename_from_db);
    $stmt->execute();

    if ($stmt->fetch()) {
        $output = $normal_upload_location .$filename_from_db;
        $check = getimagesize($output);
        $type = $check["mime"];
    }

    $stmt->close();
    $db_connection->close();
}

// väljastan pildi
header("Content-type: " .$type);
readfile($output);