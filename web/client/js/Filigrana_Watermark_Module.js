/**
 * 
 */

Filigrana.prototype.initialiseWatermarkModule = function(){ 
	this.currentWMId = "";
	this.currentIPHId = "";
}



Filigrana.prototype.searchWatermarkSketch = function(targetPage){
	//TODO upload sketch, get path

	var post = {
			action : "SearchWatermarks",
			file: $('#draw')[0].toDataURL(),
			starttime: (new Date().getTime()) / 1000
	};
	
	if(this.currentWMSearchResult != undefined){
		if( post.file == this.currentWMSketch){
			console.log("i am here");
			//this.parameters.showWMSearchResult(targetPage, this.currentWMSearchResult);
		}
		else{
			delete this.currentWMSearchResult;
			this.searchWatermarkSketch(targetPage);
		}
	}else{
		
		$(".page").hide();
		$("#loader_page").show();
		$.ajax({
			targetPage : targetPage,
			type: "POST",
			url: $.getApp().parameters.server,
			data: post,
			targetPage : targetPage,
			dataType: "json",
			success: function(response){
				if(response.check == "suc"){
					$.getApp().currentWMSketch = post.file;
					$.getApp().currentWMSearchResult = response;
					$.getApp().showWMSearchResults(this.targetPage, response);
					$.getApp().switchPage(this.targetPage, true);
				}else{
					this.Ajax_handleError(response);
					delete this.currentWMSearchResult;
				}
				
				
			}
		});
	}
}

Filigrana.prototype.getSimilarWatermarks = function(targetPage, wmid, picid){
	console.log("hi");
	var post = {
			action : "SearchWatermarks",
			file: $('#draw')[0].toDataURL(),
			wmid: wmid,
			picid: picid,
			starttime: (new Date().getTime()) / 1000
	};
	
	console.log(post);
	if(this.currentWMSearchResult != undefined){
		if( post.wmid == this.currentSearchSimilarWmid && post.picid == this.currentSearchSimilarPicid){
			//console.log("i am here");
			//this.parameters.showWMSearchResult(targetPage, this.currentWMSearchResult);
		}
		else{
			delete this.currentWMSearchResult;
			this.getSimilarWatermarks(targetPage, wmid, picid);
		}
	}else{
		$(".page").hide();
		$("#loader_page").show();
		$.ajax({
			targetPage : targetPage,
			type: "POST",
			url: $.getApp().parameters.server,
			data: post,
			targetPage : targetPage,
			dataType: "json",
			success: function(response){
				if(response.check == "suc"){
					$.getApp().currentWMSketch = post.file;
					$.getApp().currentWMSearchResult = response;
					$.getApp().showWMSearchResults(this.targetPage, response);
					$.getApp().switchPage(this.targetPage, true);
				}else{
					this.Ajax_handleError(response);
					delete this.currentWMSearchResult;
				}
				
				
			}
		});
	}
}

