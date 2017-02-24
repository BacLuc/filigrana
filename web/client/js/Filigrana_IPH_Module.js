/**
 * 
 */



Filigrana.prototype.lookUpIphType = function(targetPage){
	//TODO upload sketch, get path
	$(".page").hide();
	$("#loader_page").show();
	var post = {
			action : "SearchIPHTypes"
	};
	
	if(this.currentIPHSearchResult != undefined){
		if( $('#draw')[0].toDataURL() == this.currentWMSketch){
			
			//this.parameters.showWMSearchResult(targetPage, this.currentWMSearchResult);
		}
		else{
			delete this.currentIPHSearchResult;
			this.lookUpIphType(targetPage);
		}
	}else{ 
	
		
		$.ajax({
			targetPage : targetPage,
			type: "POST",
			url: $.getApp().parameters.server,
			data: post,
			targetPage : targetPage,
			dataType: "json",
			success: function(response){
				if(response.check == "suc"){
					$.getApp().currentIPHSearchResult = response;
					$.getApp().showIPHresults(this.targetPage, response);
				}else{
					$.getApp().Ajax_handleError(response);
				}
				
				
			}
		});
	}
}

Filigrana.prototype.lookUpSimilarIPH = function(targetPage, iphid, picid){
	//TODO upload sketch, get path
	$(".page").hide();
	$("#loader_page").show();
	var post = {
			action : "SearchIPHTypes",
			iphid: iphid,
			picid: picid
	};
	
	if(this.currentIPHSearchResult != undefined){
		if( post.iphid == this.currentSearchSimilarIphid && post.picid == this.currentSearchSimilarPicid){
			
			//this.parameters.showWMSearchResult(targetPage, this.currentWMSearchResult);
		}
		else{
			delete this.currentIPHSearchResult;
			this.lookUpSimilarIPH(targetPage, iphid, picid);
		}
	}else{ 
	
		
		$.ajax({
			targetPage : targetPage,
			type: "POST",
			url: $.getApp().parameters.server,
			data: post,
			targetPage : targetPage,
			dataType: "json",
			success: function(response){
				if(response.check == "suc"){
					$.getApp().currentIPHSearchResult = response;
					$.getApp().showIPHresults(this.targetPage, response);
				}else{
					$.getApp().Ajax_handleError(response);
				}
				
				
			}
		});
	}
}

