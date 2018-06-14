<?php
	function resizeImage($image,$new_width,$dir = "."){
		list($width,$height,$type) = getimagesize($image["tmp_name"]);
		$new_height = round($height*$new_width/$width);
		$emp_img = imagecreatetruecolor($new_width,$new_height);
		switch($type){
			case IMAGETYPE_JPEG:
				$new_image = imagecreatefromjpeg($image["tmp_name"]);
				break;
			case IMAGETYPE_GIF:
				$new_image = imagecreatefromgif($image["tmp_name"]);
				break;
			case IMAGETYPE_PNG:
				imagealphablending($emp_img, false);
				imagesavealpha($emp_img, true);
				$new_image = imagecreatefrompng($image["tmp_name"]);
				break;
		}
		imagecopyresampled($emp_img,$new_image,0,0,0,0,$new_width,$new_height,$width,$height);
		$date = date("YmdHis");
		switch($type){
			case IMAGETYPE_JPEG:
				imagejpeg($emp_img,$dir."/".$date.".jpg");
				break;
			case IMAGETYPE_GIF:
				$bgcolor = imagecolorallocatealpha($new_image,0,0,0,127);
				imagefill($emp_img, 0, 0, $bgcolor);
				imagecolortransparent($emp_img,$bgcolor);
				imagegif($emp_img,$dir."/".$date.".gif");
				break;
			case IMAGETYPE_PNG:
				imagepng($emp_img,$dir."/".$date.".png");
				break;
		}
		imagedestroy($emp_img);
		imagedestroy($new_image);
	}