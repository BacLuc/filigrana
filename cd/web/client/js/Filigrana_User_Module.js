/**
 * 
 */
Filigrana.prototype.initialiseUserModule = function(){ 
	this.username = "";
	this.usergroup = "";
	this.possibleUserGroups = {};
}

Filigrana.prototype.User_login = function(targetPage){

	
	var username = this.pages.login_page.getField("loginEmail");
	var password = this.pages.login_page.getField("loginPassword");
	var post = {
			action : "Login",
			email : username,
			pw : password
	};

	$.ajax({
		targetPage : targetPage,
		type: "POST",
		url: this.parameters.server,
		data: post,
		dataType: "json",
		success: function(response){
			if(response.check == "suc"){
				$.getApp().username = response.username;
				$.getApp().usergroup = response.usergroup;
				$.getApp().pages['manage_user_page'].getFieldNotValue("Username").setAll(response.username);
				$.getApp().pages['login_page'].getFieldNotValue("loginPassword").setAll("");
				
				if(response.createWatermark == 0){
					$('#add_watermark').hide();
				}
				if(response.createIPHType == 0){
					$('#add_iph_type').hide();
				}
				$.getApp().switchPage(targetPage);
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
			
			
		}
	});
			
	
	
}


Filigrana.prototype.logout= function(){
	var post = {
			action : "Logout"
	};

	$.ajax({
		type: "POST",
		url: this.parameters.server,
		data: post,
		dataType: "json",
		success: function(response){
			if(response.check == "suc"){
				
				$.getApp().switchPage("login_page");
				$.getApp().navmenu.hide();
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
			
			
		}
	});
}



Filigrana.prototype.getResearchGroups= function(targetPage){
	
	
	var post = {
			action : "getResearchGroups",
	};

	$.ajax({
		targetPage : targetPage,
		type: "POST",
		url: this.parameters.server,
		data: post,
		dataType: "json",
		success: function(response){
			if(response.check == "suc"){
				
				$('[FiligranaName = "ResearchGroupContainer"]').empty();
				
				/*<div class = "col-xs-12 col-sm-4">
					<button type='button' id="change-password" FiligranaAction="changePassword" class="clickable btn btn-lg btn-primary btn-block">Change Password</button>
				</div>*/
				if(response.edit == 1){ 
					var attrArray = {
							class: "row",
							FiligranaType: "FieldContainer",
							FiligranaName: "addResearchGroupRow"
						};					
					var containerAdd = $.getApp().pages[targetPage].fieldcontainers.ResearchGroupContainer.appendFieldContainer(attrArray.FiligranaName, 'div', attrArray);
						var attrArray = {
								class: "col-xs-3",
								FiligranaType: "FieldContainer",
								FiligranaName: "addResearchGroupBootstrapSize"
							};					
						var containerAdd = containerAdd.appendFieldContainer(attrArray.FiligranaName, 'div', attrArray);
							var attrArray = {
									type:'button',
									id : "addResearchGroup",
									FiligranaAction:"addResearchGroup",
									FiligranaType: "Field",
									FiligranaName: "AddResearchGroupButton",
									class:"clickable btn btn-lg plusButton bigbutton pull-left"
								};					
							var fieldAdd = containerAdd.appendFieldContainer(attrArray.FiligranaName, 'button', attrArray);
								attrArray11 = {
										class: "glyphicon glyphicon-plus green-glaphicolon",
										FiligranaType: "Field",
										FiligranaName: "addResearchGroupIcon"
									};
								var container11 = fieldAdd.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
				}
				
				for(var i in response.result){
					var attrArray = {
						class: "panel-group",
						role: "tablist",
						FiligranaType: "FieldContainer",
						FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id
					};					
					var container = $.getApp().pages[targetPage].fieldcontainers.ResearchGroupContainer.appendFieldContainer(attrArray.FiligranaName, 'div', attrArray);
					
					
						attrArray2 = {
								class: "panel panel-default",
								FiligranaType: "FieldContainer",
								FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"Panel"
							};					
						var container2 = container.appendFieldContainer(attrArray2.FiligranaName, 'div', attrArray2);
						
						
							var colid = "collapseListGroupHeading"+response.result[i].reg_id;
							attrArray3 = {
									id : colid,
									class: "panel-heading",
									FiligranaType: "FieldContainer",
									FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"PanelHeading",
									role: "tab"
								};					
							var container3 = container2.appendFieldContainer(attrArray3.FiligranaName, 'div', attrArray3);
								
								
								attrArray4 = {
										
										class: "row",
										FiligranaType: "FieldContainer",
										FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"PanelHeadingRow"
									};
								var container4 = container3.appendFieldContainer(attrArray4.FiligranaName, 'div', attrArray4);
									
									
									attrArray5 = {
											
											class: "col-xs-6",
											FiligranaType: "FieldContainer",
											FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"PanelHeadingBootstrapSize1"
										};
									var container5 = container4.appendFieldContainer(attrArray5.FiligranaName, 'div', attrArray5);
									
										attrArray6 = {
												
												class: "panel-title",
												FiligranaType: "FieldContainer",
												FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"PanelHeadingTitle"
											};
										var container6 = container5.appendFieldContainer(attrArray6.FiligranaName, 'h4', attrArray6);
										var colidtab = "collapseListGroup"+response.result[i].reg_id;	
											attrArray7 = {
													
													class : "collapseA",
													aria__controls: colidtab,
													aria__expanded: false,
													href: "",
													target : "collapseListGroup"+response.result[i].reg_id,
													data__target: "ResearchGroupContainer"+response.result[i].reg_id+"collapseListgroup",
													data__toggle : "collapse",
													FiligranaType: "Field",
													FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"PanelHeadingCollapseA"
												
												};
											var container7 = container6.appendField(attrArray7.FiligranaName, 'a', attrArray7);
								
											container7.set(response.result[i].name);
										
										attrArray12 = {
												
												class: "col-xs-3",
												FiligranaType: "FieldContainer",
												FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"PanelHeadingBootstrapSize3"
											};
										var container12 = container4.appendFieldContainer(attrArray12.FiligranaName, 'div', attrArray12);
										
											
											if(response.result[i].edit == 1){ 
												attrArray8 = {
														class: "clickable btn btn-lg plusButton pull-left",
														FiligranaType: "Field",
														FiligranaAction: "renameResearchGroup",
														FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"PanelHeadingEditButton",
														resId: response.result[i].reg_id,
														value : "",
														type : 'button'
													};
												var container8 = container12.appendFieldContainer(attrArray8.FiligranaName, 'button', attrArray8);
													attrArray11 = {
															class: "glyphicon glyphicon-pencil",
															FiligranaType: "Field",
															FiligranaName: "ResearchGroup"+response.result[i].reg_id+"EditIcon"
														};
													var container11 = container8.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
											}
									attrArray9 = {
											
											class: "col-xs-3",
											FiligranaType: "FieldContainer",
											FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"PanelHeadingBootstrapSize2"
										};
									var container9 = container4.appendFieldContainer(attrArray9.FiligranaName, 'div', attrArray9);
										if(response.result[i].edit == 1){ 
											attrArray10 = {
												type: 'button',
												res_id: ""+response.result[i].reg_id,
												class: "clickable btn btn-lg plusButton fix_button_right deleteResearchGroup",
												FiligranaAction: "deleteResearchGroup",
												page: this.targetPage,
												FiligranaType: "FieldContainer",
												FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"PanelHeadingDeleteButton"
											};
											var container10 = container9.appendFieldContainer(attrArray10.FiligranaName, 'div', attrArray10);
												attrArray11 = {
														class: "glyphicon glyphicon-trash",
														FiligranaType: "Field",
														FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"DeleteIcon"
													};
												var container11 = container10.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
						
										
									
										}
							
							
							attrArray3 = {
									id : colidtab,
									class: "panel-collapse collapse",
									aria__labelledby: colid ,
									area__expanded : "true",
									role: "tabpanel",
									href : "",
									FiligranaType: "FieldContainer",
									FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"collapseListgroup"
								};	
				
							var container15 = container2.appendFieldContainer(attrArray3.FiligranaName, 'div', attrArray3);
							
								attrArray3 = {
										
										class: "list-group",
										
										FiligranaType: "FieldContainer",
										FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"collapseListgroupUL"
									};					
								var container16 = container15.appendFieldContainer(attrArray3.FiligranaName, 'ul', attrArray3);
								
								
								if(response.result[i].edit){
									var attrArray = {
											class: "list-group-item",				
											FiligranaType: "FieldContainer",
											FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"collapseListgroupAddUserLi"
										};
										var containeraddUser = container16.appendFieldContainer(attrArray.FiligranaName, 'li', attrArray);
										
											var attrArray = {
													class: "row Userrow",				
													FiligranaType: "FieldContainer",
													FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"collapseListgroupAddUserRow"
												};
											var containeraddUser = containeraddUser.appendFieldContainer(attrArray.FiligranaName, 'div', attrArray);
											
												var attrArray = {
														class: "col-xs-3",				
														FiligranaType: "FieldContainer",
														FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"collapseListgroupAddUserRow"
													};
												var containeraddUser = containeraddUser.appendFieldContainer(attrArray.FiligranaName, 'div', attrArray);
												
													var attrArray = {
															type:'button',
															id : "addPersonToGroup"+response.result[i].reg_id,
															reg_id : response.result[i].reg_id,
															FiligranaAction:"addPersonToGroup",
															FiligranaType: "Field",
															FiligranaName: "ResearchGroupContainer"+response.result[i].reg_id+"collapseListgroupAddUserButton",
															page: this.targetPage,
															class:"clickable btn btn-lg plusButton bigbutton pull-left"
														};					
													var fieldAdd = containeraddUser.appendFieldContainer(attrArray.FiligranaName, 'button', attrArray);
														attrArray11 = {
																class: "glyphicon glyphicon-plus green-glaphicolon",
																FiligranaType: "Field",
																FiligranaName: "ResearchGroup"+response.result[i].reg_id+"addUserIcon"
															};
														var container11 = fieldAdd.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
								}
								
								
								for(var j in response.result[i].users){
									$.getApp().getUser(container16, response.result[i].reg_id, response.result[i].users[j],response.result[i].edit );
								}
										
										
				}
				
				
				
				
				$.getApp().resetClickActions();
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
			
			
		}
	});
	

}

Filigrana.prototype.getUser = function(container, regid, userid, edit, clear){
	
	var post = {
			action : "getUser",
			usr_id : userid,
			res_id : regid
	};

	$.ajax({
		type: "POST",
		url: this.parameters.server,
		data: post,
		reg_id : regid,
		user_id: userid,
		target_container : container,
		isEdit : edit,
		dataType: "json",
		success: function(response){
			if(response.check == "suc"){
				
				
				var attrArray = {
					class: "list-group-item",				
					FiligranaType: "FieldContainer",
					FiligranaName: "ResearchGroupContainer"+this.reg_id+"collapseListgroup"+this.user_id+"Li"
				};
				var container2 = this.target_container.appendFieldContainer(attrArray.FiligranaName, 'li', attrArray);
				
					var attrArray = {
							class: "row Userrow",				
							FiligranaType: "FieldContainer",
							FiligranaName: "ResearchGroupContainer"+this.reg_id+"collapseListgroup"+this.user_id+"Row"
						};
					var container2 = container2.appendFieldContainer(attrArray.FiligranaName, 'div', attrArray);
					
						var attrArray = {
								class: "col-xs-4",				
								FiligranaType: "Field",
								FiligranaName: "ResearchGroupContainer"+this.reg_id+"collapseListgroup"+this.user_id+"Bootstrapsize1"
							};
						var container3 = container2.appendField(attrArray.FiligranaName, 'div', attrArray);
			
						container3.set(response.result.name);
						/*
						var attrArray = {
								class: "col-xs-",				
								FiligranaType: "Field",
								FiligranaName: "ResearchGroupContainer"+this.reg_id+"collapseListgroup"+this.user_id+"Bootstrapsize2"
							};
						var container4 = container2.appendField(attrArray.FiligranaName, 'div', attrArray);
						container4.set(response.result.email);*/
						
						var attrArray = {
								class: "col-xs-4",				
								FiligranaType: "FieldContainer",
								FiligranaName: "ResearchGroupContainer"+this.reg_id+"collapseListgroup"+this.user_id+"Bootstrapsize3",
								userrole : response.result.userrole
							};
						var container4 = container2.appendFieldContainer(attrArray.FiligranaName, 'div', attrArray);
							var attrArray = {
									class: "select select-Userrole",				
									FiligranaType: "FieldContainer",
									FiligranaName: "ResearchGroupContainer"+this.reg_id+"collapseListgroup"+this.user_id+"select",
									FiligranaAction: "changeUserRights",
									reg_id : this.reg_id,
									userrole : response.result.userrole,
									user_id : this.user_id
								};
							var container4 = container4.appendFieldContainer(attrArray.FiligranaName, 'select', attrArray);
							$.getApp().getUserRights(container4, response.result.userrole, this.reg_id, this.user_id);
							
						var attrArray = {
								class: "col-xs-4",				
								FiligranaType: "FieldContainer",
								FiligranaName: "ResearchGroupContainer"+this.reg_id+"collapseListgroup"+this.user_id+"Bootstrapsize4",
								userrole : response.result.userrole
							};
						var container4 = container2.appendFieldContainer(attrArray.FiligranaName, 'div', attrArray);
							if(this.isEdit){ 
								var attrArray = {
										type:'button',
										userID : this.user_id,
										resID: this.reg_id,
										class :"clickable btn btn-lg removeUserFromGroup pull-right fix_button_right plusButton",				
										FiligranaType: "FieldContainer",
										FiligranaName: "ResearchGroupContainer"+this.reg_id+"collapseListgroup"+this.user_id+"deleteButton",
										FiligranaAction: "removeUserFromResearchGroup"
									};
								var container4 = container4.appendFieldContainer(attrArray.FiligranaName, 'button', attrArray);
									attrArray11 = {
											class: "glyphicon glyphicon-trash",
											FiligranaType: "Field",
											FiligranaName: "ResearchGroupContainer"+this.reg_id+"collapseListgroup"+this.user_id+"deleteIcon",
										};
									var container11 = container4.appendField(attrArray11.FiligranaName, 'span', attrArray11);
							}	
							
							
							$.getApp().resetClickActions();
			
			}else{
				$getApp().Ajax_handleError(response);
			}
		}
	});
}


Filigrana.prototype.getUserRights = function(container, userRoleId, regId, userid){
	var post = {
			action : "getUserRoles",
			query : ""
	};

	$.ajax({
		type: "POST",
		url: this.parameters.server,
		data: post,
		userRoleId : userRoleId,
		reg_id : regId,
		user_id : userid,
		target_container : container,
		dataType: "json",
		success: function(response){
		
			if(response.check == "suc"){
				for(var i in response.result){
					
						attrArray11 = {
								class: "userrights-option",
								value : response.result[i].id,
								FiligranaType: "Field",
								FiligranaName: "ResearchGroupContainer"+this.reg_id+"collapseListgroup"+this.user_id+"SelectUserrightsoption"+response.result[i].id,
							};
					if(response.result[i].id == this.userRoleId)attrArray11.selected = "true";
					var container11 = this.target_container.appendField(attrArray11.FiligranaName, 'option', attrArray11);
					container11.set(response.result[i].name);
				}
				
			}else{
				this.Ajax_handleError(response);
			}
		}
	});
}



Filigrana.prototype.changeToEditUserGroup = function(res_id, page){
	var editButtoncontainer = 
	WebApp.pages[page].fieldcontainers.ResearchGroupContainer.
	fieldcontainers["ResearchGroupContainer"+res_id].fieldcontainers["ResearchGroupContainer"+res_id+"Panel"].
	fieldcontainers["ResearchGroupContainer"+res_id+"PanelHeading"].
	fieldcontainers["ResearchGroupContainer"+res_id+"PanelHeadingRow"].
	fieldcontainers["ResearchGroupContainer"+res_id+"PanelHeadingBootstrapSize3"];
	
	editButtoncontainer.remove("ResearchGroupContainer"+res_id+"PanelHeadingEditButton");
	
	attrArray8 = {
			class: "clickable btn btn-lg plusButton pull-left",
			FiligranaType: "Field",
			FiligranaAction: "saveEditResearchGroup",
			FiligranaName: "ResearchGroupContainer"+res_id+"PanelHeadingEditButton",
			resId: res_id,
			value : "",
			type : 'button'
		};
	var editButton = editButtoncontainer.appendFieldContainer(attrArray8.FiligranaName, 'button', attrArray8);
		attrArray11 = {
				class: "glyphicon glyphicon-floppy-open",
				FiligranaType: "Field",
				FiligranaName: "WatermarkSaveIcon"
			};
		var container11 = editButton.appendField(attrArray11.FiligranaName, 'span', attrArray11);
	
	var nameContainer = 
		WebApp.pages[page].fieldcontainers.ResearchGroupContainer.
		fieldcontainers["ResearchGroupContainer"+res_id].fieldcontainers["ResearchGroupContainer"+res_id+"Panel"].
		fieldcontainers["ResearchGroupContainer"+res_id+"PanelHeading"].
		fieldcontainers["ResearchGroupContainer"+res_id+"PanelHeadingRow"].
		fieldcontainers["ResearchGroupContainer"+res_id+"PanelHeadingBootstrapSize1"];
	var oldvalue = this.getField("ResearchGroupContainer"+res_id+"PanelHeadingCollapseA");
	
	nameContainer.remove("ResearchGroupContainer"+res_id+"PanelHeadingTitle");
	
	attrArray8 = {
			FiligranaName : "ResearchGroupContainer"+res_id+"PanelHeadingTextInput",
			FiligranaType :"Field",
			type : "text", 
			class:"form-control text textInputField",
			oldvalue : oldvalue
		};
	var inputField = nameContainer.appendField(attrArray8.FiligranaName, 'input', attrArray8);
	inputField.set(oldvalue);
	
	$.getApp().resetClickActions();
	
	
}

Filigrana.prototype.saveEditResearchGroup = function(res_id){
	
	var value = this.getField("ResearchGroupContainer"+res_id+"PanelHeadingTextInput");
	var oldvalue = $('[FiligranaName="ResearchGroupContainer'+res_id+'PanelHeadingTextInput"]').attr("oldvalue");
	var container = this.getField("ResearchGroupContainer"+res_id+"PanelHeadingBootstrapSize1");
	
	var buttonContainer = WebApp.getField("ResearchGroupContainer"+res_id+"PanelHeadingBootstrapSize3");
	
	var post = {
			action: "editResearchGroup",
			res_id : res_id,
			newname: value,
			newuser: -1,
			role_id:-1,
			edituser: -1,
			removeuser: -1,
			del: false
	};

	$.ajax({
		type: "POST",
		url: this.parameters.server,
		data: post,
		reg_id : res_id,
		target_container : container,
		newvalue : value,
		oldvalue : oldvalue,
		buttonContainer: buttonContainer,
		dataType: "json",
		success: function(response){
			
			this.buttonContainer.remove("ResearchGroupContainer"+this.reg_id+"PanelHeadingEditButton");
			
			attrArray8 = {
					class: "clickable btn btn-lg plusButton pull-left",
					FiligranaType: "Field",
					FiligranaAction: "renameResearchGroup",
					FiligranaName: "ResearchGroupContainer"+this.reg_id+"PanelHeadingEditButton",
					resId: this.reg_id,
					value : "",
					type : 'button'
				};
			var editButton = this.buttonContainer.appendFieldContainer(attrArray8.FiligranaName, 'button', attrArray8);
				attrArray11 = {
						class: "glyphicon glyphicon-pencil",
						FiligranaType: "Field",
						FiligranaName: "ResearchGroup"+this.reg_id+"EditIcon"
					};
				var container11 = editButton.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
			
			this.target_container.remove("ResearchGroupContainer"+this.reg_id+"PanelHeadingTextInput");
			
			
			attrArray6 = {
					
					class: "panel-title",
					FiligranaType: "FieldContainer",
					FiligranaName: "ResearchGroupContainer"+this.reg_id+"PanelHeadingTitle"
				};
			var container6 = this.target_container.appendFieldContainer(attrArray6.FiligranaName, 'h4', attrArray6);
			var colidtab = "collapseListGroup"+this.reg_id;	
			
				attrArray7 = {
						
						aria__controls: colidtab,
						aria__expanded: false,
						href: "",
						class: "collapseA",
						data__toggle : "collapse",
						target : "collapseListGroup"+this.reg_id,
						data__target: "ResearchGroupContainer"+this.reg_id+"collapseListgroup",
						FiligranaType: "Field",
						FiligranaName: "ResearchGroupContainer"+this.reg_id+"PanelHeadingCollapseA"
					
					};
				var container7 = container6.appendField(attrArray7.FiligranaName, 'a', attrArray7);
	
			
			
			
			
			var value = this.oldvalue;
			
			if(response.check == "suc"){
				value = this.newvalue;
				
			
				
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
			
			
			container7.set(value);
			$.getApp().resetClickActions();
		}
	});
	
	
	
}






Filigrana.prototype.fadeInNewUser = function(reg_id, page){
	this.currentRegId = reg_id;
	this.currentToPage = page;

	
	$('#newUserModal_page').modal('show');
	$('#submitAddUser').attr("regid", reg_id);
	$.getApp().resetClickActions();
}


Filigrana.prototype.submitAddUser = function(regId){
	console.log(regId);
	var post = {
			action : "invitateUser",
			researchGroup : this.currentRegId,
			email : this.getField("newUserEmail")
	};

	$.ajax({
		type: "POST",
		url: this.parameters.server,
		data: post,
		regId : regId,
		dataType: "json",
		success: function(response){
			
			if(response.check == "suc"){
				console.log("ResearchGroupContainer"+this.regId+"collapseListgroupUL");
				var container = $.getApp().getField("ResearchGroupContainer"+this.regId+"collapseListgroupUL");
				
				$.getApp().getUser(container, this.regId, response.result, 1);
				$('#newUserModal_page').modal('hide');
			}else{
				$.getApp().Ajax_handleError(response);
			}
			
			
		}
	});
}

Filigrana.prototype.addResearchGroup = function(targetpage){
	
	var post = {
			action : "newResearchgroup",
			name : "new Research Group"
	};

	$.ajax({
		targetpage: targetpage,
		type: "POST",
		url: this.parameters.server,
		data: post,
		dataType: "json",
		success: function(response){

			if(response.check == "suc"){
				$.getApp().getResearchGroups(this.targetpage);
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
			
			
		}
	});
}

Filigrana.prototype.deleteResearchGroup = function(targetpage, reg_id){
	
	var oldvalue = this.getField("ResearchGroupContainer"+reg_id+"PanelHeadingCollapseA");
	var r = confirm("Do you really want to delete the Research Group "+oldvalue+"?");
	if (r == true) {
		
		
		var post = {
				action: "editResearchGroup",
				res_id : reg_id,
				newname: "",
				role_id:-1,
				newuser: -1,
				edituser: -1,
				removeuser: -1,
				del: true
		};

		$.ajax({
			type: "POST",
			url: this.parameters.server,
			data: post,
			reg_id : reg_id,
			targetpage: targetpage,
			dataType: "json",
			success: function(response){	
				if(response.check == "suc"){
					$.getApp().getResearchGroups(this.targetpage);
				
					
					
				}else{
					$.getApp().Ajax_handleError(response);
				}
			}
		});
	} else {
	    
	}
}

Filigrana.prototype.removeUserFromResearchGroup = function(targetpage, reg_id, usr_id){
	var oldvalue = this.getField("ResearchGroupContainer"+reg_id+"PanelHeadingCollapseA");
	var name = this.getField("ResearchGroupContainer"+reg_id+"collapseListgroup"+usr_id+"Bootstrapsize1");
	var r = confirm("Do you really want to remove the User "+ name+" from the Research Group "+oldvalue+"?");
	if (r == true) {
		
		
		var post = {
				action: "editResearchGroup",
				res_id : reg_id,
				newname: "",
				role_id:-1,
				newuser: -1,
				edituser: -1,
				removeuser: usr_id,
				del: false
		};

		$.ajax({
			type: "POST",
			url: this.parameters.server,
			data: post,
			reg_id : reg_id,
			usr_id: usr_id,
			targetpage: targetpage,
			dataType: "json",
			success: function(response){	
				if(response.check == "suc"){
					$.getApp().getField("ResearchGroupContainer"+reg_id+"collapseListgroupUL").remove("ResearchGroupContainer"+reg_id+"collapseListgroup"+usr_id+"Li");
					
					
					
				}else{
					$.getApp().Ajax_handleError(response);
				}
			}
		});
	} else {
	    
	}
}




Filigrana.prototype.changeUserRights = function(reg_id, usr_id, uro_id){
	var post = {
			action: "editResearchGroup",
			res_id : reg_id,
			newname: "",
			role_id: uro_id,
			newuser: -1,
			edituser: usr_id,
			removeuser: -1,
			del: false
	};

	$.ajax({
		type: "POST",
		url: this.parameters.server,
		data: post,
		reg_id : reg_id,
		usr_id: usr_id,
		dataType: "json",
		success: function(response){	
			console.log(response);
			if(response.check == "suc"){
				
				
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
		}
	});
}

Filigrana.prototype.displayRegisterUser = function(targetPage,hash){
	
	var post = {
			action: "checkHash",
			hash : hash
	};

	$.ajax({
		type: "POST",
		url: this.parameters.server,
		data: post,
		targetPage :targetPage,
		dataType: "json",
		success: function(response){	
	
			if(response.check == "suc"){
			
				var Field = $.getApp().pages[this.targetPage].getFieldNotValue("ChoosePasswordTitle");
				$.getApp().pages['login_page'].getFieldNotValue("loginEmail").setAll(response.email);
				Field.set("Choose Username/Password for your account email: "+response.email);
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
		}
	});
	
}

Filigrana.prototype.displaySetPassword = function(targetPage,hash){
	
	var post = {
			action: "checkHash",
			hash : hash
	};

	$.ajax({
		type: "POST",
		url: this.parameters.server,
		data: post,
		targetPage :targetPage,
		dataType: "json",
		success: function(response){	
	
			if(response.check == "suc"){
			
				var Field = $.getApp().pages[this.targetPage].getFieldNotValue("ChoosePasswordTitle");
				$.getApp().pages['login_page'].getFieldNotValue("loginEmail").setAll(response.email);
				Field.set("Choose Password for your account email: "+response.email);
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
		}
	});
	
}

Filigrana.prototype.registerUser = function(targetPage){
	var username = this.getField("registerUsername");
	var password = this.getField("registerPassword");
	var password2 = this.getField("registerPasswordRepeat");
	var post = {
			action: "RegisterUser",
			username: username,
			pw1: password,
			pw2: password2
	};

	$.ajax({
		type: "POST",
		url: this.parameters.server,
		data: post,
		data2: post,
		targetPage :targetPage,
		dataType: "json",
		success: function(response){	
	
			if(response.check == "suc"){
				
				$.getApp().pages['manage_user_page'].getFieldNotValue("Username").setAll(this.data2.username);
				$.getApp().switchPage(targetPage);
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
		}
	});
}

Filigrana.prototype.changeUser = function(username, email, pwold, pwnew, pwnew2, del){
	var post = {
			action: "EditUser",
			username: username,
			email : email,
			oldpw: pwold,
			newpw: pwnew,
			newpw2: pwnew2,
			del : del
	};

	$.ajax({
		type: "POST",
		url: this.parameters.server,
		data: post,
		data2 : post,
		dataType: "json",
		success: function(response){	
	
			if(response.check == "suc"){
				$.getApp().switchPage("manage_user_page");
				if(this.data2.username != -1){
					$.getApp().pages.manage_user_page.setField("Username", this.data2.username );
				}
				if(this.data2.email != -1){
					$.getApp().pages.manage_user_page.setField("loginEmail", this.data2.email );
				}
				$.getApp().pages.change_email_page.setField("changeEmailPassword", "");
				$.getApp().pages.change_password_page.setField("changePasswordOld", "");
				$.getApp().pages.change_password_page.setField("changePasswordNew", "");
				$.getApp().pages.change_password_page.setField("changePasswordNew2", "");
				
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
		}
	});
}