Filigrana.prototype.showIPHresults = function(targetPage, response){

	
	$('#IPHTypeContainer').empty();
	
	for(var i in response.result){
		
		var attrObject = {
				class: "searchresultrow row",
				topage: "manage_iph_type_page",
				FiligranaAction: "goToManage",
				iphid: response.result[i].id,
				FiligranaType: "FieldContainer",
				FiligranaName: "IPHContainer"+i,
				score : response.result[i].score,
				id : "IPHContainer"+i
		};
		var FieldContainer =$.getApp().pages.choose_iph_type_page.fieldcontainers.chooseIPHTypeContainer.appendFieldContainer("IPHContainer"+i, "div", attrObject);
			var attrArray = {
					type: 'button',
					id : "repeatSearchIPH"+response.result[i].id,
					wmid : response.result[i].id,
					picid : response.result[i].picid,
					class:"clickable btn btn-lg plusButton bigbutton pull-right", 
					FiligranaName:"repeatSearchIPH"+response.result[i].id,
					FiligranaType:"Field",
					FiligranaAction: "repeatSearchIPH"
			}
			var Fieldcontainer2 = FieldContainer.appendFieldContainer(attrArray.FiligranaName, "button", attrArray);
			
				var attrArray = {
						class:"glyphicon glyphicon-repeat", 
						FiligranaName:"repeatSearchIconIPH",
						FiligranaType:"Field"
				}
				var Field = Fieldcontainer2.appendFieldContainer(attrArray.FiligranaName, "span", attrArray);
		
		var request = {
				action : "getIPHType",
				iphid :  response.result[i].id
			}
		$.ajax({
			targetPage : targetPage,
			type: "POST",
			url: $.getApp().parameters.server,
			data: request,
			iphid: response.result[i].id,
			container : FieldContainer,
			score : response.result[i].score,
			dataType: "json",
			success: function(response){
				if(response.check == "suc"){
					console.log(response);
					//image
					
					var attrObject = {
							class: "col-xs-3",
							FiligranaType: "FieldContainer",
							FiligranaName: "IPHContainer"+this.iphid+"AttrContainerImg"
							
					};
					var littlecontainer=this.container.appendFieldContainer("IPHContainer"+this.iphid+"AttrContainerImg", "div", attrObject);
						var attrObject = {
								class: "clickable btn btn-lg plusButton",
								topage: "manage_iph_type_page",
								FiligranaAction: "goToManage",
								iphid: this.iphid,
								FiligranaType: "FieldContainer",
								FiligranaName: "IPHButtonAroundImage"+this.iphid,
								score : this.score,
								id : "IPHButtonAroundImage"+this.iphid
						};
						var k= littlecontainer.appendFieldContainer(attrObject.FiligranaName, "button", attrObject);
						
					
							var attrObject = {
									class: "img-responsive limitedheight",
									src: response.result.img[0].path,
									FiligranaType: "Field",
									FiligranaName: "IPHContainer"+this.iphid+"AttrFieldImg"
									
							};
							k.appendField("IPHContainer"+this.iphid+"AttrFieldImg", "img", attrObject);
					
					
					//score
					var attrObject = {
							class: "col-xs-2",
							FiligranaType: "FieldContainer",
							FiligranaName: "IPHContainer"+i+"AttrContainerScore"
							
					};
					var scoreContainer=this.container.appendFieldContainer("IPHContainer"+this.iphid+"AttrContainerScore", "div", attrObject);
			
					
					
					var attrObject = {
							FiligranaType: "Field",
							FiligranaName: "IPHContainer"+i+"AttrFieldScore"
							
					};
					var scorefield= scoreContainer.appendField("IPHContainer"+this.iphid+"AttrFieldScore", "div", attrObject);
					scorefield.set(this.score);
					
					//name
					var attrObject = {
							class: "col-xs-6",
							FiligranaType: "FieldContainer",
							FiligranaName: "IPHContainer"+this.iphid+"AttrContainerName"
							
					};
					var NameContainer=this.container.appendFieldContainer("IPHContainer"+this.iphid+"AttrContainerName", "div", attrObject);
			
					
					
					var attrObject = {
							src: response.result.img[0],
							FiligranaType: "Field",
							FiligranaName: "IPHContainer"+i+"AttrFieldName"
							
					};
					var nameField= NameContainer.appendField("IPHContainer"+this.iphid+"AttrFieldName", "div", attrObject);
					nameField.set(response.result.name, true);
					
					$.getApp().resetClickActions();
				
				}else{
					$.getApp().Ajax_handleError(response);
				}
			}
		});
		
		//$.getApp().switchPage(targetPage);
		$('.clickable').unbind('click');
		$('.clickable').on('click', function(e){
			//this.clickAction($(this),e);
			//e.stopImmediatePropagation();
			
			$.getApp().clickAction(this, e);
			
		});
		
		
		$.getApp().switchPage(targetPage, true);	
	}
	
	
}


