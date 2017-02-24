

var Filigrana = function(){
	this.pages={};
	this.parameters = {};
	this.navmenu = new Navmenu();
	this.currentPage = null;
	this.emptySketch= "data:image/png;base64,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";
	this.currentSketch = this.emtpySketch;
	this.lastPages = [];
	this.currentPageNum = 0;
	this.appChangesHash = false;
	this.sketchPageisCalculated = false;
	this.currentSearch = 0;
};








Filigrana.prototype.clickAction = function(object, event){
	console.log(object);
	//console.log(object);
	var attr = $(object).attr('topage');
	var page = $(object).parents(".page").attr('id');
	if (typeof attr !== typeof undefined && attr !== false) {
		if($(object).attr('FiligranaAction') == "Login"){
			this.User_login(attr);
			return 1;
		}
		else if($(object).attr('FiligranaAction') == "register"){
			this.registerUser(attr);
			return 1;
			
		}
		else if($(object).attr('FiligranaAction') == "DeleteWatermark"){
			
			this.deleteWatermark(attr);
			return 1;
		}
		else if($(object).attr('FiligranaAction') == "DeleteIPHType"){
			this.deleteIPHType(attr);
			return 1;
		}
		else if($(object).attr('FiligranaAction') == "SearchWatermarkSketch"){
			
			this.searchWatermarkSketch(attr);
			
		}
		else if($(object).attr('FiligranaAction') == "lookupIPHType"){
	
			this.lookUpIphType(attr);
			
		}
		else if($(object).attr('FiligranaAction') == "camera"){
			$('#uploadImage').click(); 
			
		}
		else if($(object).attr('FiligranaAction') == "saveNewMeta"){
	
			this.saveAddMetaData(attr);
			this.switchPage(attr);
		}
		else if($(object).attr('FiligranaAction') == "saveNewMetaIPH"){
			
			this.saveAddMetaDataIPH(attr);
			this.switchPage(attr);
		}
		else if($(object).attr('FiligranaAction') == "goToManage"){
			if($(object).attr("wmid")!= undefined){
				delete this.parameters.get.iphid;
				this.parameters.get.wmid = $(object).attr("wmid");
			}else if($(object).attr("iphid")!= undefined){
				delete this.parameters.get.wmid;
				this.parameters.get.iphid = $(object).attr("iphid");
			}
			if($(object).attr("score")!= undefined){
				this.parameters.search = 1;
			}
			this.switchPage(attr);
		}
		
		else if($(object).attr('FiligranaAction') == "setToSearch"){
			delete this.parameters.manageType;
			delete this.parameters.addId;
			delete this.parameters.addSketch;
			
			$('#addSketchButton').hide();
			$('#finished_sketching').show();
			this.switchPage(attr);
		}
		
		else if($(object).attr('FiligranaAction') == "createWatermark"){
			this.createWatermark();
		}
		else if($(object).attr('FiligranaAction') == "createIPHType"){
			this.createIPHType();
		}
		else if(attr == "sketch_page"){
			if($(object).attr('id') == "sketch_without_picture"){
				$('#sketch_image_img').attr('src','/img/app/white.jpg');
				//this.calcSketchPage();
				this.switchPage(attr );
			}
			this.switchPage(attr );
		}
		else if($(object).attr('FiligranaAction') == "changeUsername"){
			var username = this.getField("newUsername");
			this.changeUser(username, -1,-1,-1,-1,false);
		}
		else if($(object).attr('FiligranaAction') == "changeEmail"){
			var email = this.getField("newEmail");
			var password = this.getField("changeEmailPassword");
			
			
			this.changeUser(-1, email,password,-1,-1,false);
		}
		else if($(object).attr('FiligranaAction') == "saveNewPassword"){
			
			var pwold = this.getField("changePasswordOld");
			var pwnew = this.getField("changePasswordNew");
			var pwnew2 = this.getField("changePasswordNew2");
			this.changeUser(-1, -1,pwold,pwnew,pwnew2,false);
		}
		else if($(object).attr('FiligranaAction') == "Logout"){
			this.logout();
			
			
		}
		else{
			this.switchPage(attr, false);
		}
		
	
	}
	else if($(object).attr('FiligranaAction') == "SketchWithImage"){
		$('#background_between').css('z-index', '-20');
	}
	else if($(object).attr('FiligranaAction') == "SketchWithoutImage"){
		this.navmenu.setImageUrls("/img/app/white.jpg", null);
		
		$('#background_between').css('z-index', '5');
	}
	else if($(object).attr('FiligranaAction') == "SketchDraw"){
		Processing.getInstanceById('draw').setToDraw();
	}
	else if($(object).attr('FiligranaAction') == "SketchErase"){
		Processing.getInstanceById('draw').setAsEreaser();
	}
	else if($(object).attr('FiligranaAction') == "show_sidenav"){
		this.navmenu.show();
	}else if($(object).attr('FiligranaAction') == "hide_sidenav"){
		this.navmenu.hide();
	}
	else if($(object).attr('FiligranaAction') == "renameResearchGroup"){
	
		var resid = $(object).attr("resId");
		this.changeToEditUserGroup(resid, page);
		
	}
	else if($(object).attr('FiligranaAction') == "saveEditResearchGroup"){

		var resid = $(object).attr("resId");
		this.saveEditResearchGroup(resid);
		
	}
	else if($(object).attr('FiligranaAction') == "addResearchGroup"){

	
		this.addResearchGroup(page);
		
	}
	else if($(object).attr('FiligranaAction') == "addPersonToGroup"){
	
		var regid = $(object).attr('reg_id');
		this.fadeInNewUser(regid,page);
		
	}
	else if($(object).attr('FiligranaAction') == "submitAddUser"){
		var regid=$(object).attr('regid');
			
		this.submitAddUser(regid);
		
	}
	else if($(object).attr('FiligranaAction') == "deleteResearchGroup"){

	
		var reg_id =$(object).attr('res_id');
		
		this.deleteResearchGroup(page, reg_id);
		
	}
	else if($(object).attr('FiligranaAction') == "removeUserFromResearchGroup"){
	
	
		var reg_id =$(object).attr('resID');
		var usr_id = $(object).attr('userID');
		
		this.removeUserFromResearchGroup(page, reg_id, usr_id);
		
	}
	else if($(object).attr('FiligranaAction') == "changeUserRights"){
		
	
		var reg_id =$(object).attr('reg_id');
		var usr_id = $(object).attr('user_id');
		var uro_id = $(object).find(':selected').val();
		
		this.changeUserRights(reg_id, usr_id, uro_id);
		
	}
	
	else if($(object).attr('FiligranaAction') == "editWMName"){

		
		
		this.changeToEditWmName(page);
		
	}
	else if($(object).attr('FiligranaAction') == "saveEditWmName"){

		var page = $(object).parents(".page").attr('id');
		
		this.saveWMNameEdit(page);
		
	}
	else if($(object).attr('FiligranaAction') == "deleteWMMetaProperty" || $(object).attr('FiligranaAction') == "deleteWMImage"){
	
		var page = $(object).parents(".page").attr('id');
		var type = $(object).attr("deltype");
		var id = $(object).attr("delid");
		this.watermarkPropertyDelete(page, type, id);
		
	}else if($(object).attr('FiligranaAction') == "watermarkAddMetadata"){
		event.stopImmediatePropagation();
		event.stopPropagation();
		event.preventDefault();
		var page = $(object).parents(".page").attr('id');
		
		this.addMetaDataWatermark(page);
		
	}
	else if($(object).attr('FiligranaAction') == "editIPHName"){

		
		
		this.changeToEditIPHName(page);
		
	}
	else if($(object).attr('FiligranaAction') == "saveEditIPHName"){

		var page = $(object).parents(".page").attr('id');
		
		this.saveIPHNameEdit(page);
		
	}
	else if($(object).attr('FiligranaAction') == "deleteIPHMetaProperty" || $(object).attr('FiligranaAction') == "deleteIPHImage"){
	
		var page = $(object).parents(".page").attr('id');
		var type = $(object).attr("deltype");
		var id = $(object).attr("delid");
		this.IPHPropertyDelete(page, type, id);
		
	}else if($(object).attr('FiligranaAction') == "IPHAddMetadata"){
		console.log("i am here");
		var page = $(object).parents(".page").attr('id');
		
		this.addMetaDataIPH(page);
		
	}

	else if($(object).attr('FiligranaAction') == "back"){
		this.back();
	}
	else if($(object).attr('FiligranaAction') == "forward"){
		this.forward();
	}
	else if($(object).attr('FiligranaAction') == "repeatSearchWM"){
		var wmid = $(object).attr('wmid');
		var picid = $(object).attr('picid');
		console.log(wmid);
		console.log(picid);
		this.getSimilarWatermarks("choose_watermark_page", wmid, picid);
	}
	else if($(object).attr('FiligranaAction') == "repeatSearchIPH"){
		var iphid = $(object).attr('iphid');
		var picid = $(object).attr('picid');
		this.lookUpSimilarIPH("choose_iph_type_page", iphid, picid);
	}else if($(object).attr('FiligranaAction') == "SketchClear"){
		this.sketchPageisCalculated = false;
		this.calcSketchPage();
	}
	
	else if($(object).attr('FiligranaAction') == "watermarkAppendImage"){

		this.appendToWm(false);
	}
	else if($(object).attr('FiligranaAction') == "watermarkAppendSketch"){
			
		this.appendToWm(true);
	}
	else if($(object).attr('FiligranaAction') == "IPHAppendImage"){
		
		this.appendToIPH(false);
	}
	else if($(object).attr('FiligranaAction') == "IPHAppendSketch"){
		
		this.appendToIPH(true);
	}
	else if($(object).attr('FiligranaAction') == "deleteWMMetaProperty"){
		console.log(object);
		var delid = $(object).attr('delid');
		console.log(delid);
		this.deleteWMMetaProperty(delid);
	}
	else if($(object).attr('FiligranaAction') == "deleteIPHMetaProperty"){
		
		this.deleteIPHMetaProperty();
	}
	
	else if($(object).attr('FiligranaAction') == "watermarkAddImage"){
	
		this.parameters.manageType = "wm";
		this.parameters.addId = this.parameters.get.wmid;
		$('#uploadImageManage').click(); 
	}
	
	else if($(object).attr('FiligranaAction') == "IPHAddImage"){
		this.parameters.manageType = "iph";
		this.parameters.addId = this.parameters.get.iphid;
		$('#uploadImageManage').click(); 
	}
	
	else if($(object).attr('FiligranaAction') == "watermarkAddSketch"){
		this.parameters.manageType = "wm";
		this.parameters.addId = this.parameters.get.wmid;
		this.parameters.addSketch = true;

		$('#addSketchButton').show();
		$('#finished_sketching').hide();
		this.resetClickActions();
		this.switchPage("menu_choose_picture_source_page");
	}
	else if($(object).attr('FiligranaAction') == "IPHAddSketch"){
		this.parameters.manageType = "iph";
		this.parameters.addId = this.parameters.get.iphid;
		this.parameters.addSketch = true;
		
		$('#addSketchButton').show();
		$('#finished_sketching').hide();
		this.resetClickActions();
		this.switchPage("menu_choose_picture_source_page");
	}
	else if($(object).attr('FiligranaAction') == "addSketch"){
		this.addSketch();
		
		
	}
	
	
	
	
	
	
	
	
	
	// For some browsers, `attr` is undefined; for others,
	// `attr` is false.  Check for both.
		
};

