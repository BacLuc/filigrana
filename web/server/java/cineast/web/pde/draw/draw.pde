color col = color(0);
int lastX = -1, lastY = -1;
int strokeWidth = 25;
boolean mouseDown = false;
PImage img = new PImage();
void setup(){
  frameRate(30);
  size(500, 300);
  colorMode(RGB, 255);
  smooth();
  noFill();
 clearCanvas();
}
void draw(){
  if(img.width > 0){
    image(img, 0, 0, width, height);
    img = new PImage();
  }
  if(mouseDown){
    getColor();
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
     line(lastX, lastY, mouseX, mouseY);
   }
     lastX = mouseX;
     lastY = mouseY;
  }
  
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
   //externals.context.clearRect(0,0,width,height);
   background(255);
}

function plugin()
{
        return document.getElementById("wtPlugin");
}



void getColor(){
  var val = document.getElementById("drawColor").value.substring(1);
  tmpcol = color(parseInt(val, 16));
  col = color(red(tmpcol), green(tmpcol), blue(tmpcol), 255);
}

void setStrokeWidth(int w){
  strokeWidth = w;
}

void fillCanvas(){
  getColor();
  background(col);
}

void setImage(String url){
  img = loadImage(url);
}

void touchStart(TouchEvent touchEvent){
  mousePressed();
}

void touchEnd(TouchEvent touchEvent){
  mouseReleased();
}

void touchCancel(TouchEvent touchEvent){
  mouseOut();
}