Filigrana.prototype.showWMSearchResults = function(targetPage, response){
	$('#WatermarkContainer').empty();
	$.getApp().navmenu.setImageUrls(response.pathimage+"?"+ new Date().getTime(), response.pathsketch+"?"+ new Date().getTime());
	for(var i in response.result){
		
		var attrObject = {
				class: "searchresultrow row",
				topage: "manage_watermark_page",
				FiligranaAction: "goToManage",
				wmid: response.result[i].id,
				FiligranaType: "FieldContainer",
				FiligranaName: "WatermarkContainer"+response.result[i].id,
				score : response.result[i].score,
				id : "WatermarkContainer"+response.result[i].id
		};
		var FieldContainer =$.getApp().pages.choose_watermark_page.fieldcontainers.chooseWatermarkContainer.appendFieldContainer( "WatermarkContainer"+response.result[i].id, "div", attrObject);
		
			var attrArray = {
					type: 'button',
					id : "repeatSearchWM"+response.result[i].id,
					wmid : response.result[i].id,
					picid : response.result[i].picid,
					class:"clickable btn btn-lg plusButton bigbutton pull-right", 
					FiligranaName:"repeatSearchWM"+response.result[i].id,
					FiligranaType:"Field",
					FiligranaAction: "repeatSearchWM"
			}
			var Fieldcontainer2 = FieldContainer.appendFieldContainer(attrArray.FiligranaName, "button", attrArray);
			
				var attrArray = {
						class:"glyphicon glyphicon-repeat", 
						FiligranaName:"repeatSearchIcon",
						FiligranaType:"Field"
				}
				var Field = Fieldcontainer2.appendFieldContainer(attrArray.FiligranaName, "span", attrArray);
				
				$('#repeatSearch'+response.result[i].id).offset({
					top: $("#WatermarkContainer"+response.result[i].id).offset().top,
					left: $("#WatermarkContainer"+response.result[i].id).offset().left + 
					$("#WatermarkContainer"+response.result[i].id).width()-
					$("#repeatSearchWM"+response.result[i].id).width()
					
				});
		
		
		var request = {
				action : "getWatermark",
				wmid : response.result[i].id,
				picid : response.result[i].picid,
				starttime: (new Date().getTime()) / 1000
			}
		$.ajax({
			targetPage : targetPage,
			type: "POST",
			wmid : response.result[i].id,
			picid: response.result[i].picid,
			url: $.getApp().parameters.server,
			data: request,
			container : FieldContainer,
			score : response.result[i].score,
			dataType: "json",
			success: function(response){
				if(response.check == "suc"){
					
					
					var attrObject = {
							class: "col-xs-3",
							FiligranaType: "FieldContainer",
							FiligranaName: "WatermarkContainer"+this.wmid+"AttrContainerImg"
							
					};
					var littlecontainer=this.container.appendFieldContainer("WatermarkContainer"+this.wmid+"AttrContainerImg", "div", attrObject);
						
						var attrObject = {
								class: "clickable btn btn-lg plusButton",
								topage: "manage_watermark_page",
								FiligranaAction: "goToManage",
								wmid: this.wmid,
								FiligranaType: "FieldContainer",
								FiligranaName: "WatermarkButtonAroundImage"+this.wmid,
								score : this.score,
								id : "WatermarkButtonAroundImage"+this.wmid
						};
						var k= littlecontainer.appendFieldContainer(attrObject.FiligranaName, "button", attrObject);
							console.log(this.picid);
							
							var imgpath = response.result.img[0].path;
							
							var attrObject = {
									class : "img-responsive limitedheight",
									src: imgpath,
									FiligranaType: "Field",
									FiligranaName: "WatermarkContainer"+this.wmid+"AttrFieldImg"
									
							};
							k.appendField("WatermarkContainer"+this.wmid+"AttrFieldImg", "img", attrObject);
					
					
					//score
					var attrObject = {
							class: "col-xs-2",
							FiligranaType: "FieldContainer",
							FiligranaName: "WatermarkContainer"+this.wmid+"AttrContainerScore"
							
					};
					var scoreContainer=this.container.appendFieldContainer("WatermarkContainer"+this.wmid+"AttrContainerScore", "div", attrObject);
			
					
					
					var attrObject = {
							
							FiligranaType: "Field",
							FiligranaName: "WatermarkContainer"+this.wmid+"AttrFieldScore"
							
					};
					var scorefield= scoreContainer.appendField("WatermarkContainer"+this.wmid+"AttrFieldScore", "div", attrObject);
					scorefield.set(this.score);
					
					//name
					var attrObject = {
							class: "col-xs-6",
							FiligranaType: "FieldContainer",
							FiligranaName: "WatermarkContainer"+this.wmid+"AttrContainerName"
							
					};
					var NameContainer=this.container.appendFieldContainer("WatermarkContainer"+this.wmid+"AttrContainerName", "div", attrObject);
			
					
					
					var attrObject = {
							src: response.result.img[0],
							FiligranaType: "Field",
							FiligranaName: "WatermarkContainer"+this.wmid+"AttrFieldName"
							
					};
					var nameField= NameContainer.appendField("WatermarkContainer"+this.wmid+"AttrFieldName", "div", attrObject);
					nameField.set(response.result.name, true);
					
					$.getApp().resetClickActions();
				
				}else{
					$.getApp().Ajax_handleError(response);
				}
			}
		});
		
		
	$.getApp().resetClickActions();
		
		
			
	}
	
	
}


