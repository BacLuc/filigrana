/**
 * 
 */

var FieldContainer = function(container,key, tag){
	this.fields = {};
	this.fieldcontainers = {};
	this.id = "";
	this.selector = "";
	this.FiligranaName = "";
	this.page = null;
	this.value = "";
	this.htmlObject = null;
	this.container = null;
	this.fieldcontainer = null;
	this.tag = null;
	this.attrArray = [];
	this.constructor(container, key);
};


FieldContainer.prototype.constructor = function(container,key){
	
	this.container = container;
	if(container instanceof Page){
		
		
		this.page = container;
	}else{
		
		this.page = this.container.page;
		
	}
	
	this.id = $(this.container.getSelector() +' [FiligranaName="'+key+'"]').attr("id");
	if (typeof this.id !== typeof undefined && this.id !== false){
		this.selector = "#"+this.id;
	}else{
		this.selector = this.container.getSelector()+ ' [FiligranaName="'+key +'"]'; 
	}
	
	this.page=page;
	this.FiligranaName = key;

	this.value = $(this.getSelector()).val();
}


FieldContainer.prototype.setField = function(FiligranaName,value){
	for(var i in this.fields){
		if(i == FiligranaName){
			return this.fields[i].set(value);
		}
	}
		
	for(var i in this.fieldcontainers){
		var res = this.fieldcontainers[i].setField(FiligranaName, value); 
		if( res != false){
			return res;
		}
	}
	return false;
}

FieldContainer.prototype.getField = function(FiligranaName){
	
		if(FiligranaName == this.FiligranaName){
			return this;
		}
	for(var i in this.fields){
		if(i == FiligranaName){
			return this.fields[i].get();
		}
	}
		
	for(var i in this.fieldcontainers){
		var res = this.fieldcontainers[i].getField(FiligranaName); 
		if( res != false){
			return res;
		}
	}
	return false;
	
	
}

FieldContainer.prototype.appendFieldContainer = function(key, tag, attrArray){
	
	var html = '<';
	html += tag;
	html += ' FiligranaName ="'+key+'"';
	for(var i in attrArray){
		
		html += ' '+i.replace("__","-")+' = "'+attrArray[i]+'"';
	}
	html += '></'+tag+'>';

	var htmlObject = $(this.container.selector + ' [FiligranaName="'+this.FiligranaName+'"]');
	$(this.getSelector()).append(html);

	this.fieldcontainers[key] = new FieldContainer(this, key, tag);
	return this.fieldcontainers[key];
}

FieldContainer.prototype.appendField = function(key, tag, attrArray){
	var html = '<';
	html += tag;
	html += ' FiligranaName ="'+key+'"';
	for(var i in attrArray){
		html += ' '+i.replace("__","-")+' = "'+attrArray[i]+'"';
	}
	html += '></'+tag+'>';
	
	$(this.getSelector()).append(html);
	this.fields[key] = new Field(this, key, tag );
	return this.fields[key];
}

FieldContainer.prototype.remove= function(FiligranaName){
	if(this.FiligranaName == FiligranaName){
		$(this.getSelector()).remove();
		return true;
	}else{
		for(var i in this.fields){
			if(i == FiligranaName){
				return this.fields[i].remove();
			}
		}
			
		for(var i in this.fieldcontainers){
			var res = this.fieldcontainers[i].remove(FiligranaName); 
			if( res != false){
				return res;
			}
		}
		return false;
	}
}

FieldContainer.prototype.getSelector= function(){
	return this.selector;
}