Filigrana.prototype.getIPHDetail = function(targetPage, iphid){
	
	var post = {
			action : "getIPHType",
			iphid : iphid,
			withSession: 1
	};
	
	if(!this.fromSearch){
		this.navmenu.hideImages();
	}
	
	$.ajax({
		targetPage : targetPage,
		type: "POST",
		url: $.getApp().parameters.server,
		data: post,
		iphid:iphid,
		dataType: "json",
		success: function(response){
			if(response.check == "suc"){
				//$($.getApp().pages[this.targetPage].getField("IPHAppendWatermarkContainer").getSelector()).empty();
				//$($.getApp().pages[this.targetPage].getField("DeleteIPHType").getSelector()).empty();
				if(response.result.edit==1){
					/*
					 * <button type='button' id="IPHAppendWatermarkContainer" class="clickable btn btn-lg btn-primary btn-block" topage="manage_watermark_page"
					FiligranaName="IPHAppendWatermarkContainer" FiligranaType="Field" FiligranaAction="IPHAppendWatermarkContainer">create Watermark for IPH Type</button>
					 */
					/*
					var attrArray = {
							type: "button",
							id : "DeleteIPHType",
							class: "clickable btn btn-lg btn-primary pull-right",
							topage: "menu_page",
							FiligranaName: "DeleteIPHTypeButton",
							FiligranaType: "Field",
							FiligranaAction: "DeleteIPHType"
						};*/
					//var Field =$.getApp().pages[targetPage].getField("DeleteIPHType").appendField(attrArray.FiligranaName, "button", attrArray);
					//Field.set("delete IPH Type");
					
					/*
					var attrArray = {
						type: "button",
						id : "IPHAppendWatermarkButton",
						class: "clickable btn btn-lg btn-primary btn-block",
						topage: "manage_watermark_page",
						FiligranaName: "IPHAppendWatermarkButton",
						FiligranaType: "Field",
						FiligranaAction: "IPHAppendWatermark"
					};
					var Field =$.getApp().pages[targetPage].getField("IPHAppendWatermarkContainer").appendField(attrArray.FiligranaName, "button", attrArray);
					Field.set("create Watermark under IPH Type");*/
					
					
				}
				var type = "searcher";
				if(response.result.edit == 1){
					type = "owner";
				}else{
					type = "searcher";
				}
				//todo: admin/owner bestimmen
				$.getApp().navmenu.showIPHButtons(type);
				
				$.getApp().pages[targetPage].setField("IPHName", response.result.name);
				if(response.result.edit !=1){
					$('#editIPHName').hide();
				}else{
					$('#editIPHName').show();
				}
				
				var MetaDataContainer = $.getApp().pages[targetPage].getField('IPHMetadataContainer');
				$(MetaDataContainer.getSelector()).empty();
				for(var i in response.result.metadata){
					$.getApp().displayPropertyIPH(MetaDataContainer, response.result.metadata[i], this.wmid, response.result.edit);
				}
				if(response.result.edit ==1){
					var attrArray = {
							type: 'button',
							id : "addIPHMetaData",
							class:"clickable btn btn-lg plusButton", 
							FiligranaName:"IPHAddMetadata",
							FiligranaType:"Field",
							FiligranaAction: "IPHAddMetadata",
							style: "margin-left: 90px"
					}
					var Fieldcontainer = MetaDataContainer.appendFieldContainer(attrArray.FiligranaName, "button", attrArray);
						var attrArray = {
								class:"glyphicon glyphicon-plus green-glaphicolon", 
								FiligranaName:"IPHAddMetadata",
								FiligranaType:"Field",
						}
						var Field = Fieldcontainer.appendFieldContainer(attrArray.FiligranaName, "span", attrArray);
					
				}
				
				var PhotoContainer = $.getApp().pages[targetPage].getField('IPHImageContainer');
				$(PhotoContainer.getSelector()).empty();
				for(var i in response.result.img){
					$.getApp().displayImageIPH(PhotoContainer, response.result.img[i], "Photo", this.iphid, response.result.edit);
				}
				if(response.result.edit ==1){
					var attrObject = {
							class: "col-xs-2",
							FiligranaType: "FieldContainer",
							FiligranaName: "IPHPhotoContaineryetanotherdiv"	
					}
					var anotherdiv=PhotoContainer.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
						var attrArray = {
								type: 'button',
								id : "addIPHImage",
								class:"clickable btn btn-lg plusButton pull-right", 
								FiligranaName:"IPHAddImage",
								FiligranaType:"Field",
								FiligranaAction: "IPHAddImage"
						}
						var Fieldcontainer=anotherdiv.appendFieldContainer(attrArray.FiligranaName, "button", attrArray);
							var attrArray = {
									class:"glyphicon glyphicon-plus green-glaphicolon", 
									FiligranaName:"IPHAddImage",
									FiligranaType:"Field",
							}
							var Field = Fieldcontainer.appendFieldContainer(attrArray.FiligranaName, "span", attrArray);
					/*
					if($.getApp().fromSearch){ 
						var attrArray = {
								type: 'button',
								id : "appendIPHImage",
								class:"clickable btn btn-lg btn-primary", 
								FiligranaName:"IPHAppendImage",
								FiligranaType:"Field",
								FiligranaAction: "IPHAppendImage",
								iphid : this.iphid
						}
						var Field=PhotoContainer.appendField(attrArray.FiligranaName, "button", attrArray);
						Field.set('add current Image');
					}*/
				}
				
				var SketchContainer =  $.getApp().pages[targetPage].getField('IPHSketchContainer');
				$(SketchContainer.getSelector()).empty();
				for(var i in response.result.sketches){
					$.getApp().displayImageIPH(SketchContainer, response.result.sketches[i], "Sketch", this.iphid, response.result.edit);
				}
				if(response.result.edit ==1){
					var attrObject = {
							class: "col-xs-2",
							FiligranaType: "FieldContainer",
							FiligranaName: "IPHSketchContaineryetanotherdiv"	
					}
					var anotherdiv=SketchContainer.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
						var attrArray = {
								type: 'button',
								id : "addIPHSketch",
								class:"clickable btn btn-lg plusButton pull-right", 
								FiligranaName:"IPHAddSketch",
								FiligranaType:"Field",
								FiligranaAction: "IPHAddSketch"
						}
						var Field=anotherdiv.appendFieldContainer(attrArray.FiligranaName, "button", attrArray);
							var attrArray = {
									class:"glyphicon glyphicon-plus green-glaphicolon", 
									FiligranaName:"IPHAddImage2",
									FiligranaType:"Field"
							}
							var Field = Field.appendFieldContainer(attrArray.FiligranaName, "span", attrArray);
					/*
					if($.getApp().fromSearch){ 
						var attrArray = {
								type: 'button',
								id : "appendIPHSketch",
								class:"clickable btn btn-lg btn-primary", 
								FiligranaName:"IPHAppendSketch",
								FiligranaType:"Field",
								FiligranaAction: "IPHAppendSketch",
								iphid : this.iphid
						}
						var Field=SketchContainer.appendField(attrArray.FiligranaName, "button", attrArray);
						Field.set('add current Sketch');
					}*/
				}
				var timer = setInterval(function () {
					console.log("hiding panel");
					//$('#IPHcollapseMetadata').collapse('hide');
					//$('#IPHcollapseSketch').collapse('hide');
					//$('#addIPHMetaData').unbind('click');
					clearInterval(timer);
				 }, 800);
				$.getApp().resetClickActions();
				
			}else{
				//$.getApp().Ajax_handleError(response);
				console.log(response);
			}
		
		
		}
	});
}