Filigrana.prototype.addSketch = function(){
	
	
	
	
	//TODO upload sketch, get path

	var post = {
			action : "addSketch",
			file: $('#draw')[0].toDataURL(),
			type: this.parameters.manageType,
			id : this.parameters.addId
	};
	
	
		$(".page").hide();
		$("#loader_page").show();
		$.ajax({
			type: "POST",
			url: $.getApp().parameters.server,
			data: post,
			dataType: "json",
			success: function(response){
				if(response.check == "suc"){
					
					
					
					$('#addSketchButton').hide();
					$('#finished_sketching').show();
					if($.getApp().parameters.manageType == "wm"){
						$.getApp().switchPage('manage_watermark_page');
					}else{
						$.getApp().switchPage('manage_iph_type_page');
					}
					delete $.getApp().parameters.manageType;
					delete $.getApp().parameters.addId;
					delete $.getApp().parameters.addSketch;
				}else{
					$.getApp().Ajax_handleError(response);
					delete $.getApp().currentWMSearchResult;
				}
				
				
			}
		});
	
}

Filigrana.prototype.forward = function(){
	if(this.currentPageNum != this.lastPages.length-1){ 
		this.currentPageNum++;
		console.log(this.lastPages);
		console.log(this.currentPageNum);
		this.switchPage(this.lastPages[this.currentPageNum].page, false, true);
	}
	
	
}

