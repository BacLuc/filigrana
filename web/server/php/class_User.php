<?php
class User extends DB{
	private  $tablename = "users";
	private  $idfield = "usr_id";
	protected  $Fields;
	protected  $Log;

	
	static function getTablename(){
		return "users";
	}
	
	static function getIdField(){
		return "usr_id";
	}
	
	function __construct(){
		parent::__construct();
	}
	
	
	/**
	 * creates a Person and a User with the given values
	 * $Values['prename'] = $prename;
		$Values['surname'] = $surname;
		$Values['email'] = $email;
		$Values['phone'] = $phone;
		$Values['shortname'] = $shortname;
		$Values['type'] = $type;
	 * @param array $Values
	 * @param int $creator_id
	 * @return boolean|int
	 */
	function createUser($Values, $creator_id){
	
		//TODO discuss creator ID;
		
			
		
			//create Hash
		
			$validation_id = hash('sha512',$Values['email'].time().'normal');
			
			
		
			//insert User
			
			
			$insertArray = array(
					"usr_email" => $Values['email'],
					"usr_validation_status" => 'false',
					"usr_validation_hash" => $validation_id
					
			);
		
			$id=$this->insertPreparedStatement($this->tablename, $insertArray, $this->getIdField());
			
			
			
			if($id != "PDOERROR"){
					
				$insertArrayRel = array(
						"uur_usr_id" => $id,
						"uur_uro_id" => 1,
						"uur_reg_id" => $Values['researchGroup']
				);
				
				$id2 = $this->insertPreparedStatement("usr_uro_reg", $insertArrayRel, "uur_reg_id" );
				if($id2 != "PDOERROR"){
					$this->Fields = $insertArray;
					$this->Fields['usr_ID'] = $id;
					$this->send_mail(null, $Values['email'], $validation_id, "validation");
					return $id;
				}
				
				
				
				
				
			}
			
			
		return false;
	}
	
	
	
	
	function editUser($Key_Values){
		$updateArray = array();
		if(isset($Key_Values['username'])){
			$updateArray['usr_username']=$Key_Values['username'];
		}
		if(isset($Key_Values['email'])){
			$updateArray['usr_email'] = $Key_Values['email'];
		}
		
		
		
		
		if($this->updateFields($this->getTablename(), $this->getIdField(), $this->getField($this->getIdField()), $updateArray))
		{
			foreach($updateArray as $key => $value){
				$this->setField($key, $value);
			}
			return true;
		}else{
			return false;
		}
	}
	
	
	function getUserByEmail($email){
		$statement = $this->dblk->prepare("SELECT * FROM users WHERE usr_email = ?");
		
		$statement->bindParam(1, $email);
		$statement->execute();
		$res=$statement->fetchAll(PDO::FETCH_ASSOC);

		if(count($res)==1){
			$this->Fields = $res[0];
			return $this->Fields;
		}else{
			return false;
		}
		
	}
	