Filigrana.prototype.getWatermarkDetail = function(targetPage, wmid){
	
	var post = {
			action : "getWatermark",
			wmid : wmid,
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
		wmid:wmid,
		dataType: "json",
		success: function(response){
			if(response.check == "suc"){
				
				/*$($.getApp().pages[this.targetPage].getField("DeleteWatermark").getSelector()).empty();
				if(response.result.edit==1){
					
					var attrArray = {
						type: "button",
						id : "DeleteWatermarkButton",
						class: "clickable btn btn-lg btn-primary pull-right",
						topage: "menu_page",
						FiligranaName: "DeleteWatermarkButton",
						FiligranaType: "Field",
						FiligranaAction: "DeleteWatermark"
					};
					var Field =$.getApp().pages[targetPage].getField("DeleteWatermark").appendField(attrArray.FiligranaName, "button", attrArray);
					Field.set("delete Watermark");
					
					
				}*/
				var type = "searcher";
				if(response.result.edit == 1){
					type = "owner";
				}else{
					type = "searcher";
				}
				//todo: admin/owner bestimmen
				$.getApp().navmenu.showWMButtons(type);
				$.getApp().pages[targetPage].setField("WatermarkName", response.result.name);
				if(response.result.edit !=1){
					$('#editWMName').hide();
				}else{
					$('#editWMName').show();
				}
				
				var MetaDataContainer = $.getApp().pages[targetPage].getField('watermarkMetadataContainer');
				$(MetaDataContainer.getSelector()).empty();
				for(var i in response.result.metadata){
					$.getApp().displayProperty(MetaDataContainer, response.result.metadata[i], this.wmid, response.result.edit);
				}
				if(response.result.edit ==1){
					var attrArray = {
							type: 'button',
							id : "addWMMetaData",
							class:"clickable btn btn-lg plusButton", 
							FiligranaName:"WatermarkAddMetadata",
							FiligranaType:"Field",
							FiligranaAction: "watermarkAddMetadata",
							style: "margin-left: 90px"
					}
					var Fieldcontainer = MetaDataContainer.appendFieldContainer(attrArray.FiligranaName, "button", attrArray);
					
						var attrArray = {
								class:"glyphicon glyphicon-plus green-glaphicolon", 
								FiligranaName:"WatermarkAddMetadata",
								FiligranaType:"Field",
								FiligranaAction: "watermarkAddMetadata"
						}
						var Field = Fieldcontainer.appendFieldContainer(attrArray.FiligranaName, "span", attrArray);
						
					
					
					
					
				}
				
				var PhotoContainer = $.getApp().pages[targetPage].getField('watermarkImageContainer');
				$(PhotoContainer.getSelector()).empty();
				for(var i in response.result.img){
					$.getApp().displayImage(PhotoContainer, response.result.img[i], "Photo", this.wmid, response.result.edit);
				}
				if(response.result.edit ==1){
					var attrObject = {
							class : "col-xs-2",
							FiligranaType: "FieldContainer",
							FiligranaName: "WatermarkPhotoContaineryetanotherdiv"	
					}
					var anotherdiv=PhotoContainer.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
						var attrArray = {
								type: 'button',
								id : "addWMImage",
								class:"clickable btn btn-lg plusButton pull-right", 
								FiligranaName:"WatermarkAddImage",
								FiligranaType:"Field",
								FiligranaAction: "watermarkAddImage"
						}
						var Fieldcontainer=anotherdiv.appendFieldContainer(attrArray.FiligranaName, "button", attrArray);
						
							var attrArray = {
									class:"glyphicon glyphicon-plus green-glaphicolon", 
									FiligranaName:"WatermarkAddImage",
									FiligranaType:"Field",
									FiligranaAction: "watermarkAddImage"
							}
							var Field = Fieldcontainer.appendFieldContainer(attrArray.FiligranaName, "span", attrArray);
						
						/*
						if($.getApp().fromSearch){ 
							var attrArray = {
									type: 'button',
									id : "appendWMImage",
									class:"clickable btn btn-lg btn-primary", 
									FiligranaName:"WatermarkAppendImage",
									FiligranaType:"Field",
									FiligranaAction: "watermarkAppendImage",
									wmid: this.wmid
							}
							var Field=PhotoContainer.appendField(attrArray.FiligranaName, "button", attrArray);
							Field.set('add current Image');
						}
						*/
				}
				
				var SketchContainer =  $.getApp().pages[targetPage].getField('watermarkSketchContainer');
				$(SketchContainer.getSelector()).empty();
				for(var i in response.result.sketches){
					
					$.getApp().displayImage(SketchContainer, response.result.sketches[i], "Sketch", this.wmid, response.result.edit);
				}
				
				if(response.result.edit ==1){
					var attrObject = {
							class : "col-xs-2",
							FiligranaType: "FieldContainer",
							FiligranaName: "WatermarkSketchContaineryetanotherdiv"	
					}
					var anotherdiv=SketchContainer.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
						var attrArray = {
								type: 'button',
								id : "addWMSketch",
								class:"clickable btn btn-lg plusButton pull-right", 
								FiligranaName:"WatermarkAddSketch",
								FiligranaType:"Field",
								FiligranaAction: "watermarkAddSketch"
						}
						var Field=anotherdiv.appendFieldContainer(attrArray.FiligranaName, "button", attrArray);
							var attrArray = {
									class:"glyphicon glyphicon-plus green-glaphicolon", 
									FiligranaName:"WatermarkAddImage2",
									FiligranaType:"Field",
									FiligranaAction: "watermarkAddMetadataImage2"
							}
							var Field = Field.appendFieldContainer(attrArray.FiligranaName, "span", attrArray);
					/*
					if($.getApp().fromSearch){ 
						var attrArray = {
								type: 'button',
								id : "appendWMSketch",
								class:"clickable btn btn-lg btn-primary", 
								FiligranaName:"WatermarkAppendSketch",
								FiligranaType:"Field",
								FiligranaAction: "watermarkAppendSketch",
								wmid: this.wmid
						}
						var Field=SketchContainer.appendField(attrArray.FiligranaName, "button", attrArray);
						Field.set('add current Sketch');
					}*/
				}
				$.getApp().resetClickActions();
				var timer = setInterval(function () {
								console.log("hiding panel");
								//$('#collapseMetadata').collapse('hide');
								//$('#collapseSketch').collapse('hide');
								$('#addWMMetaData').unbind('click');
								$.getApp().resetClickActions();
								clearInterval(timer);
							 }, 800);
				
				
				
			}else{
				$.getApp().Ajax_handleError(response);
				
			}
		
		
		}
	});
}


