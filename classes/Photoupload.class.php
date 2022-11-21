<?php

class Photoupload {
    private $photo;
    private $image_type;
    private $temp_image;
    private $photo_file_size_limit = 1.5 * 1024 * 1024;

    public $filename;
    public $thumbnail_photo;
    public $normal_photo;

    public $photo_name_prefix = "vp_";
    public $normal_photo_max_w = 800;
    public $normal_photo_max_h = 450;
    public $thumbnail_max_w = 100;
    public $thumbnail_max_h = 100;

    public $error = null;

    function __construct($photo) {
        $this->photo = $photo;
        $this->check_file_type();

        if (empty($this->error)) {
            if ($this->photo["size"] >= $this->photo_file_size_limit) {
                $this->error = "Valitud fail on liiga suur!";
            }
        }
    }

    function __destruct() {
        imagedestroy($this->normal_photo);
        imagedestroy($this->temp_image);
        imagedestroy($this->thumbnail_photo);
    }

    private function check_file_type() {
        $this->image_type = "maasikas";
        $image_check = getimagesize($this->photo["tmp_name"]);
    
        if ($image_check) {
            if ($image_check["mime"] == "image/jpeg") {
                $this->image_type = "jpg";
            } else if ($image_check["mime"] == "image/png") {
                $this->image_type = "png";
            } else if ($image_check["mime"] == "image/gif") {
                $this->image_type = "gif";
            }
        }
    
        if ($this->image_type == "maasikas") {
            $this->error = "Valitud fail pole sobivat tüüpi!";
        }
    }
    
    private function create_filename() {
        $timestamp = microtime(1) * 10000;
        $this->filename = $this->photo_name_prefix .$timestamp ."." .$this->image_type;
    }
    
    public function create_image() {
        if (!empty($this->error)) return;
        
        $this->temp_image = null;
    
        if ($this->image_type == "jpg") {
            $this->temp_image = imagecreatefromjpeg($this->photo["tmp_name"]);
        } else if ($this->image_type == "png") {
            $this->temp_image = imagecreatefrompng($this->photo["tmp_name"]);
        } else if ($this->image_type == "gif") {
            $this->temp_image = imagecreatefromgif($this->photo["tmp_name"]);
        }

        if (empty($this->temp_image)) {
            $this->error = "Pildi loomisel läks midagi valesti.";
        }

        $this->create_filename();
    }
    
    public function resize_photos() {
        if (!empty($this->error)) return;

        $image_w = imagesx($this->temp_image);
        $image_h = imagesy($this->temp_image);
        $new_w = $this->normal_photo_max_w;
        $new_h = $this->normal_photo_max_h;
    
        // säilitan originaalproportsioonid
        if ($image_w / $this->normal_photo_max_w > $image_h / $this->normal_photo_max_h) {
            $new_h = round($image_h / ($image_w / $this->normal_photo_max_w));
        } else {
            $new_w = round($image_w / ($image_h / $this->normal_photo_max_h));
        }
    
        $temp_photo = imagecreatetruecolor($new_w, $new_h);

        // sailitame vajadusel labipaistvuse (png ja gif jaoks)
        imagesavealpha($temp_photo, true);
        $trans_color = imagecolorallocatealpha($temp_photo, 0, 0, 0, 127);
        imagefill($temp_photo, 0, 0, $trans_color);
    
        // teeme originaalist väiksele koopia
        imagecopyresampled($temp_photo, $this->temp_image, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);
    
        $this->normal_photo = $temp_photo;

        $this->resize_photo_thumbnail();
    }
    
    private function resize_photo_thumbnail() {
        if (!empty($this->error)) return;

        $image_w = imagesx($this->temp_image);
        $image_h = imagesy($this->temp_image);

        $temp_photo = imagecreatetruecolor($this->thumbnail_max_w, $this->thumbnail_max_h);

        // sailitame vajadusel labipaistvuse (png ja gif jaoks)
        imagesavealpha($temp_photo, true);
        $trans_color = imagecolorallocatealpha($temp_photo, 0, 0, 0, 127);
        imagefill($temp_photo, 0, 0, $trans_color);

        imagecopyresampled($temp_photo, $this->temp_image, 0, 0, 0, 0, $this->thumbnail_max_w, $this->thumbnail_max_h, $image_w, $image_h);
    
        $this->thumbnail_photo = $temp_photo;
    }
    
    public function save_photos($normal_target, $thumbnail_target, $original_target) {
        if (!empty($this->error)) return;

        $is_normal_saved = false;
        $is_thumbnail_saved = false;
        
        if ($this->image_type == "jpg") {
            $is_normal_saved = imagejpeg($this->normal_photo, $normal_target .$this->filename, 95);
            $is_thumbnail_saved = imagejpeg($this->thumbnail_photo, $thumbnail_target .$this->filename, 95);
        } else if ($this->image_type == "png") {
            $is_normal_saved = imagepng($this->normal_photo, $normal_target .$this->filename, 6);
            $is_thumbnail_saved = imagepng($this->thumbnail_photo, $thumbnail_target .$this->filename, 6);
        } else if ($this->image_type == "gif") {
            $is_normal_saved = imagegif($this->normal_photo, $normal_target .$this->filename);
            $is_thumbnail_saved = imagegif($this->thumbnail_photo, $thumbnail_target .$this->filename);
        }

        if (!$is_normal_saved) {
            $this->error = "Normaalpildi salvestamine ebaõnnestus!";
        }
        if (!$is_thumbnail_saved) {
            $this->error .= "Thumbnaili salvestamine ebaõnnestus!";
        }

        if (empty($this->error)) {
            $this->save_original_photo($original_target);
        }
    }

    private function save_original_photo($target) {
        if (!move_uploaded_file($this->photo["tmp_name"], $target .$this->filename)) {
            $this->error = "Originaalpildi salvestamine ebaõnnestus!";
        }
    }
}