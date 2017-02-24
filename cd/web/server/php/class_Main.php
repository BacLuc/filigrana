<?php

class Main{
	private $Log=NULL;
	private $User=NULL;
	private $Person=NULL;
	private $Terminsearch=NULL;
	private $isAdminSession=NULL;
	private $UserId = NULL;
	private $tmpFolderName = NULL;
	private $currentResearchGroup = 1;
	private $userrole = array();
	
	function __construct(){
		/*
		if(!is_session_started()){
			session_start();
		}
		if(isset($_SESSION['tmpFolderName'])){
			$this->tmpFolderName = $_SESSION['tmpFolderName'];
		}
		if(isset($_SESSION['user'])){
			/*
			
			
			$this->User = new User($this->Log);
			
			$this->User->setField("usr_ID", $_SESSION['user']['user_id']);
			$this->User->setField("per_prename", $_SESSION['user']['prename']);
			$this->User->setField("per_surname", $_SESSION['user']['surname']);
			$this->User->setField("per_email", $_SESSION['user']['email']);
			$this->User->setField("usr_type", $_SESSION['user']['type']);
			$this->UserId=$this->User->getField($this->User->getField($this->User->getIdField()));
		
		}*/
		
		
	}
	
	function startSession(){
		if(!is_session_started()){
			session_start();
		}
		if(isset($_SESSION['usr_id'])){
			$User = new User();
			$User->getById(User::getTablename(), $User::getIdField(), $_SESSION['usr_id']);
		
			$this->User=$User;
			$this->UserId = $this->User->getField(User::getIdField());
		}
		
		if(isset($_SESSION['tmpFolderName'])){
			$this->tmpFolderName = $_SESSION['tmpFolderName'];
		}
		
		if(isset($_SESSION['currentResearchGroup'])){
			$this->currentResearchGroup = $_SESSION['currentResearchGroup'];
		}
		if(isset($_SESSION['userrole'])){
			$this->userrole = $_SESSION['userrole'];
		}
	}
	
	function endSession(){
		if(isset($this->tmpFolderName)){
			$_SESSION['tmpFolderName']=$this->tmpFolderName;
		}
		if(isset($this->User)){
			$_SESSION['usr_id']=$this->User->getField(User::getIdField());
			
		}
		if(isset($this->currentResearchGroup)){
			$_SESSION['currentResearchGroup'] = $this->currentResearchGroup;
		}
		if(isset($this->userrole)){
			$_SESSION['userrole']= $this->userrole;
		}
		
	}
	
	
	function __destruct(){
		/*
		if(isset($this->tmpFolderName)){
			$_SESSION['tmpFolderName']=$this->tmpFolderName;
		}
		if($this->User!= null){
			/*
			$_SESSION['user']['user_id'] = $this->User->getField("usr_ID");
			$_SESSION['user']['prename'] = $this->User->getField("per_prename");
			$_SESSION['user']['surname'] = $this->User->getField("per_surname");
			$_SESSION['user']['email'] = $this->User->getField("per_email");
			$_SESSION['user']['type'] = $this->User->getField("usr_type");
			
		}*/
		
		
	}
	