Filigrana.prototype.displayPropertyIPH = function(container, PropertyData, iphid, edit){
	
	var attrArray = {
			class: "row",
			FiligranaName : "IPHMetaRow"+ PropertyData.id,
			FiligranaType : "FieldContainer",
			style: "min-height : 40px;"
	}
	var container = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
		var attrArray = {
				class:"col-xs-1", 
				FiligranaName:"IPHMetaPropertyPlaceholder" + PropertyData.id,
				FiligranaType:"Field"	
		}
		var Field = container.appendField(attrArray.FiligranaName, "div", attrArray);
		var attrArray = {
				class:"col-xs-4 wrapfield", 
				FiligranaName:"IPHMetaProperty" + PropertyData.id,
				FiligranaType:"Field"	
		}
		var Field = container.appendField(attrArray.FiligranaName, "div", attrArray);
		Field.set(PropertyData.property);
		
		var attrArray = {
				class:"col-xs-5 wrapfield", 
				FiligranaName:"IPHMetaValue" + PropertyData.id,
				FiligranaType:"Field"	
		}
		var Field = container.appendField(attrArray.FiligranaName, "div", attrArray);
		Field.set(PropertyData.value);
		
		if(edit == 1){ 
			var attrArray = {
					class:"col-xs-2", 
					FiligranaName:"IPHMetaButtonDiv" + PropertyData.id,
					FiligranaType:"FieldContainer"	
			}
			var container10 = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
				attrArray10 = {
						type: 'button',
						delid: PropertyData.id,
						iphid: iphid,
						class: "clickable btn btn-lg plusButton fix_button_right",
						FiligranaAction: "deleteIPHMetaProperty",
						page: "manage_iph_type_page",
						FiligranaType: "FieldContainer",
						FiligranaName: "IPHPropertyDeleteButton",
						deltype : "Meta"
					};
					var container10 = container10.appendFieldContainer(attrArray10.FiligranaName, 'div', attrArray10);
						attrArray11 = {
								class: "glyphicon glyphicon-trash",
								FiligranaType: "Field",
								FiligranaName: "IPHProperty"+PropertyData.id+"DeleteIcon"
							};
						var container11 = container10.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
		}
}


