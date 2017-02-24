<?php
class IPHType extends DB{
	
	
	private  $tablename = "iph_type";
	private  $idfield = "iph_id";
	protected  $Fields;
	protected  $Log;
	
	
	static function getTablename(){
		return "iph_type";
	}
	
	static function getIdField(){
		return "iph_id";
	}
	
	function __construct(){
		parent::__construct();
	}
	
	
	function createNewIPHType( $usr_id, $reg_id, $subiphid = null, $superiphid = null){
		$insertArray = array(
				"iph_usr_id" => $usr_id,
				"iph_reg_id" => $reg_id
		);
		
		$iph=$this->insertPreparedStatement(IPHType::getTablename(), $insertArray, IPHType::getIdField());
		
		if($iph != "PDOERROR"){
			if($subiphid != null || $superiphid != null){
					$ressub = true;
					$ressuper = true;
					if($subiphid != null){
						$insertArray = array(
								"iix_subtype_iph_id" => $subiphid,
								"iix_supertype_iph_id" => $iph
						);
						$ressub = $this->insertPreparedStatement("iph_iph", $insertArray, "(iix_subtype_iph_id || '_' iix_supertype_iph_id)" );
					}
					if($subiphid != null){
						$insertArray = array(
								"iix_subtype_iph_id" => $iph,
								"iix_supertype_iph_id" => $superiphid
						);
						$ressuper = $this->insertPreparedStatement("iph_iph", $insertArray, "(iix_subtype_iph_id || '_' iix_supertype_iph_id)" );
					}
					if($ressub != "PDOERROR" && $ressuper != "PDOERROR"){
						$this->getByIdPrepared(IPHType::getTablename(), IPHType::getIdField(), $iph);
						return true;
					}else{
						return false;
					}
			}else{
				$this->getByIdPrepared(IPHType::getTablename(), IPHType::getIdField(), $iph);
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
				"imx_iph_id" => $this->getField(IPHType::getIdField()),
				"imx_met_id" => $met_id,
				"imx_value" => $value
		);
		$wmx_id = $this->insertPreparedStatement("iph_met", $insertArray, "imx_iph_id");
	
		if($wmx_id != "PDOERROR"){
			return true;
		}else{
			return false;
		}
		
	}
	
	function deleteMetadata($met_id, $value){
		$sql = "DELETE FROM iph_met WHERE imx_iph_id = ? AND imx_met_id = ? AND imx_value = ?";
		$statement = $this->dblk->prepare($sql);
		$statement->bindParam(1, $this->getField(IPHType::getIdField()));
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
	
	function deleteIPHType(){
		$sql = "DELETE FROM iph_type WHERE iph_id = ?";
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