Filigrana.prototype.back = function(){

	if(this.currentPageNum != 0){
		this.currentPageNum--;
		this.switchPage(this.lastPages[this.currentPageNum].page, false, true);
	}
	
	
}




Filigrana.prototype.switchPage= function (page, calculated, histmanipulation){
		this.navmenu.hideSketchPage();
		this.navmenu.hideIPHButtons();
		this.navmenu.hideWMButtons();
		document.title = "Filigrana WebApp "+page;
		if(page.indexOf("#") >-1){ 
			var splitarray = page.split('&');
			page = splitarray[0].substring(1,splitarray[0].length);
		}
		if(page == "choose_watermark_page" || page == "choose_iph_type_page" || (this.fromSearch && (page == "manage_watermark_page" || page == "manage_iph_type_page"))){
			this.fromSearch = true;
		}else{
			this.fromSearch = false;
		}
			
		var getString = "";
		if(page == "manage_watermark_page" || page == "manage_iph_type_page" || page == "register_page"){ 
			for(var i in this.parameters.get){
				if(i != "page"){ 
					getString +="&"+i+"="+this.parameters.get[i];
				}
			}
		}

		var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
		
			var URLArray = document.URL.split('/');
			
			var URL = URLArray[0]+"//"+URLArray[2]+"/"+getString+"#"+page;
			
		/*
			if(this.currentPage != null){
				if(!isSafari){ 	
					history.replaceState(this.currentPage.page, this.currentPage.title, this.currentPage.URL);
					
					history.pushState(page, document.title, URL);
				}
			}else{
				if(!isSafari){ 
					history.replaceState(page, document.title, URL);
				}		
			}
		*/
		this.currentPage = {
				page : page,
				title: page + "",
				URL : URL
		};
		
		if(this.currentPageNum != this.lastPages.lenght -1 && this.lastPages.length != 0 && histmanipulation == undefined){
			var tmp = [];
			for(var i = 0; i <= this.currentPageNum; i++){
				tmp.push(this.lastPages[i]);
			}
			this.lastPages = tmp;
		}
		
		if(histmanipulation === undefined){
			
			this.lastPages.push(this.currentPage);
			this.currentPageNum = this.lastPages.length-1;
			
		}
		this.appChangesHash = true;
		window.location.hash = page+getString;
		
		if(this.currentPageNum == this.lastPages.length -1){
			$('#nav_forward_button').attr("disabled", true);
		}else{
			$('#nav_forward_button').removeAttr("disabled");
		}
		
		if(this.currentPageNum == 0){
			$('#nav_back_button').attr("disabled", true);
		}else{
			$('#nav_back_button').removeAttr("disabled");
		}
		
		
		if(page == "sketch_page"){
			//this.calcSketchPage();
			if(!this.sketchPageisCalculated){ 
				mem = setInterval(function () {
			        var sketch = Processing.getInstanceById("draw");
			        if (sketch) {
			     
			            $.getApp().calcSketchPage();
			            
			            $.getApp().emptySketch = $('#draw')[0].toDataURL();
			            
			            clearInterval(mem);
			        } else {
			            timer += 10;
			            if (timer > timeout) {
			            
			                clearInterval(mem);
			            }
			        }
			    }, 10);
			}
			this.navmenu.showSketchPage();
			
		}
		if(page == "choose_picture_page"){
			this.calcChoosePicturePage();
		}
		
		if(page == "manage_user_page"){
			if(!calculated)this.getResearchGroups(page);
		}
		
		if(page == "choose_watermark_page"){
			if(!calculated)this.searchWatermarkSketch(page);
		}
		if(page == "choose_iph_type_page"){
			if(!calculated)this.lookUpIphType(page);
		}
		if(page == "manage_watermark_page"){
			wmid = this.parameters.get.wmid
			if(!calculated)this.getWatermarkDetail(page, wmid);
		}
		if(page == "manage_iph_type_page"){
			iphid = this.parameters.get.iphid
			if(!calculated)this.getIPHDetail(page, iphid);
		}
		if(page == "search_page"){
			if(!calculated)this.manageTextSearch(page, this.pages[page].getField("Search"));
		}
		if(page == "register_page"){
			
			if(!calculated){
				
				var hash = "";
				if(this.parameters.get.hash != undefined){
					hash = this.parameters.get.hash;
				}
				this.displayRegisterUser(page,hash);
			}
		}
		
		if(page == "set_password_page"){
			
			if(!calculated){
				
				var hash = "";
				if(this.parameters.get.hash != undefined){
					hash = this.parameters.get.hash;
				}
				this.displaySetPassword(page,hash);
			}
		}
		 
		    
	    if(page == "login_page" || page == "register_page"){
	    	this.navmenu.disable();
	    }
	    else if(page == "choose_watermark_page" || page == "choose_iph_type_page" || (this.fromSearch == true && (page == "manage_watermark_page" || page == "manage_iph_type_page"))){
	    	if(page == "choose_watermark_page" || page == "choose_iph_type_page"){
	    		this.navmenu.showImages(true);
	    	}else{ 
	    		this.navmenu.showImages(false);
	    	}
	    	this.navmenu.enable();
	    }else{
	    	this.navmenu.hideImages();
	    	this.navmenu.enable();
	    }
	    
	    
	
	    $('.page').hide();
	    if(page.indexOf("#") >-1){ 
	    	$(page).show();
	    }else{
	    	$('#'+page).show();
	    }
	
	   
	
}