Filigrana.prototype.displayImageIPH = function(container, imageData, Sketch, iphid, edit){

	var attrObject = {
			class: "col-xs-10 col-sm-2",
			FiligranaType: "FieldContainer",
			FiligranaName: "IPH"+Sketch+"Container"+imageData.id
			
	};
	var littlecontainer=container.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
		var attrObject = {
				FiligranaType: "FieldContainer",
				FiligranaName: "IPH"+Sketch+"Container"+imageData.id+"anotherdiv"	
		}
		var anotherdiv=littlecontainer.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
	
	
			var attrObject = {
					class: "img-responsive limitedheight",
					src: imageData.path,
					FiligranaType: "Field",
					imgid: imageData.id,
					sketch: Sketch,
					FiligranaName: "IPH"+Sketch+""+imageData.id+"AttrFieldImg"
					
			};
			var image= anotherdiv.appendField(attrObject.FiligranaName, "img", attrObject);
	if(edit == 1){ 
		var attrArray = {
				class:"imagetrashdiv", 
				FiligranaName:"IPH"+Sketch+"Div" + imageData.id,
				FiligranaType:"FieldContainer"	
		}
		var container10 = anotherdiv.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
			attrArray10 = {
					type: 'button',
					delid: imageData.id,
					iphid: iphid,
					class: "clickable btn btn-lg plusButton delImageButton",
					FiligranaAction: "deleteIPHImage",
					page: container.container.getSelector(),
					FiligranaType: "FieldContainer",
					FiligranaName: "IPH"+Sketch+"DeleteButton"+imageData.id,
					deltype : Sketch
				};
				var container10 = container10.appendFieldContainer(attrArray10.FiligranaName, 'div', attrArray10);
					attrArray11 = {
							class: "glyphicon glyphicon-trash",
							FiligranaType: "Field",
							FiligranaName: "IPHImage"+imageData.id+"DeleteIcon"
						};
					var container11 = container10.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
					$('[FiligranaName="Watermark'+Sketch+imageData.id+'AttrFieldImg"]').load(function(e){ 
						var koords = {
								top: $('[FiligranaName="IPH'+$(this).attr('sketch')+$(this).attr('imgid')+'AttrFieldImg"]').offset().top,
								left: $('[FiligranaName="IPH'+$(this).attr('sketch')+$(this).attr('imgid')+'AttrFieldImg"]').offset().left + 
										$('[FiligranaName="IPH'+$(this).attr('sketch')+$(this).attr('imgid')+'AttrFieldImg"]').width()
						};
						$('[FiligranaName="IPH'+$(this).attr('sketch')+'Div' + $(this).attr('imgid')+'"]').offset(koords);
					});
	}
	
}
// not used anymore
Filigrana.prototype.changeToEditIPHName = function(page){
	
	var editButtoncontainer = this.pages[page].getField("IPHNameEditButtonContainer");
		
		editButtoncontainer.remove("IPHNameEditButton");
		
		
		attrArray8 = {
				class: "clickable btn btn-lg plusButton",
				FiligranaType: "Field",
				FiligranaAction: "saveEditIPHName",
				FiligranaName: "IPHNameEditButton",
				type : 'button'
			};
		var editButton = editButtoncontainer.appendFieldContainer(attrArray8.FiligranaName, 'button', attrArray8);
			attrArray11 = {
					class: "glyphicon glyphicon-floppy-open",
					FiligranaType: "Field",
					FiligranaName: "WatermarkProperty"+PropertyData.id+"DeleteIcon"
				};
			var container11 = editButton.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
		
		
		
		$('.IPHname').removeAttr("disabled");
		
		$.getApp().resetClickActions();
}
// not used anymore
Filigrana.prototype.saveIPHNameEdit = function(page){
	var post = {
				action : "editIPHType",
				iphid : this.parameters.get.iphid,
				name : this.getField("IPHName"),
				newMetadata : -1,
				delMetadata: -1,
				newiphtypes : -1,
				deliphtypes : -1,
				newimg : -1,
				delimg : -1,
				newSketch: -1,
				delSketch: -1,
				del : false


			
	};
	$.ajax({
		type: "POST",
		url: $.getApp().parameters.server,
		data: post,
		dataType: "json",
		success: function(response){
			if(response.check == "suc"){
				var editButtoncontainer = $.getApp().pages[page].getField("IPHNameEditButtonContainer");
					
					editButtoncontainer.remove("IPHNameEditButton");
					
					
					attrArray8 = {
							class: "clickable btn btn-lg btn-primary",
							FiligranaType: "Field",
							FiligranaAction: "editIPHName",
							FiligranaName: "IPHNameEditButton",
							type : 'button'
						};
					var editButton = editButtoncontainer.appendField(attrArray8.FiligranaName, 'button', attrArray8);
					editButton.set("edit");
					
					
					
					$('.IPHname').attr("disabled", true);
					
					$.getApp().resetClickActions();
			}else{
				//$.getApp().Ajax_handleError(response);
				console.log(response);
			}
		
		
		}
	});
}


