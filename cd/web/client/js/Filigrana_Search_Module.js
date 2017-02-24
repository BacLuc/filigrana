/**
 * 
 */

Filigrana.prototype.manageTextSearch = function(targetPage, query){
	var currentsearchid = ++this.currentSearch;
	$('#textSearchContainer').hide();
	$('#loaderrow').show();
	var post = {
				action: "ManageTextSearch",
				query : query
	};
	console.log(currentsearchid);
	$.ajax({
		reqnr :currentsearchid,
		type: "POST",
		url: $.getApp().parameters.server,
		data: post,
		targetPage : targetPage,
		dataType: "json",
		success: function(response){
			if(response.check == "suc"){
	
				var Container = $.getApp().pages[this.targetPage].getField("searchContainer");
				
		
	
				if($.getApp().currentSearch == this.reqnr){
					$(Container.getSelector()).empty();
					for(var i in response.result){
						
						var attrObject = {
								class: "clickable searchresultrow row",
								topage: "manage_watermark_page",
								FiligranaAction: "goToManage",
								wmid: response.result[i].id,
								FiligranaType: "FieldContainer",
								FiligranaName: "SearchContainer"+response.result[i].id+response.result[i].type,
								id : "SearchContainer"+i+response.result[i].type
						};
						var request = {
								action : "getWatermark",
								wmid : response.result[i].id,
								key : response.result[i].key
							}
						if(response.result[i].type == "iph"){
							attrObject.topage= "manage_iph_type_page";
							delete attrObject.wmid;
							attrObject.iphid= response.result[i].id;
							request = {
									action : "getIPHType",
									iphid : response.result[i].id,
									key : response.result[i].key
								}
						}
						var FieldContainer =Container.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
						
						$.getApp().getSearchRow(FieldContainer, targetPage, request, response.result[i]);
						
						
						//$.getApp().switchPage(targetPage);
						$.getApp().resetClickActions();
						
						
							
					}
				}
				if(response.result.length == 0){
					$('#loaderrow').hide();
					$('#textSearchContainer').show();
				}
				
				
				
			}else{
				$.getApp().Ajax_handleError(response);
			}
			
			
		}
	});
}


Filigrana.prototype.getSearchRow = function(FieldContainer, targetPage, request , item){

	$.ajax({
		rowid : item.id,
		rowtype : item.type,
		targetPage : targetPage,
		type: "POST",
		url: $.getApp().parameters.server,
		data: request,
		container : FieldContainer,
		dataType: "json",
		success: function(response){
			if(response.check == "suc"){
				
				//image
				
				var attrObject = {
						class: "col-xs-2",
						FiligranaType: "FieldContainer",
						FiligranaName: "SearchContainer"+this.rowid+"AttrContainerImg"
						
				};
				var littlecontainer=this.container.appendFieldContainer("SearchContainer"+this.rowid+"AttrContainerImg", "div", attrObject);
				
				
				if(response.result.img.length >0){
					var attrObject = {
							class: "img-responsive limitedheight",
							src: response.result.img[0].path,
							FiligranaType: "Field",
							FiligranaName: "SearchContainer"+this.rowid+"AttrFieldImg"
							
					};
				}else{
					var attrObject = {
							class: "img-responsive limitedheight",
							src: "img/app/white.jpg",
							FiligranaType: "Field",
							FiligranaName: "SearchContainer"+this.rowid+"AttrFieldImg"
							
					};
				}
				littlecontainer.appendField("SearchContainer"+this.rowid+"AttrFieldImg", "img", attrObject);
				
				
				//attr / value
				var attrObject = {
						class: "col-xs-10",
						FiligranaType: "FieldContainer",
						FiligranaName: "SearchContainer"+this.rowid+"AttrContainerScore"
						
				};
				var scoreContainer=this.container.appendFieldContainer("SearchContainer"+this.rowid+"AttrContainerScore", "div", attrObject);
				
				var attrObject = {
						class: "row",
						FiligranaType: "FieldContainer",
						FiligranaName: "SearchContainer"+this.rowid+"MetacontainerType"
				}
				var container = scoreContainer.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
					var attrObject = {
							class:"col-xs-12 wrapfield",
							FiligranaType: "FieldContainer",
							FiligranaName: "SearchContainer"+this.rowid+"Metacontainertype"
					}
					var Field = container.appendField(attrObject.FiligranaName, "div", attrObject);
					if(this.rowtype == "wm"){ 
						Field.set("Watermark");
					}else{
						Field.set("IPH Type");
					}
					
				for(var i in response.result.metadata){
					var attrObject = {
							class: "row",
							FiligranaType: "FieldContainer",
							FiligranaName: "SearchContainer"+this.rowid+"Metacontainer"+i
					}
					var container = scoreContainer.appendFieldContainer(attrObject.FiligranaName, "div", attrObject);
						var attrObject = {
								class:"col-xs-6 wrapfield",
								FiligranaType: "FieldContainer",
								FiligranaName: "SearchContainer"+this.rowid+"Metacontainer"+i+"property"
						}
						var Field = container.appendField(attrObject.FiligranaName, "div", attrObject);
						Field.set(response.result.metadata[i].property);
						
						var attrObject = {
								class:"col-xs-6 wrapfield",
								FiligranaType: "FieldContainer",
								FiligranaName: "SearchContainer"+this.rowid+"Metacontainer"+i+"value"
						}
						var Field = container.appendField(attrObject.FiligranaName, "div", attrObject);
						Field.set(response.result.metadata[i].value);
				}
			
				
				
				
			
			}else{
				this.Ajax_handleError(response);
			}
			$('#loaderrow').hide();
			$('#textSearchContainer').show();
			
		}
	});
}