Filigrana.prototype.displayProperty = function(container, PropertyData, wmid, edit){
	
	var attrArray = {
			class: "row",
			FiligranaName : "WatermarkMetaRow"+ PropertyData.id,
			FiligranaType : "FieldContainer",
			style: "min-height : 40px;"
	}
	var container = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
		var attrArray = {
				class:"col-xs-1", 
				FiligranaName:"WatermarkMetaPropertyPlaceholder" + PropertyData.id,
				FiligranaType:"Field"	
		}
		var Field = container.appendField(attrArray.FiligranaName, "div", attrArray);
		var attrArray = {
				class:"col-xs-4 wrapfield", 
				FiligranaName:"WatermarkMetaProperty" + PropertyData.id,
				FiligranaType:"Field"	
		}
		var Field = container.appendField(attrArray.FiligranaName, "div", attrArray);
		Field.set(PropertyData.property);
		
		var attrArray = {
				class:"col-xs-5 wrapfield", 
				FiligranaName:"WatermarkMetaValue" + PropertyData.id,
				FiligranaType:"Field"	
		}
		var Field = container.appendField(attrArray.FiligranaName, "div", attrArray);
		Field.set(PropertyData.value);
		
		if(edit == 1){ 
			var attrArray = {
					class:"col-xs-1", 
					FiligranaName:"WatermarkMetaButtonDiv" + PropertyData.id,
					FiligranaType:"FieldContainer"	
			}
			var container10 = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
				attrArray10 = {
						type: 'button',
						delid: PropertyData.id,
						wmid: wmid,
						class: "clickable btn btn-lg plusButton pull-right",
						FiligranaAction: "deleteWMMetaProperty",
						page: "manage_watermark_page",
						FiligranaType: "FieldContainer",
						FiligranaName: "WatermarkPropertyDeleteButton",
						deltype : "Meta"
					};
					var container10 = container10.appendFieldContainer(attrArray10.FiligranaName, 'div', attrArray10);
						attrArray11 = {
								class: "glyphicon glyphicon-trash",
								FiligranaType: "Field",
								FiligranaName: "WatermarkProperty"+PropertyData.id+"DeleteIcon"
							};
						var container11 = container10.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
						
		}
	
}


