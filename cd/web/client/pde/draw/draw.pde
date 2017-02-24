color col = color(0,0,0,255);
int lastX = -1, lastY = -1;
int strokeWidth = 10;
int opacity = 255;
boolean isMenuShown = false;
int menuwidth = 150;

boolean mouseDown = false;
PImage img = new PImage();
void setup(){
  frameRate(30);
  size(970, 301);
  colorMode(RGB, 255);
  //smooth();
  noFill();
 clearCanvas();
}
void setup(int width, int height){
  frameRate(30);
  size(width, height);
  colorMode(RGB, 255);
  smooth();
  noFill();
 clearCanvas();
 background(0,0,0,0);
}

void draw(){
  if(img.width > 0){
    image(img, 0, 0, width, height);
    img = new PImage();
  }
  if(mouseDown){
   if(lastX > -1 && lastY > -1){
     var penAPI = plugin().penAPI;
     if(penAPI){
       var pressure = penAPI.pressure * strokeWidth;
       if(pressure <= 0){
         pressure = strokeWidth;
       }
       strokeWeight(pressure);
       if(penAPI.isEraser){
         stroke(255);
       }else{
         stroke(col);
       }
     }else{
       stroke(col);
       strokeWeight(strokeWidth);
     }
    drawLine(lastX, lastY, mouseX, mouseY); 
   }
     lastX = mouseX;
     lastY = mouseY;
  }
  
}
void drawLine(startx, starty, endx, endy){
	if(isMenuShown){
	
		startx = startx - menuwidth;
		endx = endx - menuwidth;
	
	}
	lines.push({
		sx : startx,
		sy : starty,
		ex : endx,
		ey: endy
	});
	
	if(col == color(255,255,255,255)){
		line(startx, starty, endx, endy);
		turnWhiteToTransparent();
	}else{
		tint(255, opacity);
		line(startx, starty, endx, endy);
		//setOpacity(opacity);
	}
}

void undo(){
	clearCanvas();
	var tmplines = [];
	for(var i in lines){
		tmplines.push(lines[i]);
	}
	lines = [];
	lastX = tmplines[0].sx;
	lastY = tmplines[0].sy;
	
	for(int i=0; i<tmplines.length-1;i++){
		if(tmplines[i].sx != -1 && tmplines[i].sy != -1){
			console.log('drawing line from: '+ tmplines[i].sx+'//'+tmplines[i].sy+' to: '+tmplines[i].ex+'//'+tmplines[i].ey);
			drawLine(tmplines[i].sx,tmplines[i].sy,tmplines[i].ex,tmplines[i].ey);
		}
	}
	

}

void setAsEreaser(){
	col = color(255,255,255,255);
	
}


void setToDraw(){
	col = color(0,0,0,255);
	
}

void setMenuShown(){
	isMenuShown = true;
}

void setMenuHidden(){
	isMenuShown = false;
}

void mouseReleased() {
   lastX = -1;
   lastY = -1;
   mouseDown = false;
}

void mouseOut() {
   lastX = -1;
   lastY = -1;
   mouseDown = false;
}

void mousePressed() { 
  mouseDown = true;
}

void clearCanvas(){
   loadPixels();
   for (int i = 0; i< pixels.length; i++){
   		pixels[i] = color(0,0,0,0);
   }
   updatePixels();
}

void turnWhiteToTransparent(){
	loadPixels();
   for (int i = 0; i< pixels.length; i++){
   		if(pixels[i] == color(255,255,255,255)){
   			pixels[i] = color(0,0,0,0);
   		}
   }
   updatePixels();
}

function plugin()
{
        return document.getElementById("wtPlugin");
}





void setStrokeWidth(int w){
  strokeWidth = w;
}

void fillCanvas(){
  
  background(col);
}

void setImage(String url){
  img = loadImage(url);
}
/*
void touchStart(TouchEvent touchEvent){
	
	
	$('#Logger').append("recieved touchstart event <br>");
	$('#Logger').append(logRecursive(touchEvent));
	touchEvent.preventDefault();
	 for (var i=0; i < touches.length; i++) {

		    ongoingTouches.push(copyTouch(touches[i]));
		   
		  }
  mousePressed();
  
}

void touchMove(TouchEvent touchEvent){
	//$('#Logger').append("recieved touchmove event <br>");
	touchEvent.preventDefault();
	$('#Logger').append("recieved touchend event <br>");
	$('#Logger').append(logRecursive(touchEvent));
    
	  for (var i=0; i < touches.length; i++) {
	    ongoingTouches.push(copyTouch(touches[i]));
	    
	  }
	console.log(touchEvent);
	mousePressed();

}

void touchEnd(TouchEvent touchEvent){
	$('#Logger').append("recieved touchend event "+touchEvent+" <br>");
	$('#Logger').append(logRecursive(touchEvent));
	if(ongoingTouches.length > 1){
		  for (int i=0; i < touches.length-1; i++) {
			  drawLine(ongoingTouches[i].pageX,ongoingTouches[i].pageY,ongoingTouches[i+1].pageX,ongoingTouches[i+1].pageY );
		  }
	  }else{
		  drawLine(ongoingTouches[0].pageX,ongoingTouches[0].pageY,ongoingTouches[0].pageX,ongoingTouches[0].pageY );
	  }
	ongoingTouches = [];
  mouseReleased();
}

void touchCancel(TouchEvent touchEvent){
	$('#Logger').append("recieved touchcancel event <br>");
	$('#Logger').append(logRecursive(touchEvent));
	if(ongoingTouches.length > 1){
		  for (int i=0; i < touches.length-1; i++) {
			  drawLine(ongoingTouches[i].pageX,ongoingTouches[i].pageY,ongoingTouches[i+1].pageX,ongoingTouches[i+1].pageY );
		  }
	  }else{
		  drawLine(ongoingTouches[0].pageX,ongoingTouches[0].pageY,ongoingTouches[0].pageX,ongoingTouches[0].pageY );
	  }
	ongoingTouches = [];
  mouseOut();
}
*/
void setOpacity(int op){

	 loadPixels();
	int beforeopacity = opacity;
   for (int i = 0; i< pixels.length; i++){
   		if(pixels[i] == color(0,0,0,255) || pixels[i] == color(0,0,0,beforeopacity)){
   			pixels[i] = color(0,0,0,op);
   		}
   }
   opacity = op;
   updatePixels();

}