Filigrana.prototype.initialiseModules = function(){
	this.initialiseUserModule();
	this.initialiseWatermarkModule();
}


Filigrana.prototype.instantiate = function(params){
	this.parameters = params;
	this.initialiseModules();
	if(typeof(jQuery) == "function"){
		this.resetClickActions();
		
		$('.page').each(function(){
			$.getApp().addPage("confirm_page");
			$.getApp().addPage($(this).attr('id'));
		});
		
		
		
		$('.img_form').ajaxForm(function() { 
			console.log("choose image folder closed");
            $.getApp().switchPage("choose_picture_page", false);
        });
		
		$('#uploadImage').on('change', function(){
			$(".page").hide();
			$("#loader_page").show();
			$("#uploadImageForm").ajaxForm({
				url : $.getApp().parameters.server,
				dataType: "json",
				success: function(response) {
					if(response.check == "suc" ){
						$('#choose_image_img').attr('src',response.path);
						$('#sketch_image_img').attr('src',response.path);
						

						$.getApp().switchPage("choose_picture_page");
					}else{
						$.getApp().handleError(response);
					}
			    }
			}).submit();
			console.log("form closed");
		});
		
		$('#uploadImageManage').on('change', function(){
			$(".page").hide();
			$("#loader_page").show();
			var type = $.getApp().parameters.manageType;
			var id = $.getApp().parameters.addId;
			$('#typeField').val(type);
			$('#idField').val(id);
			
			$("#uploadImageFormMan").ajaxForm({
				url : $.getApp().parameters.server,
				dataType: "json",
				success: function(response) {
					if(response.check == "suc" ){
						
						if($.getApp().parameters.manageType == "wm"){ 	
								$.getApp().switchPage("manage_watermark_page");
							}
						else if($.getApp().parameters.manageType == "iph"){
							$.getApp().switchPage("manage_iph_type_page");
						}
						delete $.getApp().parameters.manageType;
						delete $.getApp().parameters.addId;
					}else{
						$.getApp().handleError(response);
					}
			    }
			}).submit();
			console.log("form closed");
		});
		
		
		/*
		window.removeEventListener('popstate', this.popstateHandler);
		
		window.addEventListener('popstate', this.popstateHandler);
		*/
		window.onhashchange = this.popstateHandler;
		this.parseHash();
		//for the choose watermark page
		//this.calcChoosePicturePage();
	
		var loaderwith = 180;
		var loaderheight = 220;
		var height = window.innerHeight - $('#side_nav').height();
		if(height< window.innerWidth){
			$(".loaderimage").height(height);
			$(".loaderimage").width(height*loaderheight/loaderwith);
		}else{
			$(".loaderimage").width(window.innerWidth);
			$(".loaderimage").height(window.innerWidth*loaderwith/loaderheight);
		}
		 

		
		
		// Trigger the event (useful on page load).
		

		
	}
	
	if(parameters.session != undefined){
		$.getApp().pages['manage_user_page'].getFieldNotValue("Username").setAll(parameters.session.username);
		$.getApp().pages['manage_user_page'].getFieldNotValue("loginEmail").setAll(parameters.session.email);
		if(parameters.session.createWatermark == 0){
			$('#add_watermark').hide();
		}
		if(parameters.session.createIPHType == 0){
			$('#add_iph_type').hide();
		}

	}
		
	
};

