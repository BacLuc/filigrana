int lastX = -1, lastY = -1;
boolean mouseDown = false;
ArrayList<ArrayList> pathlist = new ArrayList();
ArrayList path = new ArrayList();
void setup(){
  frameRate(30);
  size(500, 300);
  strokeWeight(2);
  smooth();
  noFill();
  clearCanvas();
}
void draw(){
  if(mouseDown){
   if(lastX > -1 && lastY > -1){
     line(lastX, lastY, mouseX, mouseY);
   }
     lastX = mouseX;
     lastY = mouseY;
     float x = mouseX, y = mouseY;
     x /= width;
     y /= height;
    float[] point = new float[]{x, y};
    path.add(point);
  }
  
}

void mouseReleased() {
   lastX = -1;
   lastY = -1;
   mouseDown = false;
//   println("mouseReleased");
   endPath();
   drawPaths();
}

void mouseOut() {
   lastX = -1;
   lastY = -1;
   mouseDown = false;
 //  println("mouseOut");
   endPath();
   drawPaths();
}

void mousePressed() { 
  mouseDown = true;
  path = new ArrayList();
}

void endPath(){
 // for(int i = 0; i < path.size(); ++i){
 //    println(path.get(i));
 //  }
   pathlist.add(path);
}

void clearCanvas(){
   externals.context.clearRect(0,0,width,height);
}

void drawPaths(){
 clearCanvas();
 //println(pathlist.size());
 for(int i = 0; i < pathlist.size(); ++i){
  ArrayList p = pathlist.get(i);
  drawPath(p);
 }
 
}

void drawPath(ArrayList p){
  if(p.size() >= 3){ 
  beginShape();
  float[] point;
  point = p.get(0);
  curveVertex(point[0] * width, point[1] * height);
  for(int j = 0; j < p.size(); ++j){
   float[] point = p.get(j);
    curveVertex(point[0] * width, point[1] * height);
  }
  float[] point = p.get(p.size() - 1);
  curveVertex(point[0] * width, point[1] * height);
  endShape(); 
  fill(0);
  int x1 = point[0] * width, y1 = point[1] * height;
  float[] point = p.get(p.size() - 2);
  float x2 = point[0] * width, y2 = point[1] * height;
  float dx = x1 - x2, dy = y1 - y2;
  translate(x1, y1);
  rotate(atan2(dy, dx) - HALF_PI);
  triangle(-4, 0, 0, 8, 4, 0);
  resetMatrix();
  noFill();
  }
 }
 
 String getPaths(){
   String _return = "[";
   for(int i = 0; i < pathlist.size(); ++i){
     ArrayList p = pathlist.get(i);
     _return = _return + "[";
     for(int j = 0; j < p.size() - 1; ++j){
       float[] point = p.get(j);
       _return = _return + "[" + point + "]" + ","; 
     }
     float[] point = p.get(p.size() - 1);
       _return = _return + "[" + point + "]]"; 
       if(i < pathlist.size() - 1){
         _return = _return + ",";
       }
   }
   _return = _return + "]";
   return _return;
 }
 
void clearPaths(){
  pathlist = new ArrayList();
  drawPaths();
}
