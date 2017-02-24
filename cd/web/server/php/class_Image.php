<?php
/*
 * Created on 27.12.2012
 *
 * Copyright by Lucius Bachmann
 * 
 */
 
 class Image{
 	public $path;
 	public $height;
 	public $width;
 	public $size;
 	public $filename;
 	public $extension;
 	public $image;
 	public $fullpath;
 	public $error=0;
 	public $errmsg;
 	
 public function __construct($index){
 	/*
 	$fileinfos=pathinfo($_FILES[$index]['tmp_name']);
 	$this->path=$fileinfos['dirname'];
 	$this->filename=$fileinfos['filename'];
 	$this->extension=$fileinfos['extension'];
 	$this->fullpath=$this->path . $this->filename . $this->extension;
 	*/
 	$this->image=new Imagick($_FILES[$index]['tmp_name']);
 	
 	if(!gettype($this->image)=='object'){
 		$this->error=1;
 		$this->errmsg="nim";
 	}
 	else{
 		/*
 		$tw=224;
 		$th=152;
 		$d = $this->image->getImageGeometry();
		$w = $d['width'];
		$h = $d['height'];
		
		if($w/$tw > $h/$th){  
 		
 		
 		$this->image->adaptiveResizeImage($tw, 0);
		}
		else{
			$this->image->adaptiveResizeImage(0, $th);
		}*/
 		
 	}
 }	
 	
 public function writeImage($path){
 
 	$this->image->setFormat("png");
 	$this->image->writeImage($path);
 	
 	
 	
 }
 	
 
 
 }
?>