	function sessionFunctionToCopy($values){
		$ReturnArray['check']="ser";
		$this->startSession();
		
		$this->endSession();
		return $ReturnArray;
	}
	
	
	function checkHash($values){
		$this->startSession();
		
		$ReturnArray['check'] = "ser";
		$User = new User();
		$hasharray = explode("@", $values['hash']);
		$hash= $hasharray[2];
		if($email = $User->getEmailMatchingHash($hash)){
			$User->getUserByEmail($email);
			
			$ReturnArray['check']="suc";
			$ReturnArray['email']=$email;
			$this->User = $User;
			
		}else{
			$ReturnArray['check'] = "nhf";
		}
		
		
		$this->endSession();
		return $ReturnArray;
	}
	
	
function registerUser($values){
		//$values = utf8_encode_deep($values);
		$ReturnArray['check']="ser";
		$this->startSession();
		if($this->User == NULL){
			$ReturnArray['check']="ser";
		}
		else if($values['pw1'] != $values['pw2']){
			$ReturnArray['check']="pns";
		}
		else{
			if($this->User->setUserPassword($values['pw1'])){
	
				if($this->User->setValidated()){
					if($values['username']!= ""){
						if($this->User->editUser(array("username" => $values['username']))){
							$ReturnArray['check']="suc";
						}
					}else{
						$ReturnArray['check']="suc";
					}
				}
			}
		}
		
		
		$this->endSession();
		return $ReturnArray;
	}
	
function invitateUser($reg_id, $email){

	$ReturnArray['check']="ser";
	$this->startSession();
	if(!isset($this->UserId)){
		$ReturnArray['check']="nlg";
	
	}elseif((!$this->userrole['uro_editresearchgroup'] &&
			($reg_id ==$this->currentResearchGroup && !$this->userrole['uro_editownresearchgroup'])) 
			&& !$this->userrole['uro_addUser']){
		$ReturnArray['check']="nri";
	}
	else{
		$User = new User();
		if($User->getUserByEmail($email)){
	
			$researchGroups = $User->getResearchGroups(NULL);
			
			
			//change if multiple researchgroups are possible
			if($researchGroups == false){
				if($User->insertUserIntoResearchGroup($reg_id)){
					$ReturnArray['check']="suc";
					$ReturnArray['result']=$User->getField(User::getIdField());
				}
			}else{
				$ReturnArray['check']="uig";
			}
		}else{
	
			$usr_id = $User->createUser(array("email" => $email, "researchGroup" => $reg_id), null);
			if($usr_id !== false){
				$ReturnArray['check']="suc";
				$ReturnArray['result']=$usr_id;
			}
		}
	
	}
	$this->endSession();
	return $ReturnArray;
}


function login($email, $password){

		
		$ReturnArray['check']="ser";
		$this->startSession();
		$User = new User();
		if($User->getUserByEmail($email)){
	
			if($User->passwordMatch($password)){
				$this->User = $User;
				$this->UserId = $User->getField(User::getIdField());
				$result = $User->getResearchGroups(NULL);
				if($result != false){
					$this->currentResearchGroup = $result[0];
					$UserRole = new UserRole();
					$this->userrole = $UserRole->getUserRoleOfUser($this->UserId, $this->currentResearchGroup);
					if($this->userrole != false){
						$ReturnArray['check']= "suc";
						$ReturnArray['username'] = $User->getField("usr_username");
						$_SESSION['username']=$User->getField("usr_username");
						$_SESSION['email']=$User->getField("usr_email");
						
						if($this->userrole['uro_uploadnewwatermark']){
							$ReturnArray['createWatermark']=1;
							$_SESSION['createWatermark']=1;
						}else{
							$ReturnArray['createWatermark']=0;
							$_SESSION['createWatermark']=0;
						}
						if($this->userrole['uro_uploadnewiphtype']){
							$ReturnArray['createIPHType']=1;
							$_SESSION['createIPHType']=1;
						}else{
							$ReturnArray['createIPHType']=0;
							$_SESSION['createIPHType']=0;
						}
					}
					
				}else{
					$ReturnArray['check']="del";
				}
				
			}else{
				$ReturnArray['check']="wpa";
			}
		}else{
			$ReturnArray['check']= "enf";
		}
	
	
		$this->endSession();
		return $ReturnArray;
}

function editUser($username, $email, $oldpw, $newpw, $newpw2){
	$ReturnArray['check']="ser";
	$this->startSession();
	if(!isset($this->UserId)){
		$ReturnArray['check']="nlg";
	}else{
		if($username != -1){
			if($this->User->editUser(array("username" => $username))){
				$ReturnArray['check']="suc";
			}
		}
		elseif ($email != -1){
			if($this->User->passwordMatch($oldpw)){
				if($this->User->editUser(array("email"=> $email))){
					$ReturnArray['check']="suc";
				}
			}else{
				$ReturnArray['check']="wpa";
			}
		}
		elseif($newpw != -1){
			if($newpw != $newpw2){
				$ReturnArray['check']="pns";
			}
			elseif(!$this->User->passwordMatch($oldpw)){
				$ReturnArray['check']="wpa";
			}else{
				if($this->User->setUserPassword($newpw)){
					$ReturnArray['check']="suc";
				}
			}
		}
	}
	
	
	$this->endSession();
	return $ReturnArray;
}
	
function getResearchGroups(){

	$ReturnArray['check']="ser";
	$this->startSession();

	if(!isset($this->UserId)){
		$ReturnArray['check']="nlg";
	}else{
		$ResearchGroups = new ResearchGroup();
		if($result=$ResearchGroups->getResearchGroups()){
			$ReturnArray['check']="suc";
			$ReturnArray['result']=$result;
			foreach($ReturnArray['result'] as $key=>$resgroup){
				if($this->userrole['uro_editresearchgroup'] || 
				($this->currentResearchGroup == $ReturnArray['result'][$key]['reg_id'] && $this->userrole['uro_editownresearchgroup'])){
					$ReturnArray['result'][$key]['edit']=1;
				}else{
					$ReturnArray['result'][$key]['edit']=0;
				}
			}
			if($this->userrole['uro_createresearchgroup']){
				$ReturnArray['edit']=1;
			}else{
				$ReturnArray['edit']=0;
			}
		}
	}
	$this->endSession();
	return $ReturnArray;

}

function getUserForManageLayout($usr_id, $reg_id){
	$ReturnArray['check']="ser";
	$this->startSession();
	if(!isset($this->UserId)){
		$ReturnArray['check']="nlg";
	}else{
		$User = new User();
		if($result = $User->getUserWithRole($usr_id, $reg_id)){
			$ReturnArray['check']="suc";
			$ReturnArray['result'] = array(
					"name" => $result['usr_username'],
					"email" => $result['usr_email'],
					"userrole" => $result['uur_uro_id']
			);
		}
	}
	
	
	$this->endSession();
	return $ReturnArray;
}

function getUserRoles(){
	$ReturnArray['check']="ser";
	
		$UserRole = new UserRole();
		$result = $UserRole->getUserRoles();
		if($result != false){
			$ReturnArray['check']="suc";
			$ReturnArray['result']=$result;
		}
	




	return $ReturnArray;
}

function newResearchGroup(){
	$ReturnArray['check']="ser";
	$this->startSession();
	if(!isset($this->UserId)){
		$ReturnArray['check']="nlg";
	
	}
	elseif(!$this->userrole['uro_createresearchgroup']){
		$ReturnArray['check']="nri";
	}
	else{
		if(!$this->userrole['uro_createresearchgroup']){
			$ReturnArray['check']="nri";
		}else{
			$ResearchGroup = new ResearchGroup();
			if($ResearchGroup->createResearchGroup()){
				$ReturnArray['check']="suc";
			}
		}
	}
	
	
	$this->endSession();
	return $ReturnArray;
}

function editResearchGroup($reg_id, $newname, $edituser, $uro_id,$removeUser, $del){
	$ReturnArray['check']="ser";
	$this->startSession();
	
	if($del === "true"){
		$del = true;
	}else{
		$del = false;
	}
	if(!$this->userrole['uro_editresearchgroup'] &&
	 ($reg_id ==$this->currentResearchGroup && !$this->userrole['uro_editownresearchgroup'])){
	
		$ReturnArray['check']="nri";
	}else{
		if($del === true){
			if(!$this->userrole['uro_deleteresearchgroup'] &&
				($reg_id ==$this->currentResearchGroup && !$this->userrole['uro_deleteownresearchgroup'])
				|| $reg_id == 1	){
				$ReturnArray['check']="nri";
			}else{
				$ResearchGroup = new ResearchGroup();
				if($ResearchGroup->deleteResearchGroup($reg_id)){
					$ReturnArray['check']="suc";
				}
			}
		}
		elseif($edituser != -1 && $uro_id != -1){
		
			if((!$this->userrole['uro_editresearchgroup'] &&
				 ($reg_id ==$this->currentResearchGroup && !$this->userrole['uro_editownresearchgroup'])) 
					&& !$this->userrole['uro_edituser']){
				$ReturnArray['check']="nri";
			}else{
				$ResearchGroup = new ResearchGroup();
				if($ResearchGroup->editUserRole($reg_id, $edituser, $uro_id)){
					$ReturnArray['check']="suc";
				}
			}
		}
		elseif($removeUser == -1 ){
			if((!$this->userrole['uro_editresearchgroup'] 
					&&($reg_id ==$this->currentResearchGroup && !$this->userrole['uro_editownresearchgroup']))
				){
				$ReturnArray['check']="nri";
			}else{
				$ResearchGroup = new ResearchGroup();
				if($ResearchGroup->editResearchGroup($reg_id, $newname)){
					$ReturnArray['check']="suc";
				}
			}
		}elseif($newname == "" && $removeUser != -1){
			$User = new User();
			if((!$this->userrole['uro_editresearchgroup']
					&&($reg_id ==$this->currentResearchGroup && !$this->userrole['uro_editownresearchgroup']))
					&& !$this->userrole['uro_deleteuser']){
				$ReturnArray['check']="nri";
			}else{
				if($User->getById(User::getTablename(), User::getIdField(), $removeUser)){
					if($User->removeUserFromResearchGroup($reg_id)){
						$ReturnArray['check']="suc";
					}
				}
			}
		}
	}
	
	
	
	$this->endSession();
	return $ReturnArray;
}
//look if not used
function deleteResearchGroup($reg_id){
	$ReturnArray['check']="ser";
	$this->startSession();
	$ResearchGroup = new ResearchGroup();
	if($ResearchGroup->deleteResearchGroup($reg_id)){
		$ReturnArray['check']="suc";
	}
	
	
	
	
	$this->endSession();
	return $ReturnArray;
}
	
	
	function uploadImage($index){
		$ReturnArray['check']= "ser";
		$this->startSession();
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}else{
			$img = new Image($index);
		
			if($img->error != 1){
				if($this->checkTmpFolder()){
					$path = "/var/www/html/filigrana/web/client/tmp/".$this->tmpFolderName."/currentimage.png";
					$img->writeImage($path);
					$ReturnArray['check']="suc";
					$ReturnArray['path']= "tmp/".$this->tmpFolderName."/currentimage.png";
				}else{
					
				}
				
			}else{
				$ReturnArray['check']="noi";
			}
		}
		$this->endSession();
		return $ReturnArray;
	}
	
	
	function addImage($index, $type, $id){
		//To do: user rights shit

		$ReturnArray['check']= "ser";
		$this->startSession();
		$targetItem = null;
		$reg_id = -1;
		if($type === "wm"){
			$targetItem = new Watermark();
			$res = $targetItem->getByIdPrepared(Watermark::getTablename(), Watermark::getIdField(), $id);
			if($res != false){
				$reg_id = $targetItem->getField("wmk_reg_id");
			}
		}elseif($type === "iph"){
			$targetItem = new IPHType();
			$res = $targetItem->getByIdPrepared(IPHType::getTablename(), IPHType::getIdField(), $id);
			if($res != false){
				$reg_id = $targetItem->getField("iph_reg_id");
			}
		}
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		elseif($reg_id == -1){
			$ReturnArray['check']="ser";
		}
		elseif($type === "wm" && (!$this->userrole['uro_editallwatermarks']
				&& ($reg_id == $this->currentResearchGroup && !$this->userrole['uro_editwatermarksofresearchgroup']))){
			$ReturnArray['check']="nri";
		}
		elseif($type === "iph" && (!$this->userrole['uro_editalliphtypes']
				&& ($reg_id == $this->currentResearchGroup && !$this->userrole['uro_editiphtypesofresearchgroup']))){
			$ReturnArray['check']="nri";
		}
		else{
			$img = new Image($index);
		
			if($img->error != 1){
				if($this->checkTmpFolder()){
					$path = "/var/www/html/filigrana/web/client/tmp/".$this->tmpFolderName."/addimage.png";
					$img->writeImage($path);
					$socket = new SocketHandler();
					$date= new DateTime();
					$dateString = $date->format("d_m_Y");
					$targetFolder = "/var/www/html/filigrana/web/client/img/photos/".$dateString;
					if(!is_dir($targetFolder)){
						mkdir($targetFolder);
					}
					$result['check']="ser";
					if($type == "wm"){
						$result = $socket->extractWM($path, $targetFolder, false, $id);
					}elseif($type == "iph"){
						$result = $socket->extractIPH($path, $targetFolder, false, $id);
					}
					
					$ReturnArray['check']=$result['check'];
				}else{
		
				}
					
			}else{
				$ReturnArray['check']="noi";
			}
		}		
		$this->endSession();
		return $ReturnArray;
	}
	
	
	function addSketch($base64_string, $type, $id){
		$ReturnArray['check']="ser";
		$this->startSession();
		$targetItem = null;
		$reg_id = -1;
		if($type === "wm"){
			$targetItem = new Watermark();
			$res = $targetItem->getByIdPrepared(Watermark::getTablename(), Watermark::getIdField(), $id);
			if($res != false){
				$reg_id = $targetItem->getField("wmk_reg_id");
			}
		}elseif($type === "iph"){
			$targetItem = new IPHType();
			$res = $targetItem->getByIdPrepared(IPHType::getTablename(), IPHType::getIdField(), $id);
			if($res != false){
				$reg_id = $targetItem->getField("iph_reg_id");
			}
		}
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		elseif($reg_id == -1){
			$ReturnArray['check']="ser";
		}
		elseif($type === "wm" && (!$this->userrole['uro_editallwatermarks']
				&& ($reg_id == $this->currentResearchGroup && !$this->userrole['uro_editwatermarksofresearchgroup']))){
			$ReturnArray['check']="nri";
		}
		elseif($type === "iph" && (!$this->userrole['uro_editalliphtypes']
				&& ($reg_id == $this->currentResearchGroup && !$this->userrole['uro_editiphtypesofresearchgroup']))){
			$ReturnArray['check']="nri";
		}
		else{
			if($this->checkTmpFolder()){
				
				$output_file ="/var/www/html/filigrana/web/client/tmp/".$this->tmpFolderName."/addsketch.png";
				$date= new DateTime();
				$dateString = $date->format("d_m_Y");
				$targetFolder = "/var/www/html/filigrana/web/client/img/sketches/".$dateString;
				if(!is_dir($targetFolder)){
					mkdir($targetFolder);
				}
				$ifp = fopen($output_file, "wb");
					
				$data = explode(',', $base64_string);
					
				fwrite($ifp, base64_decode($data[1]));
				fclose($ifp);
				$startupdated = microtime(true);
				$sh = new SocketHandler();
				if($type == "wm"){
					$result = $sh->extractWM($output_file, $targetFolder, true, $id);
				}elseif($type == "iph"){
					$result = $sh->extractIPH($output_file, $targetFolder, true, $id);
				}
				$ReturnArray['check']=$result['check'];
				
			}
		}
		$this->endSession();
		return $ReturnArray;
	}
	/**
	 * checks if the tmpfolder is created, and creates it if not
	 * @return boolean
	 */
	function checkTmpFolder(){
		if(empty($this->tmpFolderName)){
				
			$foldername = $this->User->getField("usr_email");
			$foldername = str_replace(" ", "", $foldername);
			$foldername = str_replace(".", "", $foldername);
			if(!is_dir("/var/www/html/filigrana/web/client/tmp/$foldername")){	
				if(mkdir("/var/www/html/filigrana/web/client/tmp/$foldername")){
					$this->tmpFolderName = $foldername;
				}
			}else{
				$this->tmpFolderName = $foldername;
			}
		}
		if(!empty($this->tmpFolderName)){
			return true;
		}else{
			return false;
		}
		
	}
	
	
	
	function searchBySketchWM($base64_string, $wmid = null, $picid = ""){
		$this->startSession();
		$ReturnArray['check']="ser";
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		else{
			if(isset($_POST['starttime'])){
				//Log::logError(__FILE__, __LINE__, "Upload took: ".(microtime(true)-$_POST['starttime']));
			}
			$start = microtime(true);
				
			$startupdated = microtime(true);		
				if($this->checkTmpFolder()){
					$output_file ="/var/www/html/filigrana/web/client/tmp/".$this->tmpFolderName."/currentsketch.png"; 
					$ifp = fopen($output_file, "wb");
				
					$data = explode(',', $base64_string);
				
					fwrite($ifp, base64_decode($data[1]));
					fclose($ifp);
					$startupdated = microtime(true);
					$sh = new SocketHandler();
					
					$result = $sh->retrieveWM($output_file, $picid);
			
					//Log::logError(__FILE__, __LINE__, "Retrieve Images took: ".(microtime(true)-$startupdated));
					$startupdated = microtime(true);
					//var_dump($result);
					$ReturnArray['result'] = array();
					$shownWatermarks = array();
					if(count($result) >1){
						
						foreach($result as $key => $value){
							if(!array_key_exists($value->shotid,$shownWatermarks)){
								$shownWatermarks[$value->shotid] = 1;
								
								$ReturnArray['result'][] = array("id" => $value->shotid, "score" => $value->score, "picid" => $value->picid);
							}
						}
					}
					$ReturnArray['pathimage']="tmp/".$this->tmpFolderName."/currentimage.png";
					$ReturnArray['pathsketch']="tmp/".$this->tmpFolderName."/currentsketch.png";
					$ReturnArray['check']="suc";
				
				}
		}
		$startupdated = microtime(true);
		$this->endSession();
		return $ReturnArray;
	}
	
	function searchBySketchIPHType($picid = ""){
		$this->startSession();
		$ReturnArray['check']="ser";
		
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		else{
			if(!empty($this->tmpFolderName)){
				$output_file ="/var/www/html/filigrana/web/client/tmp/".$this->tmpFolderName."/currentsketch.png";
				
				$sh = new SocketHandler();
				$result = $sh->retrieveIPH($output_file, $picid);
				//var_dump($result);
				$ReturnArray['result'] = array();
				$showniphtypes = array();
				if(count($result) >1){
					foreach($result as $key => $value){
						if(!array_key_exists($value->shotid,$showniphtypes)){
							$showniphtypes[$value->shotid] = 1;
						
							$ReturnArray['result'][] = array("id" => $value->shotid, "score" => $value->score, "picid" => $value->picid);
						}
					}
				}
				$ReturnArray['pathimage']="tmp/".$this->tmpFolderName."/currentimage.png";
				$ReturnArray['pathsketch']="tmp/".$this->tmpFolderName."/currentsketch.png";
				$ReturnArray['check']="suc";
					
			}
		}
		$this->endSession();
		return $ReturnArray;
	}
	
	function getWatermarkByIdWithSession($id, $picid = null, $key = null){
		$this->startSession();
		$ReturnArray['check']="ser";
		$edit = 0;
		$targetItem = null;
		$reg_id = -1;
		
		$targetItem = new Watermark();
		$res = $targetItem->getByIdPrepared(Watermark::getTablename(), Watermark::getIdField(), $id);
		if($res != false){
			$reg_id = $targetItem->getField("wmk_reg_id");
		}
		
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		elseif($reg_id == -1){
			$edit = 0;
		}
		elseif(!$this->userrole['uro_editallwatermarks']
				&& ($reg_id == $this->currentResearchGroup && !$this->userrole['uro_editwatermarksofresearchgroup'])){
			$edit = 0;
		}
		else{
			$edit = 1;
		}
		
		if($ReturnArray['check']!= "nlg"){
			$ReturnArray=$this->getWatermarkById($id, $picid, $key, $edit);
		}
		$this->endSession();
		return $ReturnArray;
	}
	
	function getWatermarkById($id, $picid = null, $key = null, $edit = 0){
		$ReturnArray['check']="ser";
		
		$start = microtime(true);
		$startupdated = $start;
		$ReturnArray['check']="ser";
		$db = new DB();
		$sql = "SELECT * FROM watermark WHERE wmk_id = $id";
		$watermark = $db->query($sql, true);
		$startupdated = microtime(true);
		if($watermark != "PDOERROR"){
			//var_dump($watermark);
			$ReturnArray['result']['wmid']=$id;
			$ReturnArray['result']['name']= $watermark[0]['wmk_name'];
			$ReturnArray['result']['edit']=$edit;
			
			$sql= "SELECT * FROM picture WHERE pic_wmk_id = $id";
			if($picid!= null){
				$sql.=" ORDER BY abs(pic_id - $picid)";
			}
			$pictures = $db->query($sql, true);
			$startupdated = microtime(true);
			$ReturnArray['result']['img'] = array();
			if($picid != null){
				$ReturnArray['result']['img'][0] = null;
			}
			
			if($pictures != "PDOERROR"){
				foreach($pictures as $picture){
					if($picture['pic_id'] == $picid){
						$ReturnArray['result']['img'][0] = array('id' => $picture['pic_id'], 'path' => $picture['pic_path']);
						break;
					}
					if($picture['pic_sketch'] == false){
						
							$ReturnArray['result']['img'][]=array('id' => $picture['pic_id'], 'path' => $picture['pic_path']);
					}else{
						$ReturnArray['result']['sketches'][]=array('id' => $picture['pic_id'], 'path' => $picture['pic_path']);
					}
				}
				if(count($ReturnArray['result']['img'] )>0){
					if($ReturnArray['result']['img'][0] == null){
						$ReturnArray['result']['img'][0] = $ReturnArray['result']['img'][1];
					}
				}
				
			}
			$startupdated = microtime(true);
			$sql = "SELECT * FROM wmk_met, Metadata_Type WHERE wmx_wmk_id = $id AND wmx_met_id = met_id";
			$metadatas = $db->query($sql, true);
			$startupdated = microtime(true);
			if($metadatas != "PDOERROR"){
				$counter = 1;
				$ReturnArray['result']['metadata'] = array();
				if($key != null){
					$ReturnArray['result']['metadata'][0]= array(
							'id' => "0_0_0",
							'property' => "",
							'value' => ""
					); 
				}
				$ReturnArray['result']['name']="<div class='row'>";
				foreach($metadatas as $metadata){
					if($key != null && $key == $metadata['wmx_wmk_id']."_".$metadata['met_id']."_".$metadata['wmx_value']){
						
							$ReturnArray['result']['metadata'][0]=array('id' => $metadata['wmx_wmk_id']."_".$metadata['met_id']."_0",
									'property' => $metadata['met_label'],
									'value' => $metadata['wmx_value']
							);
						
					}else{
						$ReturnArray['result']['metadata'][]=array('id' => $metadata['wmx_wmk_id']."_".$metadata['met_id']."_".$counter, 
														'property' => $metadata['met_label'], 
														'value' => $metadata['wmx_value']
														);
					}
					$ReturnArray['result']['name'].="<div class='col-xs-6 wrapfield'>".$metadata['met_label']."</div><div class='col-xs-6 wrapfield'> ".$metadata['wmx_value']."</div>";
					$counter++;
					
				}
				$ReturnArray['result']['name'].="</div>";
			}
			//Log::logError(__FILE__, __LINE__, "Foreach MEtadata took: ".(microtime(true)-$startupdated));
			$startupdated = microtime(true);
			$ReturnArray['check']="suc";
		}
		//Log::logError(__FILE__, __LINE__, "Whole Function GetbyId took: ".(microtime(true)-$start));
		$startupdated = microtime(true);
		return $ReturnArray;
	}
	
	function getIPHTypeByIdWithSession($id, $picid = null, $key = null){
		$this->startSession();
		$ReturnArray['check']="ser";
		$edit = 0;
		$targetItem = null;
		$reg_id = -1;
	
		$targetItem = new IPHType();
		$res = $targetItem->getByIdPrepared(IPHType::getTablename(), IPHType::getIdField(), $id);
		if($res != false){
			$reg_id = $targetItem->getField("iph_reg_id");
		}
	
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		elseif($reg_id == -1){
			$edit = 0;
		}
		elseif(!$this->userrole['uro_editalliphtypes']
				&& ($reg_id == $this->currentResearchGroup && !$this->userrole['uro_editiphtypesofresearchgroup'])){
			$edit = 0;
		}
		else{
			$edit = 1;
		}
	
		if($ReturnArray['check']!= "nlg"){
			$ReturnArray=$this->getIPHTypeById($id, $picid, $key, $edit);
		}
		$this->endSession();
		return $ReturnArray;
	}
	
	function getIPHTypeById($id, $picid= null,$key = null, $edit = 0){
		$ReturnArray['check']="ser";
		$db = new DB();
		$sql = "SELECT * FROM iph_type WHERE iph_id = $id";
		$watermark = $db->query($sql, true);
		if($watermark != "PDOERROR"){
			$ReturnArray['result']['iphid']=$id;
			$ReturnArray['result']['name']= $watermark[0]['iph_name'];
			$ReturnArray['result']['edit']=1;
				
			$sql= "SELECT * FROM picture WHERE pic_iph_id = $id";
			$pictures = $db->query($sql, true);
			$ReturnArray['result']['img'] = array();
			if($picid != null){
				$ReturnArray['result']['img'][0] = null;
			}
			if($pictures != "PDOERROR"){
				foreach($pictures as $picture){
					if($picid!= null && $picture['pic_id'] == $picid){
						$ReturnArray['result']['img'][0] = array('id' => $picture['pic_id'], 'path' => $picture['pic_path']);
						break;
					}
					if($picture['pic_sketch'] == false){
						$ReturnArray['result']['img'][]=array('id' => $picture['pic_id'], 'path' => $picture['pic_path']);
					}else{
						$ReturnArray['result']['sketches'][]=array('id' => $picture['pic_id'], 'path' => $picture['pic_path']);
					}
				}
			}
				
			$sql = "SELECT * FROM iph_met, Metadata_Type WHERE imx_iph_id = $id AND imx_met_id = met_id";
			$metadatas = $db->query($sql, true);
			if($metadatas != "PDOERROR"){
				if($key != null){
					$ReturnArray['result']['metadata'][0]= array(
							'id' => "0_0_0",
							'property' => "",
							'value' => ""
					);
				}
				$ReturnArray['result']['name']="<div class='row'>";
				$counter = 1;
				foreach($metadatas as $metadata){
					if($key != null && $key == $metadata['imx_iph_id']."_".$metadata['met_id']."_".$metadata['imx_value']){
					
						$ReturnArray['result']['metadata'][0]=array('id' => $metadata['imx_iph_id']."_".$metadata['met_id']."_0",
								'property' => $metadata['met_label'],
								'value' => $metadata['imx_value']
						);
					
					}else{	
						$ReturnArray['result']['metadata'][]=array('id' => $metadata['imx_iph_id']."_".$metadata['met_id']."_".$counter,
								'property' => $metadata['met_label'],
								'value' => $metadata['imx_value']
						);
					}
					$ReturnArray['result']['name'].="<div class='col-xs-6 wrapfield'>".$metadata['met_label']."</div><div class='col-xs-6 wrapfield'> ".$metadata['imx_value']."</div>";
					$counter++;
					
						
				}
				$ReturnArray['result']['name'].="</div>";
			}
			$ReturnArray['check']="suc";
		}
		return $ReturnArray;
	}
	

	
	function addCurrent($type, $sketch, $id){
		$ReturnArray['check']="ser";
		$this->startSession();
		$comm = new SocketHandler();
		$date= new DateTime();
		$dateString = $date->format("d_m_Y");
		if($sketch == "false"){
			$sketch = false;
		}else if($sketch == "true"){
			$sketch = true;
		}
		$targetItem = null;
		$reg_id = -1;
		if($type === "wm"){
			$targetItem = new Watermark();
			$res = $targetItem->getByIdPrepared(Watermark::getTablename(), Watermark::getIdField(), $id);
			if($res != false){
				$reg_id = $targetItem->getField("wmk_reg_id");
			}
		}elseif($type === "iph"){
			$targetItem = new IPHType();
			$res = $targetItem->getByIdPrepared(IPHType::getTablename(), IPHType::getIdField(), $id);
			if($res != false){
				$reg_id = $targetItem->getField("iph_reg_id");
			}
		}
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		elseif($reg_id == -1){
			$ReturnArray['check']="ser";
		}
		elseif($type === "wm" && (!$this->userrole['uro_editallwatermarks']
				&& ($reg_id == $this->currentResearchGroup && !$this->userrole['uro_editwatermarksofresearchgroup']))){
			$ReturnArray['check']="nri";
		}
		elseif($type === "iph" && (!$this->userrole['uro_editalliphtypes']
				&& ($reg_id == $this->currentResearchGroup && !$this->userrole['uro_editiphtypesofresearchgroup']))){
			$ReturnArray['check']="nri";
		}
		else{
			if(isset($this->tmpFolderName)){
				if($type == "wm"){
					if($sketch){
						
						if(!is_dir("img/sketches/".$dateString)){
							mkdir("img/sketches/".$dateString);
						}
						$ReturnArray=$comm->extractWM("/var/www/html/filigrana/web/client/tmp/".$this->tmpFolderName."/currentsketch.png", "/var/www/html/filigrana/web/client/img/sketches/".$dateString, true, $id);
									
					}else{
					
						if(!is_dir("img/photos/".$dateString)){
							mkdir("img/photos/".$dateString);
						}
						$ReturnArray=$comm->extractWM("/var/www/html/filigrana/web/client/tmp/".$this->tmpFolderName."/currentimage.png", "/var/www/html/filigrana/web/client/img/photos/".$dateString, false, $id);
							
					}
				}
				elseif($type == "iph"){
					if($sketch){
					
						if(!is_dir("img/sketches/".$dateString)){
							mkdir("img/sketches/".$dateString);
						}
						$ReturnArray=$comm->extractIPH("/var/www/html/filigrana/web/client/tmp/".$this->tmpFolderName."/currentsketch.png", "/var/www/html/filigrana/web/client/img/sketches/".$dateString, true, $id);
				
					}else{
						
						if(!is_dir("img/photos/".$dateString)){
							mkdir("img/photos/".$dateString);
						}
						$ReturnArray=$comm->extractIPH("/var/www/html/filigrana/web/client/tmp/".$this->tmpFolderName."/currentimage.png", "/var/www/html/filigrana/web/client/img/photos/".$dateString, false, $id);
				
					}
				}
				
			}
		}
		$this->endSession();
		return $ReturnArray;
	}
	
	
	function editWatermark($values){
		
		$ReturnArray['check']="ser";
		$this->startSession();
		//if isset delete
		
		$Watermark = new Watermark();
		$Watermark->getByIdPrepared(Watermark::getTablename(), Watermark::getIdField(), $values['wmid']);

		
		$targetItem = null;
		$reg_id = -1;
		
		
		$reg_id = $Watermark->getField("wmk_reg_id");
		
		
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		elseif($reg_id == -1){
			$ReturnArray['check']="ser";
		}
		elseif(!$this->userrole['uro_editallwatermarks']
				&& ($reg_id == $this->currentResearchGroup && !$this->userrole['uro_editwatermarksofresearchgroup'])){
			$ReturnArray['check']="nri";
		}
		else{
			if($values['del'] === "true"){
			
				if($Watermark->deleteWatermark()){
					$ReturnArray['check']="suc";
				}
			}elseif($values['newMetadata'] != -1){
				//$values['newMetadata'] = utf8_decode_deep($values['newMetadata']);
				if($Watermark->addMetadata($values['newMetadata']['key'], $values['newMetadata']['value'])){
					$ReturnArray['check']="suc";
				}
			}elseif($values['delMetadata'] != -1){
				$delid = explode("_", $values['delMetadata']['id']);
	
				if($Watermark->deleteMetadata($delid[1], $values['delMetadata']['value'])){
					$ReturnArray['check']="suc";
				}
			}elseif($values['delimg']!= -1){
	
				if($Watermark->deleteImage($values['delimg'])){
					$ReturnArray['check']="suc";
				}
			}elseif($values['delSketch']!= -1){
		
				if($Watermark->deleteImage($values['delSketch'])){
					$ReturnArray['check']="suc";
				}
			}
		}
		//if isset add metadata
		
		$this->endSession();
		return $ReturnArray;
		
		
	}
	
	function editIPHType($values){
		//TO DO: USER RIGHTS STAFF
		$ReturnArray['check']="ser";
		$this->startSession();
		//if isset delete
	
		$iphtype = new IPHType();
		$iphtype->getByIdPrepared(IPHType::getTablename(), IPHType::getIdField(), $values['iphid']);
		$targetItem = null;
		$reg_id = -1;
		
		$reg_id = $iphtype->getField("iph_reg_id");
		
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		elseif($reg_id == -1){
			$ReturnArray['check']="ser";
		}
		
		elseif(!$this->userrole['uro_editalliphtypes']
				&& ($reg_id == $this->currentResearchGroup && !$this->userrole['uro_editiphtypesofresearchgroup'])){
			$ReturnArray['check']="nri";
		}
		else{	
			if($values['del'] === "true"){
		
				if($iphtype->deleteIPHType()){
					$ReturnArray['check']="suc";
				}
			}elseif($values['newMetadata'] != -1){
				//$values['newMetadata'] = utf8_decode_deep($values['newMetadata']);
				if($iphtype->addMetadata($values['newMetadata']['key'], $values['newMetadata']['value'])){
					$ReturnArray['check']="suc";
				}
			}elseif($values['delMetadata'] != -1){
				$delid = explode("_", $values['delMetadata']['id']);
		
				if($iphtype->deleteMetadata($delid[1], $values['delMetadata']['value'])){
					$ReturnArray['check']="suc";
				}
			}elseif($values['delimg']!= -1){
		
				if($iphtype->deleteImage($values['delimg'])){
					$ReturnArray['check']="suc";
				}
			}elseif($values['delSketch']!= -1){
		
				if($iphtype->deleteImage($values['delSketch'])){
					$ReturnArray['check']="suc";
				}
			}
		}
		//if isset add metadata
	
		$this->endSession();
		return $ReturnArray;
	
	
	}
	
	
	function createWatermark(){
		$ReturnArray['check']="ser";
		$this->startSession();
		
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		elseif(!$this->userrole['uro_uploadnewwatermark']){
			$ReturnArray['check']="nri";
		}
		else{
			$wmk = new Watermark();
			if($wmk->createNewWatermark($this->UserId, $this->currentResearchGroup)){
				$id= $wmk->getField(Watermark::getIdField());
				if($id != false){
					$ReturnArray['check']="suc";
					$ReturnArray['wmid']=$id;
				}
			}
		}
		
		$this->endSession();
		return $ReturnArray;
	}
	
	function createIPHType(){
		$ReturnArray['check']="ser";
		$this->startSession();
		
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		elseif(!$this->userrole['uro_uploadnewiphtype']){
			$ReturnArray['check']="nri";
		}
		else{
			$wmk = new IPHType();
		
			if($wmk->createNewIPHType($this->UserId, $this->currentResearchGroup)){
				$id= $wmk->getField(IPHType::getIdField());
				if($id != false){
					$ReturnArray['check']="suc";
					$ReturnArray['iphid']=$id;
				}
			}
		}
	
		$this->endSession();
		return $ReturnArray;
	}
	
	
	function manageTextSearch($query){
		$ReturnArray['check']="suc";
		$this->startSession();
		if(!isset($this->UserId)){
			$ReturnArray['check']="nlg";
		}
		else{
			$db = new DB();
			$ReturnArray['result'] = [];
			
			$result = $db->textQuery($query);
			
			if($result != false){
				if(count($result) > 0){
					$ReturnArray['check']="suc";
					$alreadyShown = array();
					foreach($result as $row){
						if(array_key_exists($row['other_id'], $alreadyShown)){
							if(array_key_exists($row['type'], $alreadyShown[$row['other_id']])){
								continue;
							}
						}
						$alreadyShown[$row['other_id']][$row['type']]=1;
						$ReturnArray['result'][] = array(
								'id' => $row['other_id'],
								'type' => $row['type'],
								'key' => $row['other_id']."_".$row['met_id']."_".$row['value']
						);
						
					}
				}else{
					$ReturnArray['check']="suc";
				}
			}
		}
		
		
		$this->endSession();
		return $ReturnArray;
	}
	
	function forgotPassword($email){
		
		
		$User = new User();
		if($User->getUserByEmail($email)){
			$User->sendPasswordLink($email);
		}
		
		echo "<html><body>Password link sent to the specified Email Address</body></html>";
		exit();
	}
	
	function logout(){
		$ReturnArray['check']="suc";
		$this->startSession();
		unset($this->currentResearchGroup);
		
		unset($this->tmpFolderName);
		unset($this->User);
		unset($this->UserId);
		unset($this->userrole);
		
		
		$this->endSession();
		session_destroy();
		return $ReturnArray;
	}
	
	
}

?>
