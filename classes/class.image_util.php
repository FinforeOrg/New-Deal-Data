<?php
/*********
created by: sng
date: 4/august/2009
*********/
class image_util{
	/*****
	The thumbnail may not fit the required dimension, in which case, a padding is applied. The colour of the padding can be specified.
	*****/
	var $padding_red=254;
	var $padding_green=249;
	var $padding_blue=227;
	//var $pad = true;
	//by default, padding is not done
	var $pad = false;
	
	/*********************************
	sng:18/may/2012
	*********
	We should only accept images of known types. What we do is store the types (file extensions)
	in an array and refer to it when we check the file name
	*************/
	var $img_type_arr = array('jpg'=>'jpeg image','gif'=>'gif image','png'=>'png image');
	
	/***
	function to set padding colour
	we specify the r,g,b code (0-255) for the padding colour
	***/
	function enable_padding($padding_red=254,$padding_green=249,$padding_blue=227){
		$this->padding_red = $padding_red;
		$this->padding_green = $padding_green;
		$this->padding_blue = $padding_blue;
		$this->pad = true;
	}
	
	/*****************
	sng:18/may/2012
	check the extension against our valid image types
	The img_file can be a file name or path and file name
	The get_file_extension() is defined in nifty_functions.php
	Remember, do NOT give the tmp name of the uploaded image. When PHP
	upload a file, the uploaded file is given a name like php4597.tmp
	***/
	function is_valid_image_file($img_file){
		$name = basename($img_file);
		$extension_name = get_file_extension($name);
		if(!array_key_exists($extension_name,$this->img_type_arr)){
			return false;
		}else{
			return true;
		}
	}
	
