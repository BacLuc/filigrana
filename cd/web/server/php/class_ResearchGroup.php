<?php class ResearchGroup extends DB{
	private  $tablename = "ResearchGroup";
	private  $idfield = "reg_id";
	protected  $Fields;
	protected  $Log;
	
	
	static function getTablename(){
		return "ResearchGroup";
	}
	
	static function getIdField(){
		return "reg_id";
	}
	
	function __construct(){
		parent::__construct();
	}
	
	
	function getResearchGroups(){
		$researchgroups = array();
		$sql = "select * from researchgroup reg left join usr_uro_reg x ON reg.reg_id=x.uur_reg_id left JOIN users ON x.uur_usr_id = users.usr_id";
		$result = $this->query($sql, true);
		if($result != "PDOERROR"){
			$lastregid = -1;
			foreach($result as $row){
				if($lastregid == $row['reg_id']){
					$researchgroups[count($researchgroups)-1]['users'][] = $row['usr_id'];
					
				}else{
					$lastregid = $row['reg_id'];
					$researchgroup['name']=$row['reg_name'];
					$researchgroup['reg_id'] = $row['reg_id'];
					if($row['uur_usr_id'] == null){
						$researchgroup['users']=array();
					}else{
						$researchgroup['users'] = array($row['usr_id']);
					}
					$researchgroups[] = $researchgroup;
				}
			}
			return $researchgroups;
		}else{
			return false;
		}
	}
	
	function createResearchGroup(){
		$sql = "INSERT INTO ".$this->getTablename()." (reg_name)VALUES('new ResearchGroup')";
		$statement = $this->dblk->prepare($sql);
		$statement->execute();
		if($statement->errorCode() != '00000'){
		
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		return true;
	}
	
	function editResearchGroup($reg_id, $newname){
		$updateArray = array("reg_name" => $newname);
		if($this->updateFields($this->getTablename(), $this->getIdField(), $reg_id, $updateArray)){
			return true;
		}else{
			return false;
		}
	}
	
	function deleteResearchGroup($reg_id){
		$sql = "DELETE FROM ".$this->getTablename()." WHERE ".$this->getIdField()." = ?";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $reg_id);
		$statement->execute();
		if($statement->errorCode() != '00000'){
			
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		return true;
	}
	
	function editUserRole($reg_id, $usr_id, $uro_id){
	
		$sql = "UPDATE usr_uro_reg SET uur_uro_id = ? WHERE uur_reg_id = ? AND uur_usr_id = ?";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $uro_id);
		$statement->bindParam(2, $reg_id);
		$statement->bindParam(3, $usr_id);
		$statement->execute();
		if($statement->errorCode() != '00000'){
	
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
		return true;
	}
}

?>