Filigrana.prototype.IPHPropertyDelete = function(page, type, id){
	
	var r = confirm("Do you really want to delete this item ?");
		if (r == true) {
			var post = {
					action : "editIPHType",
					iphid : this.parameters.get.iphid,
					name : -1,
					newMetadata : -1,
					delMetadata: -1,
					newiphtypes : -1,
					deliphtypes : -1,
					newimg : -1,
					delimg : -1,
					newSketch: -1,
					delSketch: -1,
					newiphsupertypes : -1,
					deliphsupertypes: -1,
					newiphsubtypes : -1,
					deliphsubtypes: -1,
					newWatermarks: -1,
					delWatermarks: -1,
					del : false
			};
		
			
			
			
			if(type == 'Meta'){
				post.delMetadata = {
						id: id,
						value : this.getField("IPHMetaValue"+id)
				};
			}else if(type == 'Photo'){
				post.delimg = id;
			}else if(type == 'Sketch'){
				post.delSketch = id;
			}
			
			$.ajax({
				type: "POST",
				url: $.getApp().parameters.server,
				data: post,
				dataType: "json",
				page : page,
				typed : type,
				id : id,
				success: function(response){
					if(response.check == "suc"){
						if(this.typed == 'Meta'){
							$.getApp().pages[this.page].getField("IPHMetaRow"+this.id).remove("IPHMetaRow"+this.id);
						}else{
							$.getApp().pages[this.page].getField("IPH"+this.typed+"Container"+this.id).remove("IPH"+this.typed+"Container"+this.id);
							$.getApp().pages[this.page].getField("IPH"+this.typed+"Div" + this.id).remove("IPH"+this.typed+"Div" + this.id);
							
						}
					}else{
						//$.getApp().Ajax_handleError(response);
						console.log(response);
					}
				
				
				}
			});
		}else{
			
		}
	
	
}

Filigrana.prototype.addMetaDataIPH = function(page, type, id){
	var container = $.getApp().pages[page].getField('IPHMetadataContainer');
	$('#addIPHMetaData').hide();
	var attrArray = {
			class: "row",
			FiligranaName : "IPHMetaRow"+ "new",
			FiligranaType : "FieldContainer",
			style: "min-height : 40px;"
	}
	var container = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
	
		var attrArray = {
				class:"col-xs-5", 
				FiligranaName:"IPHMetaProperty" + "new",
				FiligranaType:"FieldContainer"	
		}
		var container1 = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
		
			attrArray8 = {
					FiligranaName : "IPHMetaPropertyField" + "new",
					FiligranaType :"FieldContainer",
					type : "text", 
					class:"form-control text textInputField"
					
				};
			var inputField = container1.appendField(attrArray8.FiligranaName, 'input', attrArray8);
		
		var attrArray = {
				class:"col-xs-5", 
				FiligranaName:"IPHMetaValue" + "new",
				FiligranaType:"FieldContainer"	
		}
		var container1 = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
			attrArray8 = {
					FiligranaName : "IPHNewValueField",
					FiligranaType :"FieldContainer",
					type : "text", 
					class:"form-control text textInputField"
					
				};
			var inputField = container1.appendField(attrArray8.FiligranaName, 'input', attrArray8);
		
		
		
		var attrArray = {
				class:"col-xs-2", 
				FiligranaName:"IPHMetaValueButton" + "new",
				FiligranaType:"FieldContainer"	
		}
		
		
		var container10 = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
		attrArray10 = {
				type: 'button',
				wmid: this.parameters.get.iphid,
				class: "clickable btn btn-lg plusButton",
				FiligranaAction: "saveNewMetaIPH",
				page: "manage_iph_type_page",
				FiligranaType: "Field",
				FiligranaName: "SaveNewMetaIPHButton",
				topage : page
			};
			var container10 = container10.appendFieldContainer(attrArray10.FiligranaName, 'div', attrArray10);
				attrArray11 = {
						class: "glyphicon glyphicon-floppy-open",
						FiligranaType: "Field",
						FiligranaName: "IPHSaveIcon"
					};
				var container11 = container10.appendField(attrArray11.FiligranaName, 'span', attrArray11);
			$.getApp().resetClickActions();
}