Filigrana.prototype.popstateHandler = function(event) {
		if($.getApp().appChangesHash){
			$.getApp().appChangesHash = false;
		}else{ 
			console.log("hashchanged");
			if(this.WebApp.currentPageNum == undefined){
				return;
			}
			
			if(this.WebApp.lastPages == undefined){
				return;
			}
			if(this.WebApp.lastPages.length > this.WebApp.currentPageNum +1){
				console.log("bin in if vor forward");
				if('#'+this.WebApp.lastPages[this.WebApp.currentPageNum+1].page == window.location.hash){
					console.log("bin in forward");
					event.stopImmediatePropagation();
					event.preventDefault();
					this.WebApp.forward();
					return;
				}
			}
			if(0< this.WebApp.currentPageNum){
	
				console.log("'#'"+this.WebApp.lastPages[this.WebApp.currentPageNum-1].page+" == "+window.location.hash);
				if('#'+this.WebApp.lastPages[this.WebApp.currentPageNum-1].page == window.location.hash){
					
					event.stopImmediatePropagation();
					event.preventDefault();
					this.WebApp.back();
					return
				}
			}	
				
			
			
			var maxdistance = 0;
			if(this.WebApp.lastPages.length - this.WebApp.currentPageNum > this.WebApp.currentPageNum){
				maxdinstance = this.WebApp.lastPages.length - this.WebApp.currentPageNum;
			}else{
				maxdinstance = this.WebApp.currentPageNum;
			}
			for(var i = 0; i< maxdistance; i++){
				if('#'+this.WebApp.lastPages[this.WebApp.currentPageNum + i].page == window.location.hash){
					this.WebApp.currentPageNum = this.WebApp.currentPageNum + i;
					this.WebApp.switchPage(this.WebApp.lastPages[this.WebApp.currentPageNum], false, true);
				}else if('#'+this.WebApp.lastPages[this.WebApp.currentPageNum - i].page == window.location.hash){
					this.WebApp.currentPageNum = this.WebApp.currentPageNum - i;
					this.WebApp.switchPage(this.WebApp.lastPages[this.WebApp.currentPageNum], false, true);
				}
				else{
					if(this.WebApp.lastPages[this.WebApp.currentPageNum - i] == undefined && this.WebApp.lastPages[this.WebApp.currentPageNum + i] == undefined){
							return;
						}
					}
				}
		}	
		
};

