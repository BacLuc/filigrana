function frameToTime(framenumber, fps){
	var min = Math.floor(framenumber / (60 * fps));
	framenumber -= (min * 60 * fps);
	var sec = Math.floor(framenumber / fps);
	return min + ':' + (sec < 10 ? "0" + sec : sec);
}

function printPercent(fraction){
	return (Math.round(10000 * fraction) / 100) + '%';
}

function resultTableRow(val){
	var row = "<tr>";
	row += "<td><span class='glyphicon glyphicon-search'></span><span class='glyphicon glyphicon-remove'></span><br /><span class='glyphicon glyphicon-plus'></span><span class='glyphicon glyphicon-minus'></span></td>";
	row += "<td><img src=\"../thumbnails/" + val.videoid + "/" + val.shotid + ".png\" class=\"img-thumbnail center-block shotimg\" id=\"" + val.shotid + "\" data-videopath=\"" + val.path + " \" data-starttime=\"" + (val.startframe / val.framerate) + "\"/></td>";
	row += "<td>" + printPercent(val.score) + "</td><td class='videoname'>" + val.name + "</td>";
	row += "<td>" + frameToTime(val.startframe, val.framerate) + "</td><td>" + frameToTime(val.endframe, val.framerate) + "</td>";
	row += "</tr>";
	
	return row;
}

function removeFromArray(array, element){
	var index = array.indexOf(element);
	if (index > -1) {
		array.splice(index, 1);
	}
}
//http://stackoverflow.com/questions/1517924/javascript-mapping-touch-events-to-mouse-events
function touchHandler(event)
{
    var touches = event.changedTouches,
        first = touches[0],
        type = "";
         switch(event.type)
    {
        case "touchstart": type = "mousedown"; break;
        case "touchmove":  type="mousemove"; break;        
        case "touchend":   type="mouseup"; break;
        default: return;
    }

             //initMouseEvent(type, canBubble, cancelable, view, clickCount, 
    //           screenX, screenY, clientX, clientY, ctrlKey, 
    //           altKey, shiftKey, metaKey, button, relatedTarget);

    var simulatedEvent = document.createEvent("MouseEvent");
    simulatedEvent.initMouseEvent(type, true, true, window, 1, 
                              first.screenX, first.screenY, 
                              first.clientX, first.clientY, false, 
                              false, false, false, 0/*left*/, null);

                                                                                 first.target.dispatchEvent(simulatedEvent);
    event.preventDefault();
}

function initMouseTouch() 
{
    document.addEventListener("touchstart", touchHandler, true);
    document.addEventListener("touchmove", touchHandler, true);
    document.addEventListener("touchend", touchHandler, true);
    document.addEventListener("touchcancel", touchHandler, true);    
}