<?php
include("../server/php/include.php");
ini_set("display_errors", 1);

if(isset($_POST)){
	if(isset($_POST['action'])){
		$ReturnArray = array();
		$Main = new Main();
		
		if($_POST['action'] == 'invitateUser'){
			if(isset($_POST['researchGroup']) && isset($_POST['email'])){
				$ReturnArray = $Main->invitateUser($_POST['researchGroup'], $_POST['email']);
			}else{
				
				$ReturnArray['check'] = "ser";
			}
			
			
		}
		
		elseif($_POST['action'] == 'checkHash'){
			if(isset($_POST['hash'])){
				$ReturnArray=$Main->checkHash($_POST);
				/*
				if($_POST['hash'] == '1'){
					$ReturnArray['check'] = "nhf";
				}
				elseif($_POST['hash'] == '2'){
					$ReturnArray['check'] = "alv";
				}else{
					$ReturnArray['email'] = "lucius.bachmann@gmx.ch";
					$ReturnArray['check'] = "suc";
				}*/
			}else{
		
				$ReturnArray['check'] = "ser";
			}
				
				
		}
		
		
		elseif($_POST['action'] == 'RegisterUser'){
			if(isset($_POST['username']) && isset($_POST['pw1']) && isset($_POST['pw2']) ){
				$ReturnArray = $Main->registerUser($_POST);
				/*
				if($_POST['pw1'] == '1'){
					$ReturnArray['check'] = "ivp";
				}
				elseif($_POST['pw1'] == '2'){
					$ReturnArray['check'] = "pns";
				}else{
					
					$ReturnArray['check'] = "suc";
				}*/
			}else{
		
				$ReturnArray['check'] = "ser";
			}
		
		
		}
		
		elseif($_POST['action'] == 'Login'){
			if(isset($_POST['email']) && isset($_POST['pw']) ){
				$ReturnArray = $Main->login($_POST['email'], $_POST['pw']);
			}else{
		
				$ReturnArray['check'] = "ser";
				$ReturnArray['request'] = $_POST;
			}
		
		
		}
		
		elseif($_POST['action'] == 'SwitchResearchGroup'){
			if(isset($_POST['groupid'])){
				
					$ReturnArray['check'] = "suc";
					$ReturnArray['usergroup'] = "Filigrana1";
						
				
			}else{
		
				$ReturnArray['check'] = "ser";
				$ReturnArray['request'] = $_POST;
			}
		
		
		}
		
		elseif($_POST['action'] == 'EditUser'){
			if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['oldpw']) && isset($_POST['newpw']) && isset($_POST['newpw2']) ){
				$ReturnArray = $Main->editUser($_POST['username'], $_POST['email'], $_POST['oldpw'], $_POST['newpw'],  $_POST['newpw2']);
			}else{
		
				$ReturnArray['check'] = "ser";
			}
		
		
		}
		
		
		elseif($_POST['action'] == 'newResearchgroup'){
			$ReturnArray = $Main->newResearchGroup();
		
		
		}
		
		elseif($_POST['action'] == 'editResearchGroup'){
			if(isset($_POST['res_id']) && isset($_POST['newname']) && isset($_POST['newuser']) && 
					isset($_POST['edituser']) && isset($_POST['role_id']) && isset($_POST['removeuser'])){
				$ReturnArray = $Main->editResearchGroup($_POST['res_id'], $_POST['newname'], $_POST['edituser'], $_POST['role_id'],$_POST['removeuser'],$_POST['del']);
				
			}else{
		
				$ReturnArray['check'] = "ser";
			}
		
		
		}
		
		elseif($_POST['action'] == 'uploadImage'){
			if(isset($_POST['index']) ){
				$ReturnArray = $Main->uploadImage($_POST['index']);
			}else{
		
				$ReturnArray['check'] = "ser";
			}
		
		
		}
		
		elseif($_POST['action'] == 'uploadImageAdd'){
			if(isset($_POST['index']) && isset($_POST['type']) && isset($_POST['id']) ){
				
				$ReturnArray = $Main->addImage($_POST['index'], $_POST['type'], $_POST['id']);
			}else{
		
				$ReturnArray['check'] = "ser";
			}
		
		
		}
		
		
		elseif($_POST['action'] == 'UploadSektch'){
			if( isset($_POST['file']) ){
				$ReturnArray=$Main->searchBySketchWM($_POST['file']);
			}else{
		
				$ReturnArray['check'] = "ser";
			}
		
		
		}
		
		elseif($_POST['action'] == 'addSketch'){
			if( isset($_POST['file']) && isset($_POST['type']) && isset($_POST['id'])){
				$ReturnArray=$Main->addSketch($_POST['file'], $_POST['type'], $_POST['id']);
			}else{
		
				$ReturnArray['check'] = "ser";
			}
		
		
		}
		
		elseif($_POST['action'] == 'SearchWatermarks'){
			if( isset($_POST['file']) ){

				$ReturnArray=$Main->searchBySketchWM($_POST['file'],null, isset($_POST['picid']) ? $_POST['picid'] : "");
				//$ReturnArray['result'] = array( '34' => 0.3423 , '35' => 0.3223 , '36' => 0.3223);
					
			}else{
			
				$ReturnArray['check'] = "ser";
			}
		
			
		
		
		}
		
		elseif($_POST['action'] == 'SearchIPHTypes'){
				
			$ReturnArray = $Main->searchBySketchIPHType(isset($_POST['picid']) ? $_POST['picid'] : "");
				
		
		
		}
		
		
		elseif($_POST['action'] == 'selectWatermark'){
			if( isset($_POST['wmid']) ){
				
			
					$ReturnArray['check'] = "suc";
				
			}else{
			
				$ReturnArray['check'] = "ser";
			}
			
		
		}
		
		
		elseif($_POST['action'] == 'SelectIPHType'){
			if( isset($_POST['iphid']) ){
		
					
				$ReturnArray['check'] = "suc";
		
			}else{
					
				$ReturnArray['check'] = "ser";
			}
				
		
		}
		
		elseif($_POST['action'] == 'ManageTextSearch'){
			if( isset($_POST['query']) ){
					
				$ReturnArray = $Main->manageTextSearch($_POST['query']);
			
			}else{
					
				$ReturnArray['check'] = "ser";
			}	
			
				
		
		
		}
		
		
		elseif($_POST['action'] == 'getWatermark'){
			if( isset($_POST['wmid']) ){
				if(isset($_POST['withSession'])){
					$ReturnArray= $Main->getWatermarkByIdWithSession($_POST['wmid'], isset($_POST['picid']) ? $_POST['picid'] : NULL, isset($_POST['key']) ? $_POST['key'] : NULL);
				}else{
					$ReturnArray= $Main->getWatermarkById($_POST['wmid'], isset($_POST['picid']) ? $_POST['picid'] : NULL, isset($_POST['key']) ? $_POST['key'] : NULL);
				}
						
				/*	
				$ReturnArray['check'] = "suc";
				$ReturnArray['result'] = array(
						'wmid' => 2,
						'img' => array(array('id' => 2, 'path'=>'/img/test/testpictures/testwm.jpg'),
								array('id' => 3, 'path'=>'/img/test/testpictures/testwm.jpg'),
								 array('id' => 4, 'path'=>'/img/test/testpictures/testwm.jpg')),
						'sketches' => array(array('id' => 2, 'path'=>'img/test/testpictures/testsketch.jpg'),
								array('id' => 3, 'path'=>'img/test/testpictures/testsketch.jpg'),
								array('id' => 4, 'path'=>'img/test/testpictures/testsketch.jpg')),
						'name' => 'mywatermark',
						'metadata' => array(array('id'=> 2, 'property' => 'fundort', 'value' => 'basel'),
											array('id'=> 3, 'property' => 'herkunftsort', 'value' => 'karlsruhe')),
						'iph_types' => array(34 => 'iphtyp1',35 => 'iphtyp2'),
						'url' => '',
						'edit' => 1
				);*/
					
			}else{
					
				$ReturnArray['check'] = "ser";
			}
				
		
		
		
		}
		
		
		elseif($_POST['action'] == 'getIPHType'){
			if( isset($_POST['iphid']) ){
				if(isset($_POST['withSession'])){
					$ReturnArray = $Main->getIPHTypeByIdWithSession($_POST['iphid'], isset($_POST['picid']) ? $_POST['picid'] : NULL, isset($_POST['key']) ? $_POST['key'] : NULL);
				}else{
					$ReturnArray = $Main->getIPHTypeById($_POST['iphid'], isset($_POST['picid']) ? $_POST['picid'] : NULL, isset($_POST['key']) ? $_POST['key'] : NULL);	
				}
			}
				/*	
					
				$ReturnArray['check'] = "suc";
				$ReturnArray['result'] = array(
						'wmid' => 2,
						'img' => array(array('id' => 2, 'path'=>'/img/test/testpictures/testiph.jpg'),
								array('id' => 3, 'path'=>'/img/test/testpictures/testiph.jpg'),
								 array('id' => 4, 'path'=>'/img/test/testpictures/testiph.jpg')),
						'sketches' => array(array('id' => 2, 'path'=>'img/test/testpictures/testsketch.jpg'),
								array('id' => 3, 'path'=>'img/test/testpictures/testsketch.jpg'),
								array('id' => 4, 'path'=>'img/test/testpictures/testsketch.jpg')),
						'name' => 'myIphType',
						'metadata' => array(array('id'=> 2, 'property' => 'fundort', 'value' => 'basel'),
											array('id'=> 3, 'property' => 'herkunftsort', 'value' => 'karlsruhe')),
						'iph_types' => array(34 => 'iphtyp1',35 => 'iphtyp2'),
						'url' => '',
						'edit' => 1
				);
					
			}else{
					
				$ReturnArray['check'] = "ser";
			}
			*/
		
		
		
		}
		
		

		elseif($_POST['action'] == 'editWatermark'){
			if( isset($_POST['wmid']) && isset($_POST['newimg']) && isset($_POST['delimg'])  && isset($_POST['name'])
					&& isset($_POST['newSketch']) && isset($_POST['delSketch'])
					&& isset($_POST['newMetadata']) && isset($_POST['delMetadata'])
					&& isset($_POST['newiphtypes']) && isset($_POST['deliphtypes'])){
				
					$ReturnArray = $Main->editWatermark($_POST);
					//$ReturnArray['check'] = "suc";
				
				
					
			}else{
					
				$ReturnArray['check'] = "ser";
			}
		
		
		
		
		}
		
		
		elseif($_POST['action'] == 'editIPHType'){
			if( isset($_POST['iphid']) && isset($_POST['newimg']) && isset($_POST['delimg'])
					&& isset($_POST['newSketch']) && isset($_POST['delSketch'])
					&& isset($_POST['newMetadata']) && isset($_POST['delMetadata'])
					&& isset($_POST['newiphsupertypes']) && isset($_POST['deliphsupertypes'])
					&& isset($_POST['newiphsubtypes']) && isset($_POST['deliphsubtypes'])
					&& isset($_POST['newWatermarks']) && isset($_POST['delWatermarks'])){
					
		
					
				$ReturnArray = $Main->editIPHType($_POST);
		
		
					
			}else{
					
				$ReturnArray['check'] = "ser";
			}
		
		
		
		
		}
		
		
		elseif($_POST['action'] == 'getMetadataTypes'){
			if( isset($_POST['query']) ){
					
					
				$ReturnArray['check'] = "suc";
				$ReturnArray['result'] = array('fundort' , 'herkunftsort' , 'datierung');
					
			}else{
					
				$ReturnArray['check'] = "ser";
			}
				
		
		
		
		}
		
	elseif($_POST['action'] == 'addCurrent'){
			if( isset($_POST['type']) && isset($_POST['sketch']) && isset($_POST['id']) ){
					
					
				$ReturnArray = $Main->addCurrent($_POST['type'], $_POST['sketch'], $_POST['id']);
					
			}else{
					
				$ReturnArray['check'] = "ser";
			}
				
		
		
		
		}
		
		
		elseif($_POST['action'] == 'getUsers'){
			if( isset($_POST['query']) ){
					
					
				$ReturnArray['check'] = "suc";
				$ReturnArray['result'] = array(array('username' => 'lbac', 'email' => 'lucius.bachmann@gmx.ch') ,
												    	array('username' => 'lbac', 'email' => 'lucius.bachmann@gmx.ch')
													);
					
			}else{
					
				$ReturnArray['check'] = "ser";
			}
		
		
		
		
		}
		
		
		elseif($_POST['action'] == 'getUser'){
			if( isset($_POST['usr_id']) && isset($_POST['res_id']) ){
					
					
				$ReturnArray = $Main->getUserForManageLayout($_POST['usr_id'], $_POST['res_id']);
					
			}else{
					
				$ReturnArray['check'] = "ser";
			}
		
		
		
		
		}
		
		
		elseif($_POST['action'] == 'getResearchGroups'){
			$ReturnArray = $Main->getResearchGroups();
					
			
		
		
		
		
		}
		
		
		elseif($_POST['action'] == 'getUserRoles'){
			$ReturnArray = $Main->getUserRoles();
			
		
		
		
		
		}
		
		
		elseif($_POST['action'] == 'edituserRoles'){
			if( isset($_POST['uro_id']) && isset($_POST['name']) && isset($_POST['name']) && isset($_POST['rights'])){
					
					
				$ReturnArray['check'] = "suc";
				
					
			}else{
					
				$ReturnArray['check'] = "ser";
			}
		
		
		
		
		}
		
		elseif($_POST['action'] == "createWatermark"){
			$ReturnArray = $Main->createWatermark();
		}
		elseif($_POST['action'] == "createIPHType"){
			$ReturnArray = $Main->createIPHType();
		}
		
		elseif($_POST['action'] == "forgotPassword"){
			if(isset($_POST['email'])){
				$ReturnArray = $Main->forgotPassword($_POST['email']);
			}
		}
		
		elseif($_POST['action'] == "Logout"){
			$ReturnArray = $Main->logout();
		}
		
				
		
		//$ReturnArray = utf8_encode_deep($ReturnArray);
		echo json_encode($ReturnArray);
		
	}
	
	
	
}










?>
