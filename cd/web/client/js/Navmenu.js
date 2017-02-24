/**
 * 
 */
var Navmenu = function(){
	this.enabled = null;
	this.visible = false;
	this.withPictures = false;
	this.imageUrl = null;
	this.sketchUrl = null;
	
	$('.navmenu').offcanvas({ canvas: 'body', disableScrolling: false, autohide: false, placement: 'left', toggle: false });
	
};

Navmenu.prototype.enable = function(){
	if(!this.enabled || this.enabled == null){
		$('#side_nav').show();
		$('#side_nav_real').removeAttr('display');
		this.enabled = true;
	}
}

Navmenu.prototype.disable = function(){
	
	if(this.enabled || this.enabled == null){
		$('#side_nav').hide();
		//$('#side_nav_real').hide();
		
		this.enabled = false;
	}
}






Navmenu.prototype.show = function(){
	$('#side_nav').attr('FiligranaAction', 'hide_sidenav');
	$('#side_nav_real').offcanvas('show');
	//$.getApp().calcSketchPage();
	this.visible = true;
	
	if(Processing.getInstanceById('draw'))Processing.getInstanceById('draw').setMenuShown();
	
	//$('#side_nav').hide();
}

Navmenu.prototype.hide = function(){
	$('#side_nav').attr('FiligranaAction', 'show_sidenav');
	$('#side_nav_real').offcanvas('hide');
	this.visible = false;
	if(Processing.getInstanceById('draw'))Processing.getInstanceById('draw').setMenuHidden();
	//$('#side_nav').show();
}

Navmenu.prototype.showImages = function (hidecurrent){
	
	//this.setImageUrls("/img/test/testpictures/testwm.jpg","/img/test/testpictures/testsketch.jpg");
	//TODO remove this line and replace by real shit
	$('.imageli_navmenu').show();
	if($.getApp().parameters.get.iphid != undefined){
		$('.watermark.admin.owner').hide();
	}else if($.getApp().parameters.get.wmid != undefined){
		$('.iph.admin.owner').hide();
		
	}
	if(hidecurrent){
		$(".currentButtons").hide();
	}
}

Navmenu.prototype.hideImages = function (){
	$('.imageli_navmenu').hide();
	
	
}

Navmenu.prototype.setImageUrls = function(imageUrl, sketchUrl){
	console.log("changing image urls to "+imageUrl+" "+sketchUrl);
	if(imageUrl != null){
		this.imageUrl = imageUrl;
		$('#navimage_picture').attr('src', this.imageUrl);
	}
	
	if(sketchUrl != null){ 
		this.sketchUrl = sketchUrl;
		
		$('#navimage_sketch').attr('src', this.sketchUrl);
	}
}

Navmenu.prototype.hideWMButtons = function(){
	$('.watermark').hide();
}

Navmenu.prototype.showWMButtons = function(type){
	console.log($.getApp().fromSearch);
	if(type == "searcher"){
		
	}else if(type == "editor"){
		$('.watermark.editor').show();
	}else if(type == "owner"){
		$('.watermark.editor, .watermark.owner').show();
	}else if(type == "admin"){
		$('.watermark').show();
	}
	if(!$.getApp().fromSearch || $.getApp().fromSearch == undefined){
		this.hideImages();
	}
}

Navmenu.prototype.hideSketchPage = function(){
	$('.sketch_page').hide();
}
Navmenu.prototype.showSketchPage = function(){
	$('.sketch_page').show();
}

Navmenu.prototype.hideIPHButtons = function(){
	$('.iph').hide();
}
Navmenu.prototype.showIPHButtons = function(type){
	if(type == "searcher"){
		
	}else if(type == "editor"){
		$('.iph.editor').show();
	}else if(type == "owner"){
		$('.iph.editor, .iph.owner').show();
	}else if(type == "admin"){
		$('.iph').show();
	}
	
	if(!$.getApp().fromSearch){
		this.hideImages();
	}
	
}