Filigrana.prototype.parseHash = function(){
	
	var splitarray = window.location.hash.split('&');
	
	for(var i = 1; i< splitarray.length; i++){
		var splitarray2 = splitarray[i].split("=");
		
		this.parameters.get[splitarray2[0]] = splitarray2[1];
	}
}

Filigrana.prototype.calcSketchPage= function(){

	if(this.sketchPageisCalculated){
		return;
	}
	console.error(this.sketchPageisCalculated);
	$('body').append('<img id="realImage" src="'+document.getElementById('sketch_image_img').src+'" hidden="true"/>');
	
	var toolbarheight = $('#sketch-header').height() + $('#sketch_footer').height() + $('#side_nav').height() + 20;
	console.log(window.innerHeight)
	console.log(toolbarheight+" = "+$('#sketch-header').height()+" + "+$('#sketch_footer').height()+" + "+$('#side_nav').height());
	var screenratio = (window.innerHeight-toolbarheight)/window.innerWidth;
	var imgratio = $('#realImage').height()/$('#realImage').width();
	if(screenratio <= 1 && imgratio <= 1){
		
		if(screenratio < imgratio){

			$('#sketch_image_img').height(window.innerHeight-toolbarheight);
			$('#sketch_image_img').width($('#sketch_image_img').height()/imgratio);
		}else{
			$('#sketch_image_img').width(window.innerWidth);
			$('#sketch_image_img').width($('#sketch_image_img').width()*imgratio);
		}
	}
	else if(screenratio <= 1 && imgratio > 1){
		$('#sketch_image_img').height(window.innerHeight-toolbarheight);
		$('#sketch_image_img').width($('#sketch_image_img').height()/imgratio);
	}
	else if(screenratio > 1 && imgratio <= 1){
		$('#sketch_image_img').width(window.innerWidth);
		$('#sketch_image_img').height($('#sketch_image_img').width()*imgratio);
	}
	else{
		if(screenratio > imgratio){
			$('#sketch_image_img').height(window.innerHeight-toolbarheight);
			$('#sketch_image_img').width($('#sketch_image_img').height()/imgratio);
		}else{
			$('#sketch_image_img').width(window.innerWidth);
			$('#sketch_image_img').height($('#sketch_image_img').width()*imgratio);
		}
	}
	
	if($('body').height() > window.innerHeight){
		var difference = $('body').height()-window.innerHeight;
		$('#sketch_image_img').height($('#sketch_image_img').height()-difference);
		$('#sketch_image_img').width($('#sketch_image_img').width()-(difference*imgratio));
	}
	console.log($('#sketch_image_img').height());

	if(Processing.getInstanceById("draw") != undefined){
		Processing.getInstanceById("draw").setup($('#sketch_image_img').width(), $('#sketch_image_img').height());
		document.getElementById("draw").offsetLeft = document.getElementById("sketch_image_img").offsetLeft;//(($('#background_between').width()-$('#sketch_image_img').width())/2) +'px';
		$('#draw').offset($('#sketch_image_img').offset());
		
		
	}
	$('#background_between').offset($('#drawarea').offset());
	$('#background_between').css('width',$('#drawarea').width()+'px');
	$('#background_between').css('height',$('#drawarea').height()+'px');
	

	$('#realImage').remove();
	this.sketchPageisCalculated = true;
}

