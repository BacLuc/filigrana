<?php
/*
 * Created on 27.11.2013
 *
 * Author:  Lucius Bachmann
 * 
 */
 
 class Log{
 	
 	private $MySQL_Host = LOG_DB_HOST;
	private $MySQL_User = LOG_DB_USER;
	private $MySQL_Passw = LOG_DB_PASSWORD;
	private $db = LOG_DB_NAME;
	private $logtext;
	private $session_starttime;
	private $action_starttime;
	private $action;
	private $result;
	private $dblk;
	private $ses_id;
 	
 	/**
 	 * constructor, checks first if its the first Action of a Session.
 	 * If yes, it sets his values in the $_SESSION['term_log'], logs, and finally writes the action to the database
 	 * else, it adds the new action to the existing session when the action is complete
 	 * Connects with MySQL DB to log 
 	 * ATTENTION: SESSION MUST BE RUNNING
 	 * @param JSON actionString
 	 */
 	public function __construct($action=null){
 		//TEST 1, TEST 2
 		
 		if ( is_session_started() === FALSE ) session_start();
 		
		// TEST 3
		/*
		 try{
		 	
			$connectionString = "mysql:host=".$this->MySQL_Host.";dbname=".$this->db;
			$this->dblk= new PDO($connectionString , $this->MySQL_User, $this->MySQL_Passw);
			$this->dblk->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
			
		 }
		 catch(Exception $e){
		 
		 	$this->logError(__FILE__, __LINE__, "PDO Exception: ".$e->getMessage());
			
			
		 }
		*/

		 
		 if(empty($_SESSION['term_log'])){
			 //TEST 4
			 $this->logtext="";
			 $this->session_starttime=microtime(true);
			 $this->action_starttime=microtime(true);
			 $this->action=$action;
			 $this->result=null;
			/* 
			 $res=$this->dblk->query("INSERT INTO session (ses_dauer) VALUES (0)");
			 
			 if($res == "PDOERROR"){
			 	$this->logError(__FILE__, __LINE__, "Erstellen der session hat nicht geklappt im construct() der Klasse Log");
			
			 	$this->ses_id="ERROR";
			 }else{ 
			 
			 	$this->ses_id=$this->dblk->lastInsertId();
			 }*/
		}else{
			//TEST 5
			$this->logtext="";
			 $this->session_starttime=$_SESSION['term_log']['session_starttime'];
			 $this->action_starttime=microtime(true);
			 $this->action=$action;
			 $this->result=null;
			 $this->ses_id=$_SESSION['term_log']['ses_id'];
		}

 		
 		
 	}
 	
 	public function setAction($action){
 		$this->action=$action;
 	}
 	
 	
 	public  function query($query_string, $fetch=false){
		/*try{ 
		$statement=$this->dblk->query($query_string);
		if($statement==false){
			$this->logError(__FILE__, __LINE__, "SQL query hat nicht geklappt: $query_string");
			
			return "PDOERROR";
		}
		//$statement->execute();
		if($fetch){ 
			$return=$statement->fetchAll(PDO::FETCH_ASSOC);
			$statement->closeCursor();
			return $return;
		}else{
			$statement->closeCursor();
			return "PDOSUCCESS";
		}
		}
		catch(Exception $e){
			$this->logError(__FILE__, __LINE__, "PDO Exception: ".$e->getMessage());
			
			return "PDOERROR";
		}*/
		
	}
 	
 	
 	/**
 	 * addText to log steps in the algorithms
 	 */
 	public function addText($text){
 		$this->logtext.=$text;
 		
 	}
 	
 	public function addEmailToHash($email){
 		/*
 		$hash= hash('sha512',$email);
 		$res=$this->query("UPDATE session SET `ses_hash_email` = '$hash' WHERE ses_id=".$this->ses_id);
 		if($res=="PDOERROR"){
 			$this->logError(__FILE__, __LINE__, "addEmailTo Hash update der Session hat nicht geklappt");
 		
 		}*/
 	}
 	
 	
 	/**
 	 * finishs a Action and writes it to the Database
 	 * @param JSON result of the action as JSON string
 	 */
 	public function finishAction($Result){
 		$this->result=$Result;
 		$dauer=microtime(true)-$this->action_starttime;
 		/*
 		$sql="INSERT INTO log (ses_id, anfrage, text, ausgabe, dauer) VALUES (" .
 			"".$this->getSes_id().", '".$this->getAction()."', '".$this->getLogText()."', '".$this->getResult()."', $dauer)";
 	
 		$res=$this->query($sql, false);
 			
 		if($res == "PDOERROR"){
 			$this->logError(__FILE__, __LINE__, "finishAction hat nicht geklappt");
 		}
 		$sql="UPDATE session set ses_dauer = ses_dauer + $dauer WHERE ses_id=".$this->getSes_id();
 		//echo $sql;
 		$res=$this->query($sql, false);	
 		
 		if($res == "PDOERROR"){
 			$this->logError(__FILE__, __LINE__, "Update der Session dauer hat nicht geklappt in finishAction");
 		}
 		*/
 		$_SESSION['term_log']['session_starttime']=$this->getSessionStarttime();
 		$_SESSION['term_log']['ses_id']=$this->getSes_id();
 		
 	}
 	
 	
 	public static function logError($File, $Line, $message, $once=0){
 		$File=str_replace("\\", "/",$File);
 		/*$sql="INSERT INTO error (ses_id, file, line, text) VALUES (".$this->getSes_id().", '$File', $Line, '$message')";
 	
 		$res=$this->query($sql, false);
 		
 		if($res == "PDOERROR"){
 			if($once!=0){ 
 				$this->logError(__FILE__, __LINE__, "logError in SQL hat nicht geklappt", 1);
 			}
 		}*/
 		$now=new DateTime();
 		$data=$now->format("d.m.Y H:i:s")." ERROR in File $File at Line $Line : \n" .
 				"$message \n";
				
 		$file=file_put_contents("errorlog.txt", $data, FILE_APPEND);
 		
 		/*
		if(LOG_EMAIL_ERROR){  	
	 		$to="lucius.bachmann@gmx.ch";
	 		$from="lucius.bachmann@gmx.ch";
	 		$betreff ="Error Bei der Terminverwaltung";
				
			$nachricht = '<html>
				<head>
					<title>Error Bei der Terminverwaltung</title>
				</head>
				<body>
					<p>Best&auml;tigungs Email f&uuml;r ihren Termin:</p>' .
					$now->format("d.m.Y H:i:s")." ERROR in File $File at Line $Line :<br>" .
	 				"$message <br>".'	
				</body>
				</html>
				';
		
	
	
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			$headers .= "From: $from\n";
			$headers .= "Reply-to: $from\n";
			$headers .= "Return-Path: $from\n";
			$headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">\n";
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Date: " . date('r', time()) . "\n";
		
			mail($to,$betreff,$nachricht,$headers);
		}*/
 		
 	}
 	
 	
 	/*
 	 * UNIT TEST HELPERS
 	 */
 	 /**
 	  * returns the dblk objekt
 	  */
 	public function getDblk(){
 		return $this->dblk;
 	}
 	
 	public function getLogText(){
 		return $this->logtext;
 	}
 	
 	public function getAction(){
 		return $this->action;
 	}
 	
 	public function getResult(){
 		return $this->result;
 	}
 	
 	public function getSes_id(){
 		return $this->ses_id;
 	}
 	
 	public function getSessionStarttime(){
 		return $this->session_starttime;
 	}
 	
 	public function getActionStarttime(){
 		return $this->action_starttime;
 	}
 	
 	function truncateTables(){
 		$sql ="TRUNCATE TABLE pdo_test;";
 		$this->query($sql);
 		$sql ="TRUNCATE TABLE error;";
 		$this->query($sql);
 		$sql ="TRUNCATE TABLE log;";
 		$this->query($sql);
 		$sql ="TRUNCATE TABLE session;";
 		$this->query($sql);
 	}
 	

 	
 	
 	public static function unitTest(){
 		
		echo "
		-----------------------------<br>
		Testing class Log:<br>
		----------------------------<br>
		";
		
 		
 		/*--------------------------------------------------------------------
 		 * test if the session is started when no session exists
 		 */
 		
 		echo "TEST 1: SESSION startet when not active    ";
 		
 		
 		$logObj=new Log("bla");
 		

 		
 		if(session_id() != "") {
 			echo "PASSED";
 			}else{
 				echo "FAILED";
 			}
 		
 		session_unset();
		session_write_close();
		
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		
 		/*--------------------------------------------------------------------
 		 * test if the session is started when no session exists
 		 */
 		 
 		echo "TEST 2: SESSION not startet when active    ";
 		
 		
 		$logObj=new Log("bla");
 		
 		if(session_id() != "") {
 			echo "PASSED";
 			}else{
 				echo "FAILED";
 			}
 		
 		session_unset();
		session_write_close();
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		
 		
 		/*--------------------------------------------------------------------
 		 * Test if DB Connection is established when params are right
 		 */
 		echo "TEST 3: dbConnection if right params ";
 		
 		
 		$logObj=new Log("bla");
 		
 		if(get_class($logObj->dblk) === "PDO"){
 			echo "PASSED";
 		}else{
 			echo "FALSE";
 		}
 		
		session_unset();
		session_write_close();
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		/*--------------------------------------------------------------------
 		 * Test if the variables are set right
 		 */
 		echo "TEST 4: Test if the variables are set if no Session exists ";
 		$action="bla";
 		
 		$logObj=new Log($action);
 		
 		$lastKey=$logObj->getDblk()->lastInsertId();
 		
 		if($logObj->getLogText()=="" && $logObj->getSes_id() == $lastKey &&
 		is_null($logObj->getResult()) && $logObj->getAction() == $action &&
 		$logObj->getActionStarttime() <= microtime(true) && $logObj->getSessionStarttime()<= microtime(true)){
 			echo "PASSED";
 		}else{
 			echo "FAILED";
 		}
 		
		session_unset();
		session_write_close();
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		
 		/*--------------------------------------------------------------------
 		 * Test if the Variables are set When SESSION exists
 		 */
 		echo "TEST 5: Test if the variables are set right when SESSION exists   ";
 		session_start();
 		$_SESSION['term_log']['session_starttime']="2";
 		$_SESSION['term_log']['ses_id']="1";
 		
 		
 		
 		$action="bla";
 	 	$logObj=new Log($action);
 		
 		
 		
 		
 		
 		if($logObj->getLogText()=="" && $logObj->getSes_id() == "1" &&
 		is_null($logObj->getResult()) && $logObj->getAction() == $action &&
 		$logObj->getActionStarttime() <= microtime(true) && $logObj->getSessionStarttime() == 2){
 			echo "PASSED";
 		}else{
 			echo "FAILED";
 		}
 		
		session_unset();
		session_write_close();
		
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		/*--------------------------------------------------------------------
 		 * Test the query function
 		 */
 		echo "TEST 6: Test if the query function works on empty databases     ";
 		session_start();
 		 
 		 $action="bla";
 	 	$logObj=new Log($action);
 		 
 		 
 		$res=$logObj->query("DELETE FROM PDO_TEST WHERE 1");
 		
 		$res2=$logObj->query("SELECT * FROM PDO_TEST", true);
 		
 		
 		if(count($res2)==0 && $res == "PDOSUCCESS"){
 			echo "PASSED";
 		}else{
 			echo "FAILED";
 		}
 		$logObj->truncateTables();
		session_unset();
		session_write_close();
		
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		
 		echo "TEST 7: Test if the query function throws errors for wrong sql's'     ";
 		session_start();
 		 
 		 $action="bla";
 	 	$logObj=new Log($action);
 		 
 		 
 		$res=$logObj->query("ADELETE FROM PDO_TEST WHERE 1");
 		
 		$res2=$logObj->query("SEECT * FROM PDO_TEST", true);
 		
 		
 		if(!is_array($res2) && $res != "PDOSUCCESS"){
 			echo "PASSED";
 		}else{
 			echo "FAILED";
 		}
 		$logObj->truncateTables();
		session_unset();
		session_write_close();
		
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		
 		
 		echo "TEST 8: Test if the query functionworks with filled Databse     ";
 		session_start();
 		 
 		 $action="bla";
 	 	$logObj=new Log($action);
 		 
 		 
 		$res=$logObj->query("DELETE FROM PDO_TEST");
 		
 		$res3=$logObj->query("INSERT INTO PDO_TEST(text) VALUES('hallo')");
 		
 		$res2=$logObj->query("SELECT * FROM PDO_TEST", true);

 		
 		if(count($res2) == 1 && $res == "PDOSUCCESS" && $res3 == "PDOSUCCESS"){
 			echo "PASSED";
 		}else{
 			echo "FAILED";
 		}
 		$logObj->truncateTables();
		session_unset();
		session_write_close();
		
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		//------------------------------------------------------------------------------------------
 		
 		
 		
 		echo "TEST 9: test the addText function     ";
 		session_start();
 		 
 		 $action="bla";
 	 	$logObj=new Log($action);
 		 $text="hallo";
 		$logObj->addText("hallo");
 		
 		if($logObj->getLogText() == $text){
 			echo "PASSED";
 		}else{
 			echo "FAILED";
 		}
 		$logObj->truncateTables();
		session_unset();
		session_write_close();
		
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		
 			//------------------------------------------------------------------------------------------
 		
 		
 		
 		echo "TEST 10: test the addEmailToHash function     ";
 		session_start();
 		 
 		 $action="bla";
 	 	$logObj=new Log($action);
 		 $email="a@a.com";
 		$hash= hash('sha512',$email);
		$res=$logObj->query("SELECT * FROM session WHERE ses_hash_email = '$hash'", true);
		$alteAnzahl=count($res);		 		
 		$logObj->addEmailToHash($email);
 		$res=$logObj->query("SELECT * FROM session WHERE ses_hash_email = '$hash'", true);
		$neueAnzahl=count($res);		 		
 		
 		
 		if( $alteAnzahl+1==$neueAnzahl){
 			echo "PASSED";
 		}else{
 			echo "FAILED";
 		}
 		$logObj->truncateTables();
		session_unset();
		session_write_close();
		
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		//------------------------------------------------------------------------------------------
 		
 		
 		
 		echo "TEST 11: test the finishAction function     ";
 		session_start();
 		 
 		 $action="bla";
 	 	$logObj=new Log($action);
 		$result="result";
 		
 		$res=$logObj->query("SELECT * FROM log WHERE ses_id=".$logObj->getSes_id(), true);
		$alteAnzahl=count($res);
 		$logObj->finishAction($result);		 		
 		$res=$logObj->query("SELECT * FROM log WHERE ses_id=".$logObj->getSes_id(), true);
		$neueAnzahl=count($res);
 		
 		
 		if( $alteAnzahl+1==$neueAnzahl && $logObj->getResult() == $result ){
 			echo "PASSED";
 		}else{
 			echo "FAILED";
 		}
 		
 		
 		$logObj->truncateTables();
		session_unset();
		session_write_close();
		
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		echo "TEST 12: test the logError function     ";
 		session_start();
 		 
 		 $action="bla";
 	 	$logObj=new Log($action);
 		$result="result";
 		
 		$res=$logObj->query("SELECT * FROM error WHERE ses_id=".$logObj->getSes_id(), true);
 		$alteAnzahl=count($res);
		$logObj->logError( __FILE__, __LINE__, "testError");		 		
 		$res=$logObj->query("SELECT * FROM error WHERE ses_id=".$logObj->getSes_id(), true);
		$neueAnzahl=count($res);
 		
 		if( $alteAnzahl+1==$neueAnzahl){
 			echo "PASSED";
 		}else{
 			echo "FAILED";
 		}
 		$logObj->truncateTables();
		session_unset();
		session_write_close();
		
 		echo "<br>";
 		//------------------------------------------------------------------------------------------
 		
 		
 		
 		
 		
 	}
 }
 
 

 
 /*
  function is_session_started()
{
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}*/
 
?>
