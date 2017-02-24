<?php

class SocketHandler{
	private $socket;
	private $address = "localhost";
	
	
	function __construct(){
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
			chdir("/var/www/html/filigrana/web/server");
			exec("java -jar filigranaAPI.jar > testfile.txt &");
			sleep(4);
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if($socket === false){
				die("socket_create() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error()) . "\n");
			}
			
			
			
		}
		$port = SOCKET_PORT;
		$result = @socket_connect($socket, $this->address, $port );
		if ($result === false) {
			chdir("/var/www/html/filigrana/web/server");
			exec("java -jar filigranaAPI.jar > testfile.txt &");   
			sleep(4);
			$result = socket_connect($socket, $this->address, $port );
			if($result === false){
				die("socket_create() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error()) . "\n");
			}
			
				
			}
		
		$this->socket=$socket;
		
		
		
	}
	
	function __destruct(){
		
	}
	
	function extractWM($path,$targetFolder, $sketch = false,$id = -1){
		if($sketch){
			$sketch = "1";
		}else{
			$sketch = "0";
		}
		$array= array(
				"action" => "extractWM",
				"pathToExtract" => $path,
				"targetFolder" => $targetFolder,
				"id" => $id,
				"sketch" => $sketch
		);
		
		$returnArray = $this->send($array);
		return $returnArray;
	}
	
	function extractIPH($path,$targetFolder, $sketch = false,$id = -1){
		if($sketch){
			$sketch = "1";
		}else{
			$sketch = "0";
		}
		$array= array(
				"action" => "extractIPH",
				"pathToExtract" => $path,
				"targetFolder" => $targetFolder,
				"id" => $id , 
				"sketch" => $sketch
		);
	
		$returnArray = $this->send($array);
		return $returnArray;
	}
	
	function retrieveWM($path, $picid = ""){
		$array= array(
				"action" => "retrieveWM",
				"path" => $path,
				"picid" => $picid
		);
	
		$returnArray = $this->send($array);
		return $returnArray;
	}
	
	function retrieveIPH($path, $picid = ""){
		$array= array(
				"action" => "retrieveIPH",
				"path" => $path,
				"picid" => $picid
		);
	
		$returnArray = $this->send($array);
		return $returnArray;
	}
	
	
	function send($array){
		
		$json = json_encode($array);
		$port = SOCKET_PORT;
		socket_write($this->socket, $json, strlen($json));
		socket_write($this->socket, ';', 1);
		//socket_close($this->socket);
		$returnString = "";
		while ($out = socket_read($this->socket, 2048)) {
			$returnString.=$out;
		}
		//echo $returnString;
		$returnArray = json_decode($returnString);
		if($returnArray == null){
			echo json_last_error();
		}
		if($returnArray->check == "suc"){
			if(isset($returnArray->result)){
				return $returnArray->result;
			}else{
				return array("check" => "suc");
			}
		}
		return array("check" => "ser");
	}
	
	static function unitTest(){
		/*
		echo "Testing extract Watermark \n";
		$sh = new SocketHandler();
		$result = $sh->extractWM("/var/www/html/filigrana/web/client/img/test/testwm.jpg", "/var/www/html/filigrana/web/client/img/test/extracted");
		var_dump($result);
		*/
		echo "Testing extract Iph Type \n";
		$sh = new SocketHandler();
		$result = $sh->extractIPH("/var/www/html/filigrana/web/client/img/test/testimagetmp.png", "/var/www/html/filigrana/web/client/img/test/extracted");
		var_dump($result);
		/*
		echo "Testing retrieve Wm \n";
		$sh = new SocketHandler();
		$result = $sh->retrieveWM("/var/www/html/filigrana/web/client/img/test/testsketch.jpg");
		var_dump($result);
		
		echo "Testing retrieve IPH \n";
		$sh = new SocketHandler();
		$result = $sh->retrieveIPH("/var/www/html/filigrana/web/client/img/test/testsketch.jpg");
		var_dump($result);
		*/
		
	}
	
	
	
	
}

//SocketHandler::unitTest();