	/******************************
	sng:7/may/2012
	Function to create resized version of an image uploaded.
	
	src_path_img: path to the source image alongwith the image name
	dest_path: path to the destination folder, without the image name
	name: name of the image
	
	
	fit_width: the max width of the thumbnail
	fit_height: the max height of the thumbnail
	
	pad: whether to create resized image of fit_width and fit_height, keeping the aspect ratio
	and placing padding if needed
	
	return true is thumbnail is created and false is there is a problem
	*/
	function create_resized($src_path_img,$dest_path,$name,$fit_width,$fit_height,$pad = true){
		///////////////////////////////////////
		//basic validation
		//echo $path."<br>image:==".$image."<br>dest:==".$dest."<br>"; die;
		/***********
		sng:18/may/2012
		************/
		if(!$this->is_valid_image_file($name)){
			return false;
		}
		if($fit_width == 0){
			return false;
		}
		if($fit_height == 0){
			return false;
		}
		////////////////////////////////
		$path_img = $src_path_img;
		$img_info = getimagesize($path_img);
		if(!$img_info){
			return false;
		}
		$tmp_wt = $img_info[0];
		$tmp_ht = $img_info[1];
		//the scaling
		$new_w = $tmp_wt;
		$new_h = $tmp_ht;
		//fit width
		if($tmp_wt > $fit_width){
			$new_w = $fit_width;
			$new_h = floor(($tmp_ht*$fit_width)/$tmp_wt);
		}
		//now check height. If it is a fit then no prob otherwise scale on height
		if($new_h > $fit_height){
			$new_w = floor(($new_w*$fit_height)/$new_h);
			//now set new_ht
			$new_h = $fit_height;
		}
		/////////////////////////////////////////
		
		$img_mime = $img_info['mime'];
		if($img_mime=="image/gif"){
			if($pad){
				$thumb = imagecreate($fit_width,$fit_height);
			}else{
				$thumb = imagecreate($new_w,$new_h);
			}
			
		}else{
			if($pad){
				$thumb = imagecreatetruecolor($fit_width,$fit_height);
			}else{
				$thumb = imagecreatetruecolor($new_w,$new_h);
			}
		}
		//image create true color does not work with gif
		if(!$thumb){
			return false;
		}
		////////////////////////////////////////////////////////////////////////////
		if($pad){
			//put padding colour
			$pad_colour = @imagecolorallocate($thumb,$this->padding_red,$this->padding_green,$this->padding_blue);
			if(!$pad_colour){
				//do nothing
			}else{
				@imagefill($thumb,0,0,$pad_colour);
				//if error we cannot do a thing, but we proceed with what we have
			}
		}
		/////////////////////////////////////////////////////////////////////////////
		$src_img = NULL;
		if($img_mime=="image/jpeg"){
			$src_img = imagecreatefromjpeg($path_img);
			if(!$src_img){
				return false;
			}
		}
		if($img_mime=="image/gif"){
			$src_img = imagecreatefromgif($path_img);
			if(!$src_img){
				return false;
			}
		}
		if($img_mime=="image/png"){
			$src_img = imagecreatefrompng($path_img);
			if(!$src_img){
				return false;
			}
		}
		if($src_img==NULL){
			return false;
		}
		//////////////////////////////////////////////////////////
		
		
		if($pad){
			//$success = imagecopyresampled($thumb,$src_img,($fit_width-$new_w)/2,($fit_height-$new_h)/2,0,0,$new_w,$new_h,$tmp_wt,$tmp_ht);
			$success = imagecopyresampled($thumb,$src_img,($fit_width-$new_w)/2,($fit_height-$new_h),0,0,$new_w,$new_h,$tmp_wt,$tmp_ht);
		}else{
			$success = imagecopyresampled($thumb,$src_img,0,0,0,0,$new_w,$new_h,$tmp_wt,$tmp_ht);
		}
		if(!$success){
			return false;
		}
		////////////////////////////////////////////////////////////////////////////////
		
		$img_file = $dest_path."/".$name;
		
		//save
		if($img_mime=="image/jpeg"){
			$success = imagejpeg($thumb,$img_file);
			imagedestroy($thumb);
			imagedestroy($src_img);
			if(!$success){
				return false;
			}else{
				return true;
			}
		}
		if($img_mime=="image/gif"){
			$success = imagegif($thumb,$img_file);
			imagedestroy($thumb);
			imagedestroy($src_img);
			if(!$success){
				return false;
			}else{
				return true;
			}
		}
		if($img_mime=="image/png"){
			$success = imagepng($thumb,$img_file);
			imagedestroy($thumb);
			imagedestroy($src_img);
			if(!$success){
				return false;
			}else{
				return true;
			}
		}
		////////////////////////
		return false;
	}
	/**
	Function to create thumbnail of an image dynamically. The thumbnail is kept in the folder specified by dest. The name
	of the thumbnail is same as the original image name. However, if the dest is same as path, then the image name is th_<image>
	path: path to the original image
	image: name of the image.
	fit_width: the max width of the thumbnail
	fit_height: the max height of the thumbnail
	dest: Destination folder
	return true is thumbnail is created and false is there is a problem
	*/
	function create_thumbnail($path,$image,$fit_width,$fit_height,$dest,$pad = true){
		///////////////////////////////////////
		//basic validation
		//echo $path."<br>image:==".$image."<br>dest:==".$dest."<br>"; die;
		/***********
		sng:18/may/2012
		************/
		if(!$this->is_valid_image_file($image)){
			return false;
		}
		if($fit_width == 0){
			return false;
		}
		if($fit_height == 0){
			return false;
		}
		////////////////////////////////
		$path_img = $path."/".$image;
		$img_info = getimagesize($path_img);
		if(!$img_info){
			return false;
		}
		$tmp_wt = $img_info[0];
		$tmp_ht = $img_info[1];
		//the scaling
		$new_w = $tmp_wt;
		$new_h = $tmp_ht;
		//fit width
		if($tmp_wt > $fit_width){
			$new_w = $fit_width;
			$new_h = floor(($tmp_ht*$fit_width)/$tmp_wt);
		}
		//now check height. If it is a fit then no prob otherwise scale on height
		if($new_h > $fit_height){
			$new_w = floor(($new_w*$fit_height)/$new_h);
			//now set new_ht
			$new_h = $fit_height;
		}
		/////////////////////////////////////////
		
		$img_mime = $img_info['mime'];
		if($img_mime=="image/gif"){
			if($pad){
				$thumb = imagecreate($fit_width,$fit_height);
			}else{
				$thumb = imagecreate($new_w,$new_h);
			}
			
		}else{
			if($pad){
				$thumb = imagecreatetruecolor($fit_width,$fit_height);
			}else{
				$thumb = imagecreatetruecolor($new_w,$new_h);
			}
		}
		//image create true color does not work with gif
		if(!$thumb){
			return false;
		}
		////////////////////////////////////////////////////////////////////////////
		if($pad){
			//put padding colour
			$pad_colour = @imagecolorallocate($thumb,$this->padding_red,$this->padding_green,$this->padding_blue);
			if(!$pad_colour){
				//do nothing
			}else{
				@imagefill($thumb,0,0,$pad_colour);
				//if error we cannot do a thing, but we proceed with what we have
			}
		}
		/////////////////////////////////////////////////////////////////////////////
		$src_img = NULL;
		if($img_mime=="image/jpeg"){
			$src_img = imagecreatefromjpeg($path_img);
			if(!$src_img){
				return false;
			}
		}
		if($img_mime=="image/gif"){
			$src_img = imagecreatefromgif($path_img);
			if(!$src_img){
				return false;
			}
		}
		if($img_mime=="image/png"){
			$src_img = imagecreatefrompng($path_img);
			if(!$src_img){
				return false;
			}
		}
		if($src_img==NULL){
			return false;
		}
		//////////////////////////////////////////////////////////
		
		
		if($pad){
			//$success = imagecopyresampled($thumb,$src_img,($fit_width-$new_w)/2,($fit_height-$new_h)/2,0,0,$new_w,$new_h,$tmp_wt,$tmp_ht);
			$success = imagecopyresampled($thumb,$src_img,($fit_width-$new_w)/2,($fit_height-$new_h),0,0,$new_w,$new_h,$tmp_wt,$tmp_ht);
		}else{
			$success = imagecopyresampled($thumb,$src_img,0,0,0,0,$new_w,$new_h,$tmp_wt,$tmp_ht);
		}
		if(!$success){
			return false;
		}
		////////////////////////////////////////////////////////////////////////////////
		if($dest == $path){
			$thumb_name = "th_".$image;
		}else{
			$thumb_name = $image;
		}
		$img_file = $dest."/".$thumb_name;
		
		//save
		if($img_mime=="image/jpeg"){
			$success = imagejpeg($thumb,$img_file);
			imagedestroy($thumb);
			imagedestroy($src_img);
			if(!$success){
				return false;
			}else{
				return true;
			}
		}
		if($img_mime=="image/gif"){
			$success = imagegif($thumb,$img_file);
			imagedestroy($thumb);
			imagedestroy($src_img);
			if(!$success){
				return false;
			}else{
				return true;
			}
		}
		if($img_mime=="image/png"){
			$success = imagepng($thumb,$img_file);
			imagedestroy($thumb);
			imagedestroy($src_img);
			if(!$success){
				return false;
			}else{
				return true;
			}
		}
		////////////////////////
		return false;
	}
	
	/****
	function to upload an image
	****/
	function upload_image($path,$name,$src_img){
		/***********
		sng:18/may/2012
		************/
		if(!$this->is_valid_image_file($name)){
			return false;
		}
		$dest_path = $path."/".$name;
		$success = move_uploaded_file($src_img,$dest_path);
		return $success;
	}
}
$g_img = new image_util();
?>