<?php

function check_file_type($image_file) {
    $image_type = "maasikas";
    $image_check = getimagesize($image_file);

    if ($image_check) {
        if ($image_check["mime"] == "image/jpeg") {
            $image_type = "jpg";
        } else if ($image_check["mime"] == "image/png") {
            $image_type = "png";
        } else if ($image_check["mime"] == "image/gif") {
            $image_type = "gif";
        }
    }

    return $image_type;
}

function create_filename($prefix, $file_type) {
    $timestamp = microtime(1) * 10000;
    return $prefix .$timestamp ."." .$file_type;
}

function create_image($file, $file_type) {
    $temp_image = null;

    if ($file_type == "jpg") {
        $temp_image = imagecreatefromjpeg($file);
    } else if ($file_type == "png") {
        $temp_image = imagecreatefrompng($file);
    } else if ($file_type == "gif") {
        $temp_image = imagecreatefromgif($file);
    }

    return $temp_image;
}

function resize_photo($temp_photo, $normal_photo_max_w, $normal_photo_max_h) {
    $image_w = imagesx($temp_photo);
    $image_h = imagesy($temp_photo);
    $new_w = $normal_photo_max_w;
    $new_h = $normal_photo_max_h;

    // säilitan originaalproportsioonid
    if ($image_w / $normal_photo_max_w > $image_h / $normal_photo_max_h) {
        $new_h = round($image_h / ($image_w / $normal_photo_max_w));
    } else {
        $new_w = round($image_w / ($image_h / $normal_photo_max_h));
    }

    $temp_image = imagecreatetruecolor($new_w, $new_h);

    // teeme originaalist väiksele koopia
    imagecopyresampled($temp_image, $temp_photo, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);

    return $temp_image;
}

function resize_photo_thumbnail($temp_photo) {
    $image_w = imagesx($temp_photo);
    $image_h = imagesy($temp_photo);
    $temp_image = imagecreatetruecolor(100, 100);
    imagecopyresampled($temp_image, $temp_photo, 0, 0, 0, 0, 100, 100, $image_w, $image_h);

    return $temp_image;
}

function save_photo($image, $target, $file_type) {
    $is_saved = false;
    
    if ($file_type == "jpg") {
        $is_saved = imagejpeg($image, $target, 95);
    } else if ($file_type == "png") {
        $is_saved = imagepng($image, $target, 6);
    } else if ($file_type == "gif") {
        $is_saved = imagegif($image, $target);
    }

    return $is_saved;
}

?>