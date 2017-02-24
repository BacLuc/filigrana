<?php
class Watermark extends DB{
	
	
	private  $tablename = "watermark";
	private  $idfield = "wmk_id";
	protected  $Fields;
	protected  $Log;
	
	
	static function getTablename(){
		return "watermark";
	}
	
	static function getIdField(){
		return "wmk_id";
	}
	
	function __construct(){
		parent::__construct();
	}
	
	
	function createNewWatermark( $usr_id, $reg_id ,  $iphid = null){
		$insertArray = array(
				"wmk_usr_id" => $usr_id,
				"wmk_reg_id" => $reg_id
		);
		
		$wmkid=$this->insertPreparedStatement(Watermark::getTablename(), $insertArray, Watermark::getIdField());
		
		if($wmkid != "PDOERROR"){
			if($iphid != null){
				$insertArray = array(
						"iwx_wmk_id" => $wmkid,
						"iwx_iph_id" => $iphid
				);
				$iphidres = $this->insertPreparedStatement("iph_wmk", $insertArray, "(iwx_wmk_id || '_' iwx_iph_id)" );
				if($iphidres != "PDOERROR" ){
					$this->getByIdPrepared(Watermark::getTablename(), Watermark::getIdField(), $wmkid);
					return true;
				}else{
					return false;
				}
			}else{
				$this->getByIdPrepared(Watermark::getTablename(), Watermark::getIdField(), $wmkid);
				return true;
			}
		}else{
			return false;
		}
	}
	
	
	function addMetadata($key, $value, $url = null){

		$sql = "SELECT met_id FROM metadata_type WHERE lower(met_label) = ?";
		$statement = $this->dblk->prepare($sql);
		$lowerkey = strtolower($key);
		$statement->bindParam(1, $lowerkey);
		$statement->execute();
		
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		$met_id = null;
		if(count($result)>0){
			$met_id = $result[0]['met_id'];
		}else{
		
			$insertArray = array(
					"met_label" => $key,	
			);
			if($url != null){
				$insertArray['met_url']= $url;
			}
			$met_id = $this->insertPreparedStatement("metadata_type", $insertArray, "met_id");
			if($met_id == "PDOERROR"){
				
				return false;
			}
		}
		
		$insertArray = array(
				"wmx_wmk_id" => $this->getField(Watermark::getIdField()),
				"wmx_met_id" => $met_id,
				"wmx_value" => $value
		);
		$wmx_id = $this->insertPreparedStatement("wmk_met", $insertArray, "wmx_wmk_id");
	
		if($wmx_id != false){
			return true;
		}else{
			return false;
		}
		
	}
	
	function deleteMetadata($met_id, $value){
		$sql = "DELETE FROM wmk_met WHERE wmx_wmk_id = ? AND wmx_met_id = ? AND wmx_value = ?";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $this->getField(Watermark::getIdField()));
		$statement->bindParam(2, $met_id);
		$statement->bindParam(3, $value);
		$statement->execute();
		if ($statement->errorCode() == '00000') {
			return true;
		}else{
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
	}
	
	//addimage not needed, java does this
	function deleteImage($imgId){

		$sql = "DELETE FROM picture WHERE pic_id = ?";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $imgId);
		$statement->execute();
		if ($statement->errorCode() == '00000') {
			
			return true;
		}else{
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
	}
	
	function deleteWatermark(){
		$sql = "DELETE FROM watermark WHERE wmk_id = ?";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $this->getField($this->getIdField()));

		$statement->execute();
		if ($statement->errorCode() == '00000') {
			return true;
		}else{
			$this->handlePDOError($statement, __FILE__, __LINE__);
			return false;
		}
	}
	
}


?>