	function getUserWithRole($usr_id, $reg_id){
		$statement = $this->dblk->prepare("SELECT * FROM users JOIN usr_uro_reg ON usr_id=uur_usr_id WHERE usr_id = ? AND uur_reg_id = ?");
		
		$statement->bindParam(1, $usr_id);
		$statement->bindParam(2, $reg_id);
		$statement->execute();
		$res=$statement->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($res)==1){
			$this->Fields = $res[0];
			return $this->Fields;
		}else{
			return false;
		}
	}
	
	/**
	 * 
	 * @param unknown $password
	 * @return boolean
	 */
	function setUserPassword($password){
		$salt = '';
		$hash = hashMe($password, $salt);
		$updateArray = array("usr_pw_hash" => $hash, 
							"usr_pw_salt" => $salt
		);
		
		if($this->updateFields($this->getTablename(), $this->getIdField(), $this->getField($this->getIdField()), $updateArray)){
			$this->setField("usr_pw_hash", $updateArray["usr_pw_hash"]);
			$this->setField("usr_pw_salt", $updateArray['usr_pw_salt']);
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 
	 * @param unknown $password
	 * @return boolean|NULL null if User is not set
	 */
	function passwordMatch($password){
		if($this->getField("usr_pw_hash") && $this->getField("usr_pw_salt")){
			$salt = $this->getField("usr_pw_salt");
			$hash = hashMe($password, $salt);
	
			if($hash === $this->getField("usr_pw_hash")){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function getValidationStatus(){
		return $this->getField("usr_validation_status");
	}
	
	function setValidated(){
		$updateArray = array(
				"usr_validation_hash" => "",
				"usr_validation_status" => true
		);
	
		if($this->updateFields($this->getTablename(), $this->getIdField(), $this->getField($this->getIdField()), $updateArray)){
			$this->setField("usr_validation_hash", "");
			$this->setField("usr_validation_status", true);
			return true;
		}else{
			return false;
		}
	}
	
	function validationMatch($validation_id){
		if($this->getField("usr_validation_hash") == $validation_id && $validation_id != ""){
			return true;
		}else{
			return false;
		}
	}
	
	function getEmailMatchingHash($hash){
		
		
		
		$sql = "SELECT usr_email FROM users WHERE usr_validation_hash = ?";
		
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $hash);
		$statement->execute();
		if($statement->errorCode() != '00000'){
			
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		if(count($result) != 1){
			return false;
		}else{
			return $result[0]['usr_email'];
		}
	}
	
	
	/**
	 * NOt needed
	 * @param unknown $usr_id
	 * @param unknown $editor_id
	 */
	function deleteUser($usr_id, $editor_id){
		/*
		$User = new User($this->Log);
		$User->getById(User::getTablename(), User::getIdField(), $usr_id);
		if(parent::deletePerson($User->getField("usr_per_id"), $editor_id)){
			return $this->updateFields(User::getTablename(), User::getIdField(), $usr_id, array("usr_deleted"=> 1, "usr_last_editor_ID" => $editor_id));
		}else{
			return false;
		}*/
	}
	
	function removeUserFromResearchGroup($reg_id){
		$statement=$this->dblk->prepare("DELETE FROM usr_uro_reg WHERE uur_usr_id = ? AND uur_reg_id = ?");
		$statement->bindParam(1, $this->getField(User::getIdField()));
		$statement->bindParam(2, $reg_id);
		$res = $statement->execute();
		if($statement->errorCode() != '00000'){
				$this->handlePDOError($statement, __FILE__, __LINE__);
				return false;
		}
		else{
			return true;
		}
	}
	
	function insertUserIntoResearchGroup($reg_id){
		
		$insertArray = array(
				"uur_usr_id" => $this->getField(User::getIdField()),
				"uur_uro_id" => 1,
				"uur_reg_id" => $reg_id
		);
		$res = $this->insertPreparedStatement("usr_uro_reg", $insertArray, "uur_usr_id");
		
		if($res == "PDOERROR"){
			return false;
		}
		else{
			return true;
		}
	}
	
	function sendPasswordLink($email){
		if($this->getField("per_email")){
			$email = $this->getField("per_email");
		}
		
		$validation_id = hash('sha512',$email.time().'normal');
		if($this->updateFields(User::getTablename(), User::getIdField(), 
				$this->getField(User::getIdField()), 
				array("usr_validation_hash" => $validation_id))){
			
			$this->send_mail(null, $email, $validation_id, "passwordlink");
			return true;
		}else{
			return false;
		}
		
		
		
	}
	
	function getResearchGroups($usr_id){
		if($this->getField($this->getIdField())){
			$usr_id = $this->getField($this->getIdField());
		}
		
		$sql = "SELECT uur_reg_id FROM usr_uro_reg WHERE uur_usr_id = ?";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $usr_id);
		$statement->execute();
		if($statement->errorCode() != '00000'){
		
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		$array = array();
	
		if(count($result)>0){
			foreach ($result as $row){
				$array[]=$row['uur_reg_id'];
			}
			return $array;
		}else{
			return false;
		}
	}

	
	
	






	static function unitTest(){
		
		echo "
		-----------------------------<br>
		Testing class User:<br>
		----------------------------<br>
		";


		
	}

}


?>