Filigrana.prototype.displayImage = function(container, imageData, Sketch, wmid, edit){

	var attrObject = {
			class: "col-xs-10 col-sm-2",
			FiligranaType: "FieldContainer",
			FiligranaName: "Watermark"+Sketch+"Container"+imageData.id
			
	};
	var littlecontainer=container.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
	
		var attrObject = {
				FiligranaType: "FieldContainer",
				FiligranaName: "Watermark"+Sketch+"Container"+imageData.id+"anotherdiv"	
		}
		var anotherdiv=littlecontainer.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
			var attrObject = {
					class: "img-responsive limitedheight",
					src: imageData.path,
					FiligranaType: "Field",
					imgid: imageData.id,
					sketch: Sketch,
					FiligranaName: "Watermark"+Sketch+""+imageData.id+"AttrFieldImg"
					
			};
			var image=anotherdiv.appendField(attrObject.FiligranaName, "img", attrObject);
	if(edit == 1){ 
		var attrArray = {
				class:"imagetrashdiv", 
				FiligranaName:"Watermark"+Sketch+"Div" + imageData.id,
				FiligranaType:"FieldContainer"	
		}
		var container10 = anotherdiv.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
			attrArray10 = {
					type: 'button',
					delid: imageData.id,
					wmid: wmid,
					class: "clickable btn btn-lg plusButton delImageButton",
					FiligranaAction: "deleteWMImage",
					page: container.container.getSelector(),
					FiligranaType: "FieldContainer",
					FiligranaName: "Watermark"+Sketch+"DeleteButton"+imageData.id,
					deltype : Sketch
				};
				var container10 = container10.appendFieldContainer(attrArray10.FiligranaName, 'div', attrArray10);
					attrArray11 = {
							class: "glyphicon glyphicon-trash",
							FiligranaType: "Field",
							FiligranaName: "WatermarkImage"+imageData.id+"DeleteIcon"
						};
					var container11 = container10.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
					
					$('[FiligranaName="Watermark'+Sketch+imageData.id+'AttrFieldImg"]').load(function(e){ 
						var koords = {
								top: $('[FiligranaName="Watermark'+$(this).attr('sketch')+$(this).attr('imgid')+'AttrFieldImg"]').offset().top,
								left: $('[FiligranaName="Watermark'+$(this).attr('sketch')+$(this).attr('imgid')+'AttrFieldImg"]').offset().left + 
										$('[FiligranaName="Watermark'+$(this).attr('sketch')+$(this).attr('imgid')+'AttrFieldImg"]').width()
						};
						$('[FiligranaName="Watermark'+$(this).attr('sketch')+'Div' + $(this).attr('imgid')+'"]').offset(koords);
					});
	}
	
}
//NOt used anymore
Filigrana.prototype.changeToEditWmName = function(page){
	
	var editButtoncontainer = this.pages[page].getField("WatermarkNameEditButtonContainer");
		
		editButtoncontainer.remove("WatermarkNameEditButton");
		
		
		attrArray8 = {
				class: "clickable btn btn-lg plusButton",
				FiligranaType: "Field",
				FiligranaAction: "saveEditWmName",
				FiligranaName: "WatermarkNameEditButton",
				type : 'button'
			};
		var editButton = editButtoncontainer.appendFieldContainer(attrArray8.FiligranaName, 'button', attrArray8);
			attrArray11 = {
					class: "glyphicon glyphicon-floppy-open",
					FiligranaType: "Field",
					FiligranaName: "WatermarkProperty"+PropertyData.id+"DeleteIcon"
				};
			var container11 = editButton.appendFieldContainer(attrArray11.FiligranaName, 'span', attrArray11);
		
		
		
		
		$('.wmname').removeAttr("disabled");
		
		$.getApp().resetClickActions();
}
//Not used anymore
Filigrana.prototype.saveWMNameEdit = function(page){
	var post = {
				action : "editWatermark",
				wmid : this.parameters.get.wmid,
				name : this.getField("WatermarkName"),
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
				var editButtoncontainer = $.getApp().pages[page].getField("WatermarkNameEditButtonContainer");
					
					editButtoncontainer.remove("WatermarkNameEditButton");
					
					
					attrArray8 = {
							class: "clickable btn btn-lg",
							FiligranaType: "Field",
							FiligranaAction: "editWMName",
							FiligranaName: "WatermarkNameEditButton",
							type : 'button'
						};
					var editButton = editButtoncontainer.appendField(attrArray8.FiligranaName, 'button', attrArray8);
					editButton.set("edit");
					
					
					
					$('.wmname').attr("disabled", true);
					
					$.getApp().resetClickActions();
			}else{
				$.getApp().Ajax_handleError(response);
				//console.log(response);
			}
		
		
		}
	});
}