Filigrana.prototype.saveAddMetaDataIPH = function(page){
	var property = this.getField("IPHMetaPropertyFieldnew");
	var value = this.getField("IPHNewValueField");
	var pushObject = {
			key : property,
			value: value
	};
	var post = {
			action : "editIPHType",
			iphid : this.parameters.get.iphid,
			name : -1,
			newMetadata : pushObject,
			delMetadata: -1,
			newiphsupertypes : -1,
			deliphsupertypes : -1,
			newiphsubtypes : -1,
			deliphsubtypes : -1,
			newWatermarks : -1,
			delWatermarks : -1,
			newimg : -1,
			delimg : -1,
			newSketch: -1,
			delSketch: -1,
			del : false
			

		
			
			
	};
	
		$.ajax({
			type: "POST",
			url: $.getApp().parameters.server,
			data: post,
			dataType: "json",
			page : page,
			success: function(response){
				if(response.check == "suc"){
					$.getApp().switchPage("manage_iph_type_page");
				}else{
					$.getApp().Ajax_handleError(response);
					//console.log(response);
				}
			
			
			}
		});
	
}

Filigrana.prototype.deleteIPHType = function(page){
	
	var r = confirm("Do you really want to delete this IPH Type ?");
		if (r == true) {
			var post = {
					action : "editIPHType",
					iphid : this.parameters.get.iphid,
					name : -1,
					newMetadata : -1,
					delMetadata: -1,
					newiphsupertypes : -1,
					deliphsupertypes : -1,
					newiphsubtypes : -1,
					deliphsubtypes : -1,
					newWatermarks : -1,
					delWatermarks : -1,
					newimg : -1,
					delimg : -1,
					newSketch: -1,
					delSketch: -1,
					del : true
			};
			
			
			$.ajax({
				type: "POST",
				targetPage : page,
				url: $.getApp().parameters.server,
				data: post,
				dataType: "json",
				success: function(response){
					if(response.check == "suc"){
						$.getApp().switchPage(this.targetPage);
					}else{
						$.getApp().Ajax_handleError(response);
						//console.log(response);
					}
				
				
				}
			});
		}else{
			
		}
	
	
}

Filigrana.prototype.appendToIPH = function(sketch){
	console.log("appending: "+sketch);
	var post = {
			action : "addCurrent",
			id : this.parameters.get.iphid,
			sketch : sketch,
			type: "iph"
	};
	
	
	$.ajax({
		type: "POST",
		targetPage : "manage_iph_type_page",
		url: $.getApp().parameters.server,
		data: post,
		dataType: "json",
		success: function(response){
			if(response.check == "suc"){
				$.getApp().switchPage(this.targetPage);
			}else{
				$.getApp().Ajax_handleError(response);
				//console.log(response);
			}
		
		
		}
	});

}

Filigrana.prototype.createIPHType = function(page){


	var post = {
			action : "createIPHType",
			
	};
	
		$.ajax({
			type: "POST",
			url: $.getApp().parameters.server,
			data: post,
			dataType: "json",
			page : page,
			success: function(response){
				if(response.check == "suc"){
					delete $.getApp().parameters.get.wmid;
					$.getApp().parameters.get.iphid= response.iphid;
					$.getApp().switchPage("manage_iph_type_page");
				}else{
					$.getApp().Ajax_handleError(response);
					//console.log(response);
				}
			
			
			}
		});
	
}
