<?php
//Upload ver 1.7
/////////////////////////////////////////////////////////////////////////
function upload_my_file($upload_dir, $upfile_id, $target_filename, $allowed_types){
	$uploaddir = $upload_dir;
	//chmod ($uploaddir, 0766);
	
	if(Is_Allowed_FileType($allowed_types, $upfile_id)) {
		$uploadfile = $uploaddir.$target_filename.".".Get_File_type($upfile_id['name']);
		if (move_uploaded_file($upfile_id['tmp_name'], $uploadfile)) {
			return $GLOBALS['MyErrStr']->Uploded;
		} else {
			return $GLOBALS['MyErrStr']->CannotUpload;
		}
	} else {
		return $GLOBALS['MyErrStr']->ErrFileType;
	}
}

function Get_File_type($FileName){
	$mFn=explode(".", $FileName);
	$rtrn=$mFn[count($mFn)-1];
	return strtolower($rtrn);
}

function Get_File_name($FileName){
	$mFn=explode(".", $FileName);
	return $mFn[0];
}

function Is_Allowed_FileType($AllowedFileTypes, $upfile_id){
	if ($AllowedFileTypes=="") {
		return true;
	}elseif (eregi($AllowedFileTypes."$",strtolower($upfile_id['name']))) {
		return true;
	}else {
		return false;	
	}
}

function createThumbnail($originalimage, $thumbDirectory, $imageName, $thumbWidth, $square=false, $thumbHeight=0)
{
//Getting Image Type & Creating Image using proper function.
$imgtype=Get_File_type($originalimage);
switch (trim($imgtype)) {
	case "jpg":
		$srcImg = imagecreatefromjpeg($originalimage);	
	break;
	case "png":
		$srcImg = imagecreatefrompng($originalimage);	
	break;
	case "gif":
		$srcImg = imagecreatefromgif($originalimage);	
	break;
	case "bmp":
		$srcImg = imagecreatefromwbmp($originalimage);	
	break;
	default:
		return ;	
	break;
}

//Getting Original Width & Height
$origWidth = imagesx($srcImg);
$origHeight = imagesy($srcImg);

//Calculating New Height
if ($square) {
	$thumbHeight = $thumbWidth;
}elseif ($thumbHeight>0){
	$thumbHeight=$thumbHeight;
}else {
	/*if ($origWidth>$origHeight) {
		$ratio = $origWidth / $thumbWidth;
		$thumbHeight = $origHeight * $ratio;
	}else{
		$thumbHeight = $thumbWidth;
		$ratio = $origHeight / $thumbHeight;
		$thumbWidth = $origWidth * $ratio;
	}*/
	$ratio = ($origWidth / $thumbWidth);
	$thumbHeight = $origHeight / $ratio;
}

//Creating Resized Image
$thumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight);
imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, imagesx($srcImg), imagesy($srcImg));

//Saving new thumb
switch ($imgtype) {
	case "jpg":
	 	file_exists("$thumbDirectory/$imageName.jpg") ? unlink("$thumbDirectory/$imageName.jpg") : $a="ok";
		return imagejpeg($thumbImg, "$thumbDirectory/$imageName.jpg");	
	break;
	case "png":
		file_exists("$thumbDirectory/$imageName.png") ? unlink("$thumbDirectory/$imageName.png") : $a="ok";
		return imagepng($thumbImg, "$thumbDirectory/$imageName.png");	
	break;
	case "gif":
		file_exists("$thumbDirectory/$imageName.gif") ? unlink("$thumbDirectory/$imageName.gif") : $a="ok";
		return imagegif($thumbImg, "$thumbDirectory/$imageName.gif");	
	break;
	case "bmp":
		file_exists("$thumbDirectory/$imageName.bmp") ? unlink("$thumbDirectory/$imageName.bmp") : $a="ok";
		return imagewbmp($thumbImg, "$thumbDirectory/$imageName.bmp");	
	break;
	default:
		return ;	
	break;
}
}

function createZoom($originalimage, $thumbDirectory, $imageName, $areaWidth, $areaHeight)
{
//Getting Image Type & Creating Image using proper function.
$imgtype=Get_File_type($originalimage);
switch (trim($imgtype)) {
	case "jpg":
		$srcImg = imagecreatefromjpeg($originalimage);	
	break;
	case "png":
		$srcImg = imagecreatefrompng($originalimage);	
	break;
	case "gif":
		$srcImg = imagecreatefromgif($originalimage);	
	break;
	case "bmp":
		$srcImg = imagecreatefromwbmp($originalimage);	
	break;
	default:
		return ;	
	break;
}
//Getting Original Width & Height
$origWidth = imagesx($srcImg);
$origHeight = imagesy($srcImg);

//Calculating New Size
	//1-Step one: Resize to fit area width
		$thumbWidth=$areaWidth;
		$thumbHeight=($thumbWidth * $origHeight) / $origWidth;
		
	//2-Step two: If neccessary resize to fit area height
		if ($thumbHeight > $areaHeight) {
			$thumbHeight=$areaHeight;
			$thumbWidth=($thumbHeight * $origWidth) / $origHeight;
		}

//Creating Resized Image
$thumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight);
imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, imagesx($srcImg), imagesy($srcImg));

//Saving new thumb
switch ($imgtype) {
	case "jpg":
		file_exists("$thumbDirectory/$imageName.jpg") ? unlink("$thumbDirectory/$imageName.jpg") : $a="ok";
		return imagejpeg($thumbImg, "$thumbDirectory/$imageName.jpg", 100);	
	break;
	case "png":
		file_exists("$thumbDirectory/$imageName.png") ? unlink("$thumbDirectory/$imageName.png") : $a="ok";
		return imagepng($thumbImg, "$thumbDirectory/$imageName.png");	
	break;
	case "gif":
		file_exists("$thumbDirectory/$imageName.gif") ? unlink("$thumbDirectory/$imageName.gif") : $a="ok";
		return imagegif($thumbImg, "$thumbDirectory/$imageName.gif");	
	break;
	case "bmp":
		file_exists("$thumbDirectory/$imageName.bmp") ? unlink("$thumbDirectory/$imageName.bmp") : $a="ok";
		return imagewbmp($thumbImg, "$thumbDirectory/$imageName.bmp");	
	break;
	default:
		return ;	
	break;
}
}

function merg_two_pics($mainphoto, $mask, $tofile)
{
 //soource image change accordingly
$image = imagecreatefromjpeg($mainphoto);      

//overlay image change accordingly
$overlay = imagecreatefromgif($mask);

// Get X and Y size of existing image
$width = imagesx($image);
$height = imagesy($image);

// Get X and Y size of overlay
$width_o = imagesx($overlay);
$height_o = imagesy($overlay);


//Set X & Y  of overlay image
$new_width = $width_o ;
$new_height = ($height * ($new_width/$width)) ;


//Get destination coordinates
$dst_w = round(($width_o - $new_width)/2);
$dst_h = round(($height_o - $new_height)/2);
                             
//create new image
$image_resized = imagecreatetruecolor($width_o, $height_o);

//allocate color
$bg = imagecolorallocate($image_resized,255,255,255);

//fill image with white background
imagefilledrectangle($image_resized, 0, 0, $width_o , $height_o , $bg);

//copy source image to new image
imagecopyresampled($image_resized, $image, $dst_w, $dst_h , 0, 0, $new_width, $new_height, $width, $height);

//copy new image to overlay
imagecopy($image_resized, $overlay, 0,0,0,0,$width_o,$height_o);

if (imagejpeg($image_resized, $tofile)){
	return true;
}
return false;
}
?>