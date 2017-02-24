<?php 
$starttime = microtime(true);
include("../include.php");

$imgDirectory = "/var/www/html/filigrana/data/watermarks/img";
$metaFile = "/var/www/html/filigrana/data/watermarks/watermarks.csv";
if(is_file($metaFile)){
	if(is_dir($imgDirectory)){
		$FileHandler = fopen($metaFile, 'r');
		$line=fgets($FileHandler);//Read line
		if($line === false){//If End Of file, exit
			$EndofFile=1;
			break;
		}
		
		//$pointer=ftell($FileHandler);//get the offset of the filecursor
		$string=substr($line,0, strlen($line)-1);
		
		$array = explode(",", $string);
		foreach($array as $key => &$value){
				$array[$key]=str_ireplace('"', '', $value);
			}
		print_r($array);
		$counter = 0;
		
		echo "preparation time= ".(microtime(true)-$starttime)."\n";
		while(true){
			//$socket = new SocketHandler();
			$linetime = microtime(true);
			$line=fgets($FileHandler);//Read line
			if($line === false){//If End Of file, exit
				$EndofFile=1;
				break;
			}
			$string=substr($line,0, strlen($line)-1);
			$array = explode(',"', $string);
			
			foreach($array as $key => &$value){
				$array[$key]=str_ireplace('"', '', $value);
			}
			
			$Watermark = new Watermark();
			//$id=$Watermark->createNewWatermark(1, 1);
			if($id != false || false){
				
				$id=$Watermark->getField(Watermark::getIdField());
				
				if(trim($array[0]) != ""){ 
					$Watermark->addMetadata("motiv", $array[0]);
				}
				if(trim($array[1]) != ""){ 
					$Watermark->addMetadata("IPH Class", $array[1]);
				}
				if(trim($array[2]) != ""){ 
					$Watermark->addMetadata("description@de", $array[2]);
				}
				if(trim($array[4]) != ""){ 
					$Watermark->addMetadata("institution_sign", $array[4]);
				}
				if(trim($array[5]) == ""){ 
					$Watermark->addMetadata("reference@de", $array[5]);
				}
				if(trim($array[6]) == ""){ 
					$Watermark->addMetadata("position_description@de", $array[6]);
				}
				if(trim($array[7]) == ""){ 
					$Watermark->addMetadata("contained_in@de", $array[7]);
				}
				if(trim($array[8]) == ""){ 
					$Watermark->addMetadata("used_from", $array[8]);
				}
				if(trim($array[9]) == ""){ 
					$Watermark->addMetadata("used_until", $array[9]);
				}
				if(trim($array[10]) == ""){ 
					$Watermark->addMetadata("historical_name@de", $array[10]);
				}
				if(trim($array[12]) == ""){ 
					$Watermark->addMetadata("city@de", $array[12]);
				}
				if(trim($array[13]) == ""){ 
					$Watermark->addMetadata("country@de", $array[13]);
				}
				
				
					
				
				
				/*
				$imgfile = $array[16];
				echo $imgfile ."\n";
				$imgfile = strtolower($imgfile);
				echo $imgfile ."\n";
				$imgfile = "$imgfile.gif";
				echo $imgfile ."\n";
				$path = "$imgDirectory/$imgfile";
				echo $path ."\n\n";*/
				/*if(strlen($array[2])>2){
					$array[2] = substr($array[2], 0, strlen($array[2])-1);
				}*/
				
				$path = "$imgDirectory/".strtolower($array[3]);
				echo $path ."\n";
				$date= new DateTime();
				$dateString = $date->format("d_m_Y");
				$targetpath = "/var/www/html/filigrana/web/client/img/photos/".$dateString;
				if(!is_dir($targetpath)){
					mkdir($targetpath);
				}
				
				if(is_file($path)){
					echo "it is a file, now extract it \n";
					//$socket->extractIPH($path, $targetpath, false, $id);
				}
			}
			
			print_r($array);
			
			
			$counter++;
			echo "line time: ".(microtime(true)-$linetime)."\n";
		}
		
		echo "total time= ".(microtime(true)-$starttime)."\n";
	}else{
		echo "imgdir was no directory \n";
	}
}else{
	echo "Metafile was no File \n";
}



?>