Filigrana.prototype.watermarkPropertyDelete = function(page, type, id){
	
	var r = confirm("Do you really want to delete this item ?");
		if (r == true) {
			var post = {
					action : "editWatermark",
					wmid : this.parameters.get.wmid,
					name : -1,
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
			if(type == 'Meta'){
				post.delMetadata = {
						id: id,
						value : this.getField("WatermarkMetaValue"+id)
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
							$.getApp().pages[this.page].getField("WatermarkMetaRow"+this.id).remove("WatermarkMetaRow"+this.id);
						}else{
							$.getApp().pages[this.page].getField("Watermark"+this.typed+"Container"+this.id).remove("Watermark"+this.typed+"Container"+this.id);
							$.getApp().pages[this.page].getField("Watermark"+this.typed+"Div" + this.id).remove("Watermark"+this.typed+"Div" + this.id);
							
						}
					}else{
						$.getApp().Ajax_handleError(response);
						
					}
				
				
				}
			});
		}else{
			
		}
	
	
}

Filigrana.prototype.addMetaDataWatermark = function(page){
	var container = $.getApp().pages[page].getField('watermarkMetadataContainer');
	$('#addWMMetaData').hide();
	var attrArray = {
			class: "row",
			FiligranaName : "WatermarkMetaRow"+ "new",
			FiligranaType : "FieldContainer",
			style: "min-height : 40px;"
	}
	var container = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
	
		var attrArray = {
				class:"col-xs-5", 
				FiligranaName:"WatermarkMetaProperty" + "new",
				FiligranaType:"FieldContainer"	
		}
		var container1 = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
		
			attrArray8 = {
					FiligranaName : "WatermarkMetaPropertyField" + "new",
					FiligranaType :"FieldContainer",
					type : "text", 
					class:"form-control text textInputField"
					
				};
			var inputField = container1.appendField(attrArray8.FiligranaName, 'input', attrArray8);
		
		var attrArray = {
				class:"col-xs-5", 
				FiligranaName:"WatermarkMetaValue" + "new",
				FiligranaType:"FieldContainer"	
		}
		var container1 = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
			attrArray8 = {
					FiligranaName : "WatermarkNewValueField",
					FiligranaType :"FieldContainer",
					type : "text", 
					class:"form-control text textInputField"
					
				};
			var inputField = container1.appendField(attrArray8.FiligranaName, 'input', attrArray8);
		
		
		
		var attrArray = {
				class:"col-xs-2", 
				FiligranaName:"WatermarkMetaValueButton" + "new",
				FiligranaType:"FieldContainer"	
		}
		
		
		var container10 = container.appendFieldContainer(attrArray.FiligranaName, "div", attrArray);
		attrArray10 = {
				type: 'button',
				wmid: this.parameters.get.wmid,
				class: "clickable btn btn-lg plusButton",
				FiligranaAction: "saveNewMeta",
				page: "manage_watermark_page",
				FiligranaType: "Field",
				FiligranaName: "SaveNewMetaButton",
				topage : page
			};
			var container10 = container10.appendFieldContainer(attrArray10.FiligranaName, 'div', attrArray10);
				attrArray11 = {
						class: "glyphicon glyphicon-floppy-open",
						FiligranaType: "Field",
						FiligranaName: "WatermarkSaveIcon"
					};
				var container11 = container10.appendField(attrArray11.FiligranaName, 'span', attrArray11);
			$.getApp().resetClickActions();
}

