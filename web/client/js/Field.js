/**
 * 
 */

var Field = function(container, key){
	this.id = "";
	this.selector = "";
	this.FiligranaName = "";
	this.page = null;
	this.value = "";
	this.htmlObject = null;
	this.container = null;
	this.fieldcontainer = "";
	this.html = null;
	this.constructor(container, key);
};





Field.prototype.constructor = function(container,key){

	this.container = container;

	if(container.isPrototypeOf(Page)){
		this.page = page;
	}else{
		this.page = this.container.page;
		this.fieldcontainer = container;
	}
	this.id = $(container.getSelector() +' [FiligranaName="'+key+'"]').attr("id");
	if (typeof this.id !== typeof undefined && this.id !== false){
		this.selector = "#"+this.id;
	}else{
		this.selector = container.getSelector() +' [FiligranaName="'+key+'"]';
	}

	this.FiligranaName = key;
	this.value = $(this.getSelector()).val();

	if(this.value == undefined)this.value = "";
}


Field.prototype.set = function(value, html){
	
		if($(this.getSelector()).val() == value){
			this.value = value;
		}else{
			if($(this.getSelector()).is("input")){ 
				$(this.getSelector()).val(value);
				this.value=value;
			}else{
				if(html == undefined){ 
					$(this.getSelector()).text(value);
				}else{
					$(this.getSelector()).html(value);
				}
				this.value = value;
			}
		}

}

Field.prototype.setAll = function(value){
	
	$('[FiligranaName = "'+this.FiligranaName+'"]').each(function(){
		var pageid = $(this).parents('.page').attr('id');
		console.log(value);
		
		$.getApp().pages[pageid].setField($(this).attr('FiligranaName'), value);
	});
}

Field.prototype.get = function(){

		return this.value;
	
}

Field.prototype.remove = function(){
	$(this.getSelector()).remove();
	return true;
}

Field.prototype.getSelector = function(){
	return this.selector;
}