Filigrana.prototype.calcChoosePicturePage = function(){
	var toolbarheight = $('#choose_picture_page_header').height();
	
	if(window.innerWidth > window.innerHeight-toolbarheight){
		var imgheight = window.innerHeight-toolbarheight;
		$('#choose_image_img').height(imgheight);
	}else{
		$('#choose_image_img').width(window.innerWidth);
	}
	
	
}

Filigrana.prototype.addPage = function(id){
	if(!this.pages.hasOwnProperty(id)){
		this.pages[id] = new Page(this,id);
		//console.log(this.pages[id]);
	}
}

Filigrana.prototype.insertAction = function(object, event){
	
	var currentvalue = $(object).val();
	$('[FiligranaName = "'+$(object).attr('FiligranaName')+'"]').each(function(){
		
		var pageid = $(this).parents('.page').attr('id');
	
		$.getApp().pages[pageid].setField($(this).attr('FiligranaName'), currentvalue);
	});
	
	if($(object).attr("FiligranaAction") == "Search"){
		console.log("searching");
		var pageid = $(object).parents('.page').attr('id');

		this.manageTextSearch(pageid, currentvalue);
	}
	
	
	
}

Filigrana.prototype.resetClickActions = function(){
	//route click actions to my clickAction function
	$('.clickable').unbind('click');
	$('.clickable').on('click', function(e){
		//this.clickAction($(this),e);
		//e.stopImmediatePropagation();
		e.preventDefault();
	
		$.getApp().clickAction(this, e);

		
	});
	
	//route input Actions to my function
	$('.textInputField').unbind('keyup');
	$('.textInputField').on('keyup', function(e){
		//this.clickAction($(this),e);
		//e.stopImmediatePropagation();
		
		$.getApp().insertAction(this, e);
		
	});
	
	//disable autohide on collapse panels
	$('.collapse').on('shown.bs.collapse', function () {
		  $('.panel-collapse').unbind('click');
		});
	
	//route submitAction of Modal to clickAction
	$('#submitAddUser').unbind('click');
	$('#submitAddUser').on('click', function(e){
		$.getApp().clickAction(this, e);
	});	
	
	//call this function if Modal is shown, so that the upper clickAction is set
	$('#newUserModal_page').unbind('shown.bs.modal');
	$('#newUserModal_page').on('shown.bs.modal', function () {
			$.getApp().resetClickActions();
		});
	
	//call Event if Userrole is changed
	$('.select-Userrole').unbind('change');
	$('.select-Userrole').change(function(e){
		$.getApp().clickAction(this, e);
	});
	
	//unbind autohide for navmenu dropdown
	$(document).unbind('click');
	$('.dropdown').on('click', function(e){
		e.preventDefault();
		$('.dropdown-toggle').dropdown('toggle');
	});
	
	$('.collapseA').unbind('click');
	$('.collapseA').on('click', function(e){
		e.preventDefault();
		var targetId = $(this).attr('target');
		$('#'+targetId).collapse('toggle');
	});
	
	
	
	
	//bind slider Events
	$('#penSize_slider').slider({
		min : 1,
		max : 20,
		step : 1,
		value : 10,
		tooltip: 'hide'
	}).on('slide', function(ev) {
		Processing.getInstanceById('draw').setStrokeWidth(ev.value);

		var canvas = $('#draw')[0];
		//console.log(canvas.toDataURL());
	});
	document.getElementsByClassName('slider')[0].style.cssText = "width: 100%;";
	
	
	$(function(){
		$('#opacity_slider').slider({
			min : 1,
			max : 256,
			step : 25,
			value : 255,
			tooltip: 'hide'
		}).on('slide', function(ev) {
			Processing.getInstanceById('draw').setOpacity(ev.value);
	
			var canvas = $('#draw')[0];
			//console.log(canvas.toDataURL());
		});
		document.getElementsByClassName('slider')[1].style.cssText = "width: 100%;";
	});
	
		
		
}


