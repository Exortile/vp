<?php

require_once "fnc_general.php";
require_once "../../config.php";

function count_photos($privacy){
    $photo_count = 0;
    $conn = connect_db();
    $stmt = $conn->prepare("SELECT COUNT(id) FROM vp_photos WHERE privacy >= ? AND deleted IS NULL");
    echo $conn->error;
    $stmt->bind_param("i", $privacy);
    $stmt->bind_result($count_from_db);
    $stmt->execute();
    if($stmt->fetch()){
        $photo_count = $count_from_db;
    }
    $stmt->close();
    $conn->close();
    return $photo_count;
}

function count_own_photos(){
    $photo_count = 0;
    $conn = connect_db();
    $stmt = $conn->prepare("SELECT COUNT(id) FROM vp_photos WHERE userid = ? AND deleted IS NULL");
    echo $conn->error;
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->bind_result($count_from_db);
    $stmt->execute();
    if($stmt->fetch()){
        $photo_count = $count_from_db;
    }
    $stmt->close();
    $conn->close();
    return $photo_count;
}

function read_public_photo_thumbs($privacy, $page, $limit) {
    $limit_skip = ($page - 1) * $limit;
    $db_connection = connect_db();

    # LIMIT x - mitu naidata
    # LIMIT x,y - mitu vahele jätta, mitu näidata
    $stmt = $db_connection->prepare("SELECT vp_photos.filename, vp_photos.alttext, vp_users.firstname, vp_users.lastname FROM vp_photos JOIN vp_users ON vp_photos.userid = vp_users.id WHERE vp_photos.privacy >= ? AND vp_photos.deleted IS NULL GROUP BY vp_photos.id ORDER BY vp_photos.id DESC LIMIT ?,?");
    echo $db_connection->error;

    $stmt->bind_param("iii", $privacy, $limit_skip, $limit);
    $stmt->bind_result($filename_db, $alttext_db, $firstname_db, $lastname_db);
    $stmt->execute();

    $img_html = null;

    while ($stmt->fetch()) {
        # <img src="pildi url" alt="alteranatiivtekst">

        $img_html .= '<div class="thumbgallery">' ."\n";

        if (empty($alttext_db)) {
            $alttext = "Galeriipilt";
        } else {
            $alttext = $alttext_db;
        }

        $img_html .= '<img src="' .$GLOBALS["thumbnail_upload_location"] .$filename_db .'" alt="' .$alttext .'" ';
        $img_html .= 'class="thumbs">' ."\n";
        $img_html .= "<p>" .$firstname_db ." " .$lastname_db ."</p>\n";
        $img_html .= "</div>\n";
    }

    $stmt->close();
    $db_connection->close();

    return $img_html;
}

function read_own_photo_thumbs($page, $limit) {
    $limit_skip = ($page - 1) * $limit;
    $db_connection = connect_db();

    # LIMIT x - mitu naidata
    # LIMIT x,y - mitu vahele jätta, mitu näidata
    $stmt = $db_connection->prepare("SELECT id, filename, alttext, privacy FROM vp_photos WHERE userid = ? AND deleted IS NULL ORDER BY id DESC LIMIT ?,?");
    echo $db_connection->error;

    $stmt->bind_param("iii", $_SESSION["user_id"], $limit_skip, $limit);
    $stmt->bind_result($id_db, $filename_db, $alttext_db, $privacy_db);
    $stmt->execute();

    $img_html = null;

    while ($stmt->fetch()) {
        # <img src="pildi url" alt="alteranatiivtekst">

        $img_html .= '<div class="thumbgallery">' ."\n";

        if (empty($alttext_db)) {
            $alttext = "Galeriipilt";
        } else {
            $alttext = $alttext_db;
        }

        $img_html .= '<img src="' .$GLOBALS["thumbnail_upload_location"] .$filename_db .'" alt="' .$alttext .'" ';
        $img_html .= 'class="thumbs">' ."\n";
        $img_html .= '<p><a href="edit_photo_data.php?id=' .$id_db .'">Muuda</a>' ."</p>\n";
        $img_html .= "</div>\n";
    }

    $stmt->close();
    $db_connection->close();

    return $img_html;
}

function read_latest_public_photo() {
    $privacy = 3; // avalik foto
    $limit = 1; // uks pilt
    
    $db_connection = connect_db();

    $stmt = $db_connection->prepare("SELECT vp_photos.id, vp_photos.alttext, vp_users.firstname, vp_users.lastname FROM vp_photos JOIN vp_users ON vp_photos.userid = vp_users.id WHERE vp_photos.privacy >= ? AND vp_photos.deleted IS NULL GROUP BY vp_photos.id ORDER BY vp_photos.id DESC LIMIT ?");
    echo $db_connection->error;

    $stmt->bind_param("ii", $privacy, $limit);
    $stmt->bind_result($id_db, $alttext_db, $firstname_db, $lastname_db);
    $stmt->execute();

    $img_html = null;

    if ($stmt->fetch()) {
        // <img src="pildi url" alt="alteranatiivtekst">
        // <img src="show_public_photo.php?photo=7" alt="alteranatiivtekst">

        if (empty($alttext_db)) {
            $alttext = "Galeriipilt";
        } else {
            $alttext = $alttext_db;
        }

        // $img_html .= '<img src="' .$GLOBALS["normal_upload_location"] .$filename_db .'" alt="' .$alttext .'">' ."\n";
        $img_html .= '<img src="show_public_photo.php?photo=' .$id_db .'" alt="' .$alttext .'">' ."\n";
        $img_html .= "<p>" .$firstname_db ." " .$lastname_db ."</p>\n";
    }

    $stmt->close();
    $db_connection->close();

    return $img_html;
}