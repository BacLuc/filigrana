<?php
class UserRole extends DB{
	private  $tablename = "user";
	private  $idfield = "usr_ID";
	protected  $Fields;
	protected  $Log;


	static function getTablename(){
		return "UserRole";
	}

	static function getIdField(){
		return "uro_id";
	}

	function __construct(){
		parent::__construct();
	}
	
	function getUserRoles(){
		$sql = "select * from ".UserRole::getTablename();
		$statement = $this->dblk->prepare($sql);
		$statement->execute();
		if($statement->errorCode() != '00000'){
				
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		$array = array();
		if(count($result)>0){
			foreach ($result as $row){
				$array[]=array(
						'id' => $row['uro_id'],
						'name' => $row['uro_name'],
						'rights' => array('editUserRights' => 0));
			}
			return $array;
		}else{
			return false;
		}
		
	}
	 function getUserRoleOfUser($usr_id, $reg_id){
	 	
		$sql = "SELECT UserRole.* FROM usr_uro_reg, userrole WHERE uur_usr_id = ? AND uur_reg_id = ? AND uur_uro_id = uro_id";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $usr_id);
		$statement->bindParam(2, $reg_id);
		$statement->execute();
		if($statement->errorCode() != '00000'){
	
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		$array = array();
		if(count($result) == 1){
		
			return $result[0];
		}else{
			return false;
		}
	}
	
}