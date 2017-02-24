// canvas drawing logic
// based on example code by wacom
	
	var canvasPos = {x:0.0, y:0.0};
    var canvasSize = {width:500, height:300};
    var lastX = 0.0;
    var lastY = 0.0;
    var capturing = false;	// tracks in/out of canvas context
    var mouseDown = false;


	function getLineWidth(){
		return document.getElementById('size').value;
	}
	
	function getColor(){
		var color = document.getElementById('color').value;
		//console.log(color);
		return color;
	}
	
	function getCanvas(){
		return document.getElementById('imageCanvas');
	}


    //************************************************************************
    function plugin()
    {
        return document.getElementById('wtPlugin');
    }
      


   
    //************************************************************************
    function initCanvasDrawing()
    {
        var canvas = getCanvas();   
	canvas.addEventListener("mousemove", mousemove, true);
	canvas.addEventListener("mouseup", mouseup, true);
	canvas.addEventListener("mousedown", mousedown, true); 

	var ctx = canvas.getContext("2d");
	ctx.fillStyle="#f0f0f0";
	ctx.fillRect(0,0,canvasSize.width,canvasSize.height);
   }
   

	function fill(color){
		var canvas = getCanvas();
		var ctx = canvas.getContext("2d");
		ctx.fillStyle=color;
		ctx.fillRect(0,0,canvasSize.width,canvasSize.height);
	}

    //************************************************************************
    function mousedown(evt)
    {
	//console.log("MOUSE:DOWN");

        // Non-IE browsers will use evt
        var ev = evt || window.event;

        var canvas = getCanvas();
	canvas.onmousemove=mousemove;
        
	lastX = (ev.pageX?ev.pageX : ev.clientX + document.body.scrollLeft) - canvasPos.x;
	lastY = (ev.pageY?ev.pageY : ev.clientY + document.body.scrollTop ) - canvasPos.y;
        
	capturing = inCanvasBounds(lastX, lastY);
	mouseDown = true;

	// Register click immediately
	mousemove(evt);
    }



      
    //************************************************************************
    function mouseup()
    {
	//console.log("MOUSE:UP");
        var canvas = getCanvas();
        canvas.onmousemove=null;
	capturing = false;
	mouseDown = false;
    }

      

    //************************************************************************
    function mousemove(evt)
    {
	//console.log("MOUSE:MOVE");

        // Non-IE browsers will use evt
        var ev = evt || window.event;
        if(ev == null){
        	return;
        }

        var penAPI = plugin().penAPI;
        var canvas = getCanvas();
        var pressure = 0.0;
        var isEraser;
        var pointerType;

        if (penAPI)
        {
            pressure = penAPI.pressure;
            isEraser = penAPI.isEraser;
            pointerType  = penAPI.pointerType;
        }
        else
        {
            pressure = 1.0;
            isEraser = false;
            pointerType = 0;
        }
        
        if(pointerType == 0 && mouseDown){
        	pressure = 0.5;
        }

	//console.log("pressure: " + pressure);
        
	curX = (ev.pageX?ev.pageX : ev.clientX + document.body.scrollLeft) - canvasPos.x;
	curY = (ev.pageY?ev.pageY : ev.clientY + document.body.scrollTop ) - canvasPos.y;

	capturing = inCanvasBounds(curX, curY) && mouseDown;

	if (capturing && pressure > 0.0)
	{
            if (canvas.getContext)
	    {
                var ctx = canvas.getContext("2d");
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(curX, curY);
                
                //console.log("mousemove: cur: " + curX + "," + curY);

                ctx.lineCap = 'round';
                if (isEraser == true) 
                {
                    ctx.lineWidth = getLineWidth() * pressure;
                    ctx.strokeStyle = "rgba(255, 255, 255, 1.0)";
                }
                else 
                {
                    ctx.lineWidth = getLineWidth() * pressure;
                    ctx.strokeStyle = getColor();
                }
		
		//console.log("ctx.lineWidth: " + ctx.lineWidth);
                ctx.stroke();
	    }
	}
        
        lastX = curX;
        lastY = curY;

    }


    //************************************************************************
    // posX and posY are assumed relative to canvas boundaries.
    // Returns true if posX and posY are contained within canvas boundaries.
    function inCanvasBounds( posX, posY )
    {
        var left = 0;
	var top = 0;
        var right = canvasSize.width;
	var bottom = canvasSize.height;

	return ( posX >= left && posX <= right && 
		 posY >= top && posY <= bottom);
    }



    //************************************************************************