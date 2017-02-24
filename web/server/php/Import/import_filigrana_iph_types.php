<?php 
$starttime = microtime(true);
include("../include.php");

$imgDirectory = "/var/www/html/filigrana/data/iphclasses/img";
$metaFile = "/var/www/html/filigrana/data/iphclasses/iphclasses.csv";
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
		$counter = 0;
		
		echo "preparation time= ".(microtime(true)-$starttime)."\n";
		while(true){
			$socket = new SocketHandler();
			$linetime = microtime(true);
			$line=fgets($FileHandler);//Read line
			if($line === false){//If End Of file, exit
				$EndofFile=1;
				break;
			}
			$string=substr($line,0, strlen($line)-1);
			$array = explode(",", $string);
			
			$IPHType = new IPHType();
			$id=$IPHType->createNewIPHType(1, 1);
			if($id != false){
				
				$id=$IPHType->getField(IPHType::getIdField());
				
				$IPHType->addMetadata("IPH Class", $array[0]);
				$IPHType->addMetadata("description@de", $array[1]);
				/*$imgfile = $array[2];
				echo $imgfile ."\n";
				$imgfile = strtolower($imgfile);
				echo $imgfile ."\n";
				$imgfile = "$imgfile.gif";
				echo $imgfile ."\n";
				$path = "$imgDirectory/$imgfile";
				echo $path ."\n\n";*/
				if(strlen($array[2])>2){
					$array[2] = substr($array[2], 0, strlen($array[2])-1);
				}
				$path = "$imgDirectory/".strtolower($array[2]).".gif";
				echo $path ."\n";
				$date= new DateTime();
				$dateString = $date->format("d_m_Y");
				$targetpath = "/var/www/html/filigrana/web/client/img/photos/".$dateString;
				if(!is_dir($targetpath)){
					mkdir($targetpath);
				}
				
				if(is_file($path)){
					echo "it is a file, now extract it \n";
					$socket->extractIPH($path, $targetpath, false, $id);
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