Filigrana.prototype.getField = function(FiligranaName){
	for(var i in this.pages){
		var res = this.pages[i].getField(FiligranaName);
		if(res !== false){
			return res;
		}
	}
	return false;
}

Filigrana.prototype.Ajax_handleError= function(response){
	switch(response.check){
		case "wpa":
				alert("wrong password");
			break;
		case "alu":
				alert("this Email is already used");
			break;
		case "nri":
				alert("you don't have the rights to do this");
			break;
			
		case "nhf":
				alert("the link is invalid");
			break;
			
		case "alv":
				alert("this link has already been validated")
			break;
				
		case "ivp":
				alert("invalid password");
			break;
		
		case "pns":
				alert("passwords are not the same");
			break;
			
		case "enf":
				alert("email not in database");
			break;
			
		case "wpa":
				alert("wrong password");
			break;
			
		case "nlg":
				alert("you are not logged in.");
				this.switchPage("login_page");
			break;
			
		case "noi":
				alert("the file you uploaded has an unknown image filetype.");
			break;
		case "uig":
			alert("A user with this email is already in a researchgroup. A user can not be in more than one researchgroup.");
		break;
		
		case "del":
			alert("Your account is deactivated");
		break;
		
		default:
				alert("some error occurred");
			break;
	}
}