Filigrana.prototype.saveAddMetaData = function(page){
	var property = this.getField("WatermarkMetaPropertyFieldnew");
	var value = this.getField("WatermarkNewValueField");
	var pushObject = {
			key : property,
			value: value
	};

	var post = {
			action : "editWatermark",
			wmid : this.parameters.get.wmid,
			name : -1,
			newMetadata : pushObject,
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
			page : page,
			success: function(response){
				if(response.check == "suc"){
					$.getApp().switchPage("manage_watermark_page");
				}else{
					$.getApp().Ajax_handleError(response);
					//console.log(response);
				}
			
			
			}
		});
	
}



Filigrana.prototype.deleteWatermark = function(page){
	
	var r = confirm("Do you really want to delete this Watermark ?");
		if (r == true) {
			var post = {
					action : "editWatermark",
					wmid : this.parameters.get.wmid,
					name : -1,
					newMetadata : -1,
					delMetadata: -1,
					newiphtypes : -1,
					deliphtypes : -1,
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


Filigrana.prototype.appendToWm = function(sketch){
	console.log("appending: "+sketch);
	var post = {
			action : "addCurrent",
			id : this.parameters.get.wmid,
			sketch : sketch,
			type: "wm"
	};
	
	
	$.ajax({
		type: "POST",
		targetPage : "manage_watermark_page",
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


Filigrana.prototype.createWatermark = function(page){


	var post = {
			action : "createWatermark",
			
	};
	
		$.ajax({
			type: "POST",
			url: $.getApp().parameters.server,
			data: post,
			dataType: "json",
			page : page,
			success: function(response){
				if(response.check == "suc"){
					delete $.getApp().parameters.get.iphid;
					$.getApp().parameters.get.wmid= response.wmid;
					$.getApp().switchPage("manage_watermark_page");
				}else{
					$.getApp().Ajax_handleError(response);
					//console.log(response);
				}
			
			
			}
		});
	
}
