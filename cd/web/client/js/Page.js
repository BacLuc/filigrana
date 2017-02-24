/**
 * 
 */


var Page = function(app, id){
	this.fields = {};
	this.fieldcontainers = {};
	this.id = "";
	this.app = null;
	this.constructor(app,id);
};


Page.prototype.constructor = function(app,id){

	this.id=id;
	this.app = app;
	switch(id){
			
		default:
			var children = $('#'+id).find('[FiligranaName]');
			var thispage = this;
		
			children.each(function(){
			
				var attr = $(this).attr('FiligranaType');
				if (typeof attr !== typeof undefined && attr !== false) {
		
					if($(this).parents('[FiligranaType = "FieldContainer"]').length == 0){
						if(attr == "Field"){
							
							thispage.addField($(this).attr('FiligranaName'));
						}else if(attr == "FieldContainer"){
							thispage.addFieldContainer(thispage,$(this).attr('FiligranaName'));
						}
						
					}else{
						
						
						var fieldContainerName =$($(this).parents()[0]).attr("FiligranaName");
						var container = thispage.getField(fieldContainerName);
						
						if(attr == "Field"){
							container.fields[$(this).attr('FiligranaName')] = new Field(container, $(this).attr('FiligranaName'));
							
						}else if(attr == "FieldContainer"){
							
							container.fieldcontainers[$(this).attr('FiligranaName')] = new FieldContainer(container, $(this).attr('FiligranaName'));
						}
					}
				}
				
			});
			/*for(var i in children){
				var a = children[i];
			
				
			}*/
	
	
	}
	
	
}




Page.prototype.setField = function(key, value){
	
	if(this.fields.hasOwnProperty(key)){

		this.fields[key].set(value);
	}
	for(var i in this.fieldcontainers){
		this.fieldcontainers[i].setField(key, value);
	}
	
}

Page.prototype.getField = function(key){
	if(this.fields.hasOwnProperty(key)){

		return this.fields[key].get();
	}else{
		
		for(var i in this.fieldcontainers){
			if(i == key){
				return this.fieldcontainers[i];
			}
			var res = this.fieldcontainers[i].getField(key);
			if(res != false){
				return res;
			}
		}
		return false;
	}
}

Page.prototype.getFieldNotValue = function(key){
	if(this.fields.hasOwnProperty(key)){

		return this.fields[key];
	}else{
		
		for(var i in this.fieldcontainers){
			if(i == key){
				return this.fieldcontainers[i];
			}
			var res = this.fieldcontainers[i].getField(key);
			if(res != false){
				return res;
			}
		}
		return false;
	}
}

Page.prototype.addField = function(key, html){
	
	if(this.fields.hasOwnProperty(key)){
		return false;
	}else{
		this.fields[key]= new Field(this,key, html);
		return true;
	}
	
	
}

Page.prototype.addFieldContainer = function(container,key){

	if(this.fields.hasOwnProperty(key)){
		return false;
	}else{
		
		this.fieldcontainers[key]= new FieldContainer(this,key );
		return true;
	}
}

Page.prototype.getSelector = function(){
	return '#' + this.id;
}

