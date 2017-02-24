<?php
/*
 * Created on 06.11.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class DB{
 	protected $Log;
 	protected $dblk;
	private $MySQL_Host = DB_HOST;
	private $MySQL_Port = DB_PORT;
	private $MySQL_User = DB_USER;
	private $MySQL_Passw = DB_PASSWORD;
	private $dbname = DB_NAME;
	private $tablename = "";
	private $id;
	private $idfield;
	private $isPostgresql = true;
	protected  $Fields;
	
	/**
	 * constructs the abstract DB class, which holds the common sql queries
	 * 
	 * @param Log $Log
	 */
	
 	function __construct(){
 		//$connectionString = "pgsql:host=".$this->MySQL_Host.";dbname=".$this->dbname.";port=".$this->MySQL_Port;
		$this->dblk= getDblk();// PDO($connectionString , $this->MySQL_User, $this->MySQL_Passw);
		//$this->dblk->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

 	}
	
 	/**
 	 * executes an sql query and returns an array in the structure result[0] = first row of the query in assosiative array
 	 * @param String SQL query $query_string
 	 * @param boolean $fetch
 	 * @return string "PDOSUCCES" if fetch == false, an array of the result if fetch == true, and "PDOERROR" if something went wrong.
 	 * Attention, if the table not exists, it hangs up and uses all the memory
 	 */
  function query($query_string, $fetch=false){
		try{
		$statement=$this->dblk->query($query_string);//execute the query
		if($statement==false){
			//$this->Log->logError(__FILE__, __LINE__, "SQL query hat nicht geklappt: $query_string");
			
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
			//$this->Log->logError(__FILE__, __LINE__, "PDO Exception: ".$e->getMessage());
			
			return "PDOERROR";
		}
		
	}
	
	/**
	 * Generates the Insert SQL query
	 * @param String $table tablename
	 * @param Array $Key_Values of key value pairs, atttention, the keys have to mach sql columns
	 * @return string the generatet SQL query
	 */
	function insert($table, $Key_Values){
		$sqlfields = "INSERT INTO $table (";
		$sqlvalues = "VALUES(";
		$first = 1;
		foreach($Key_Values as $key => $value){
			if($first){
				$sqlfields .= $key;
				if(is_string($value)){
					$sqlvalues .= "'$value'";
				}else{
					$sqlvalues .= "$value";
				}
				$first = false;
			}else{
				$sqlfields .= ",".$key;
				if(is_string($value)){
					$sqlvalues .= ",'$value'";
				}else{
					$sqlvalues .= ",$value";
				}
			}
		}
		$sqlfields.=")";
		$sqlvalues.=")";
		$sql = $sqlfields.$sqlvalues;
		return $sql;
		
	}
	
	/**
	 * Inserts a row into the specified query. Automatically inserts timestamps
	 * @param String $table tablename
	 * @param Array $Key_Values of key value pairs, atttention, the keys have to mach sql columns
	 * @return string "PDOSUCCESS" if successful, "PDOERROR" if not succesful
	 */
	function insertPreparedStatement($table, $Key_Values, $idfield){
		$ret = false;
		
		try{ 
			//make the two parts of an insert statement
			$sqlfields = "INSERT INTO $table (";
			$sqlvalues = "VALUES(";
			$first = 1;
			foreach($Key_Values as $key => $value){
				if($first){
					$sqlfields .= $key;
					
					$sqlvalues .= "?";
					
					$first = false;
				}else{
					
					$sqlfields .= ",".$key;
					if(!(strpos($key, "insert_timestamp")===false)){
						$sqlvalues .= ",NOW()";
					}else{
					
						$sqlvalues .= ",?";
					}
				}
			}
			$sqlfields.=")";
			$sqlvalues.=")";
			//concat them
			$sql = $sqlfields.$sqlvalues;
			if(isset($idfield) && $this->isPostgresql){
				$sql .= " RETURNING ".$idfield;
			}
	
			$statement = $this->dblk->prepare($sql);
			$counter = 1;
		
			//bin the parameters
			foreach($Key_Values as $key => &$value){
				if(!(strpos($key, "insert_timestamp")===false)){
					
				}else{
					$statement->bindParam($counter, $value);
					
					$counter++;
				}
			}
			//execute the statement, 00000 is returned if Success
	
			$res = $statement->execute();
			$ret = $statement->fetchColumn();
		
			if($statement->errorCode() != '00000'){
				$this->handlePDOError($statement, __FILE__, __LINE__);
				return "PDOERROR";
			}
		}
		catch(Exception $e){
			$this->Log->logError(__FILE__, __LINE__, "Exception: ".$e->getMessage());
			
			return "PDOERROR";
		}
		$statement->closeCursor();
		if(isset($idfield) && $this->isPostgresql){
			
			return $ret;
		}else{
			return $this->dblk->lastInsertId();
		}
	}
	
	/**
	 * 
	 * @param PDOStatement $statement
	 * @param string $File
	 * @param string $Line
	 */
	static function handlePDOError(PDOStatement &$statement, $File, $Line){
		$info = "";//get the error informations
		$error = $statement->errorInfo();
		foreach($error as $error){
			$info.=$error;
		}
		Log::logError($File, $Line, "Exception: ".$info);
		$statement->closeCursor();
	}
	
	/**
	 * executes an SQL insert statement, returns last insert ID on succes and PDOERROR on failure
	 * @param String $sql the sql query
	 * @return string|boolean
	 */
	function executeInsert($sql){
		if($this->query($sql) == "PDOSUCCESS"){
			return $this->dblk->lastInsertId();
		}else{
			return false;
		}
	}
	
	/**
	 * get the row with the specified id of the table $tablename
	 * @param String $tablename
	 * @param string $idField
	 * @param int $id
	 * @return the row as assosiative array|boolean false on failure
	 */
	function getById($tablename, $idField, $id){
		$sql = "SELECT * FROM $tablename WHERE $idField = $id";

		$result = $this->query($sql, true);

		if($result != "PDOERROR"){
			$this->Fields = $result[0];
			return $result;
		}else{
			return false;
		}
		
	}
	
	/**
	 * get the row with the specified id of the table $tablename with prepared statement
	 * @param String $tablename
	 * @param string $idField
	 * @param int $id
	 * @return the row as assosiative array|boolean false on failure
	 */
	function getByIdPrepared($tablename, $idField, $id){
		$sql = "SELECT * FROM $tablename WHERE $idField = ?";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $id);
		$statement->execute();
		if($statement->errorCode() != '00000'){
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	
		$this->Fields = $result[0];
		return $result;
		
	
	}
	/**
	 * set field of an instance
	 * @param string $key
	 * @param unknown $value
	 */
	function setField($key, $value){
		$this->Fields[$key]= $value;
	}
	
	/**
	 * get Field of an instance
	 * @param string $key
	 * @return unknown|boolean = false on failure
	 */
	function getField($key){
		if(isset($this->Fields[$key])){
			return $this->Fields[$key];
		}else{
			return false;
		}
	}
	/**
	 * get all Fields of instance
	 * @return assosiative array 
	 */
	function getFields(){
		return $this->Fields;
	}
	
	/**
	 * Updates Field in Database
	 * @param string $tablename
	 * @param string $idfield
	 * @param int $id
	 * @param string $key
	 * @param string $value
	 * @return boolean
	 */
	function updateField($tablename, $idfield, $id,$key, $value){
		$sql = "UPDATE $tablename SET $key = ? WHERE $idfield = ?";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $value);
		$statement->bindParam(2, $id);
		$statement->execute();
		if($statement->errorCode() != '00000'){
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		else{
			return true;
		}
	}
	
	/**
	 * Updates Fields in Database
	 * @param string $tablename
	 * @param string $idfield
	 * @param int $id
	 * @param array $Key_Values of key value paires, attention, keys have to match sql columns
	 * @return boolean
	 */
	function updateFields($tablename, $idfield, $id,$Key_Values){
		$first = true;
		$sql = "UPDATE $tablename SET ";
		foreach($Key_Values as $key => $value){
			if($first){
				$sql .= $key." = ?";
					
				$first = false;
			}else{
					
				$sql .= ",".$key." = ?";
			}
		} 
		
		
		$sql.=" WHERE $idfield = ?";
		
		$statement = $this->dblk->prepare($sql);
		
		$counter =1;
		foreach($Key_Values as $key => &$value){
			$statement->bindParam($counter, $value);
			$counter++;
		}
		
		
		$statement->bindParam($counter, $id);
		$statement->execute();
		if($statement->errorCode() != '00000'){
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		else{
			return true;
		}
	}

	function textQuery($query){
		$sql = "select type, other_id,met_id, value, char_length(value) as length FROM (
				  SELECT 'iph' as type, imx_met_id as met_id, imx_iph_id as other_id, imx_value as value
				  FROM iph_met
				  WHERE lower(imx_value) LIKE '%' ||lower(?)|| '%'
				  UNION
				  SELECT 'wm' as type, wmx_met_id as met_id,wmx_wmk_id as other_id, wmx_value as value
				  FROM wmk_met
				  WHERE lower(wmx_value) LIKE '%' ||lower(?)|| '%'
			) as q ORDER BY length LIMIT 100;";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $query);
		$statement->bindParam(2, $query);
		$statement->execute();
		if($statement->errorCode() != '00000'){
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		
		return $result;
		
		
	}
	
	/**
	 * sends a mail by the given parameters
	 * @param unknown $from
	 * @param unknown $to
	 * @param unknown $validation_id
	 * @param unknown $email_type
	 */
	function send_mail($from,$to,$validation_id,$email_type)
	{
		$from=_EMAIL_FROM;
		if($email_type == "validation"){
			//ErrorLog::LogError('user.php',259,'Validation: http://commcon.de/dario_labs/smbtp/src/php/validation.php?bla=knofzelbri&saecke='.$validation_id.'@1231224ddr45|4art789@;548877');
			$betreff ="Invitation to Filigrana WebApp";
	
			$nachricht = '<html>
		<head>
			<title>Invitation to Filigrana WebApp</title>
		</head>
		<body>
			<p>Welcome Filigrana WebApp</p>
			<p>You are invited to use the Filigrana WebApp. With this link you can validate your email an choose your username and password.</p>
			<p><a href="'.HOSTNAME.'/#register_page&hash=2561dddas@8f89d8cv7djdf8gfns544321asdf95645@'.$validation_id.'@1231224ddr45|4art789@;548877" title="Validation" style="font-size: 11pt; color: #999999; text-decoration: none; border: none;">Set username and password to use Filigrana WebApp</a></p>
			<p>Filigrana WebApp - Team</p>
		</body>
		</html>
		';
		}
		elseif($email_type == "passwordlink"){
			//ErrorLog::LogError('user.php',259,'Validation: http://commcon.de/dario_labs/smbtp/src/php/validation.php?bla=knofzelbri&saecke='.$validation_id.'@1231224ddr45|4art789@;548877');
			$betreff ="Reset password of Filigrana WebApp";
	
			$nachricht = '<html>
		<head>
			<title>Reset password of Filigrana WebApp</title>
		</head>
		<body>
			<p>You can choose a new password with this link.</p>
			<p><a href="'.HOSTNAME.'/#set_password_page&hash=2561dddas@8f89d8cv7djdf8gfns544321asdf95645@'.$validation_id.'@1231224ddr45|4art789@;548877" title="Set password" style="font-size: 11pt; color: #999999; text-decoration: none; border: none;">Set a new password for Filigrana WebApp</a></p>
			<p>Filigrana WebApp - Team</p>
		</body>
		</html>
		';
		}
	
	
		// fuer HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
		$headers .= "From: $from\n";
		$headers .= "Reply-to: $from\n";
		$headers .= "Return-Path: $from\n";
		$headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Date: " . date('r', time()) . "\n";
	
		mail($to,$betreff,$nachricht,$headers);
	}
	
	
	
	
	
	
	
	
	
	/*------------------------------------------------
	UNIT TEST HELPERS
	 * */
	 function getLog(){
	 	return $this->Log;
	 }
	 
	 function getDblk(){
	 	return $this->dblk;
	 }
	 
	 
	 
	 function truncateTable($tablename){
	 	if($this->isPostgresql){
	 		$sql="TRUNCATE TABLE $tablename CASCADE";
	 	}else{
		 	$sql ="
		 	SET FOREIGN_KEY_CHECKS = 0; 		
		 	TRUNCATE TABLE $tablename ;
		 	SET FOREIGN_KEY_CHECKS = 1;"
	 		;
	 	}
	 	$this->query($sql);
	 }
	
	
	
	static function unitTest(){
		echo "
		-----------------------------<br>
		Testing class DB:<br>
		----------------------------<br>
		";
		
		
		/*--------------------------------------------------------------------
 		 * Test if DB Connection is established when params are right
 		 */
 		echo "TEST 1: Test Constructor ";
 		
 		
		$DB = new DB();
 		
 		if(get_class($DB) === "DB" && get_class($DB->getDblk()) === "PDO"){
 			echo "PASSED \n\n";
 		}else{
 			echo "FAILED \n\n";
 		}
 		
 		unset($DB);
		echo "<br>";
		
		
		/*--------------------------------------------------------------------
 		 * Test if SQL String is made right
 		 */
 		echo "TEST 2: Test insert query creation ";
 		
 		
		$DB = new DB();
		
		$sql = $DB->insert("testtable", array("testcolumn1" => "testtext", "testcolumn2" => 2));

 		if($sql == "INSERT INTO testtable (testcolumn1,testcolumn2)VALUES('testtext',2)"){
 			echo "PASSED \n";
 		}else{
 			echo "FAILED \n";
 		}
 		
 		unset($DB);
		echo "<br>";
		
		
		
		
		
		
		/*--------------------------------------------------------------------
 		 * Test if SQL insert works
 		 */
 		echo "TEST 3: TTest if SQL insert works ";
 		
 		
		$DB = new DB();
		
		$sql = $DB->insert("testtable", array("testcolumn1" => "testtext", "testcolumn2" => 2));
		
		$id = $DB->executeInsert($sql);
		
		$result = $DB->getById("testtable", "id", $id);
		
 		if(count($result) == 1){
 			echo "PASSED \n";
 		}else{
 			echo "FAILED \n";
 		}
		$DB->truncateTable("testtable");
 		
 		unset($DB);
		echo "<br>";
		
		/*--------------------------------------------------------------------
 		 * Test if SQL insert with Prepared Statement works
 		 */
 		echo "TEST 4: TTest if SQLinsert with Prepared Statement works ";
 		
 		
		$DB = new DB();
		
		$id = $DB->insertPreparedStatement("testtable", array("testcolumn1" => "testtext", "testcolumn2" => 2), "id");
		
		$result = $DB->getById("testtable", "id", $id);

 		if(count($result) == 1){
 			echo "PASSED \n";
 		}else{
 			echo "FAILED \n";
 		}
		$DB->truncateTable("testtable");
 		
 		unset($DB);
		echo "<br>";
		
		/*--------------------------------------------------------------------
		 * Test if getById funktioniert with Prepared Statement works
		*/
		echo "TEST 5: Test if Fields are saved right in getFieldById ";
			
		
		$DB = new DB();
		
		$id = $DB->insertPreparedStatement("testtable", array("testcolumn1" => "testtext", "testcolumn2" => 2), "id");
		
		$result = $DB->getById("testtable", "id", $id);
		$sql = "Select * from testtable where id = $id";
		
		$ressql = $DB->query($sql, true);

		
		if(count($result) == 1 && $ressql[0]['testcolumn2'] == $DB->getField('testcolumn2') && $ressql[0]['testcolumn1'] == $DB->getField('testcolumn1') ){
			
				echo "PASSED \n";
		}else{
			echo "FAILED \n";
		}
		$DB->truncateTable("testtable");
		
		unset($DB);
		echo "<br>";
		
		
		echo "TEST 6: Test if Fields are saved right in getFieldByIdPrepared ";
			
		
		$DB = new DB();
		
		$id = $DB->insertPreparedStatement("testtable", array("testcolumn1" => "testtext", "testcolumn2" => 2), "id");
		
		$result = $DB->getByIdPrepared("testtable", "id", $id);
		$ressql = $DB->query("Select * from testtable where id = $id", true);
		
		
		if(count($result) == 1 && $ressql[0]['testcolumn2'] == $DB->getField('testcolumn2') && $ressql[0]['testcolumn1'] == $DB->getField('testcolumn1') ){
				
			echo "PASSED \n";
		}else{
			echo "FAILED \n";
		}
		$DB->truncateTable("testtable");
		
		unset($DB);
		echo "<br>";
		
		
		echo "TEST 7: Test if Fields are saved right in getFieldById ";
			
		
		$DB = new DB();
		
		$id = $DB->insertPreparedStatement("testtable", array("testcolumn1" => "testtext", "testcolumn2" => 2), "id");
		
		$result = $DB->getById("testtable", "id", $id);
		$ressql = $DB->query("Select * from testtable where id = $id", true);
		
		
		if(count($result) == 1 && $ressql[0]['testcolumn2'] == $DB->getField('testcolumn2') && $ressql[0]['testcolumn1'] == $DB->getField('testcolumn1') ){
				
			echo "PASSED \n";
		}else{
			echo "FAILED \n";
		}
		$DB->truncateTable("testtable");
		
		unset($DB);
		echo "<br>";
		
		
		echo "TEST 8: Test if Fields updateField works ";
			
		
		$DB = new DB();
		
		$id = $DB->insertPreparedStatement("testtable", array("testcolumn1" => "testtext", "testcolumn2" => 2), "id");
		
		$DB->updateField("testtable", "id", $id, "testcolumn1", "testvalue");
		$DB->setField("testcolumn1", "testvalue");
		$DB->setField("testcolumn2", 2);
		$ressql = $DB->query("Select * from testtable where id = $id", true);
	
		if(count($result) == 1 && $ressql[0]['testcolumn2'] == $DB->getField('testcolumn2') && $ressql[0]['testcolumn1'] == $DB->getField('testcolumn1') ){
		
			echo "PASSED \n";
		}else{
			echo "FAILED \n";
		}
		$DB->truncateTable("testtable");
		
		unset($DB);
		echo "<br>";
		
		echo "TEST 9: Test if Fields updateFields works ";
			
		
		$DB = new DB();
		
		$id = $DB->insertPreparedStatement("testtable", array("testcolumn1" => "testtext", "testcolumn2" => 2), "id");
		
		$DB->updateFields("testtable", "id", $id, array("testcolumn1" => "testvalue", "testcolumn2" => 3));
		$DB->setField("testcolumn1", "testvalue");
		$DB->setField("testcolumn2", 3);
		$ressql = $DB->query("Select * from testtable where id = $id", true);
		
		
		if(count($result) == 1 && $ressql[0]['testcolumn2'] == $DB->getField('testcolumn2') && $ressql[0]['testcolumn1'] == $DB->getField('testcolumn1') ){
		
			echo "PASSED \n";
		}else{
			echo "FAILED \n";
		}
		$DB->truncateTable("testtable");
		
		unset($DB);
		echo "<br>";
		
		
		
		
	}
	
	
	
	
	
 }
 
 
?>
