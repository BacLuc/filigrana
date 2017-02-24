<?php
session_start();
include("../server/php/include.php");


?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Filigrana WebApp</title>
	<meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,height=device-height, initial-scale=1"/>
	
	
	<!--Jquery-->
			<!-- Latest compiled and minified CSS -->
		<script src="js-lib/JQuery-2.1.1/jquery-2.1.1.min.js"> </script>
		<script src="js-lib/JQuery-2.1.1/jquery-form.min.js"> </script>
		<script src="js-lib/JQuery-2.1.1/jquery-ui.min.js"> </script>
		<script src="js-lib/JQuery-2.1.1/jquery.ui.touch-punch.min.js"> </script>
		
	<!--Bootstrap-->
			<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="js-lib/bootstrap-3.2.0-dist/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="js-lib/bootstrap-3.2.0-dist/css/bootstrap-theme.min.css">

		<!-- Latest compiled and minified JavaScript -->
		<script src="js-lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>
		
		<!-- Slider -->
		<script src="js-lib/bootstrap-3.2.0-dist/js/bootstrap-slider.js" type="text/javascript"></script>
		<link href="js-lib/bootstrap-3.2.0-dist/css/slider.css" rel="stylesheet" />
		
		<!-- navmenu sidebar -->
		<link href="js-lib/bootstrap-3.2.0-dist/css/navmenu-push.css" rel="stylesheet" />
		<link href="js-lib/bootstrap-3.2.0-dist/css/jasny-bootstrap.css" rel="stylesheet" />
		<script src="js-lib/bootstrap-3.2.0-dist/js/jasny-bootstrap.min.js" type="text/javascript"></script>
		
		
	<!-- Processing -->
		<script src="js-lib/processing/processing-1.4.1.js" type="text/javascript"></script>
	<!-- Filigrana -->
		<script src="js/main.js" type="text/javascript"></script>
		<script src="js/Page.js" type="text/javascript"></script>
		<script src="js/Field.js" type="text/javascript"></script>
		<script src="js/FieldContainer.js" type="text/javascript"></script>
		<script src="js/Filigrana_User_Module.js" type="text/javascript"></script>
		<script src="js/Filigrana_Watermark_Module.js" type="text/javascript"></script>
		<script src="js/Filigrana_IPH_Module.js" type="text/javascript"></script>
		<script src="js/Filigrana_Search_Module.js" type="text/javascript"></script>
		
		
		<script src="js/Navmenu.js" type="text/javascript"></script>
		<link href="css/main.css" rel="stylesheet" />
		
		
		<?php
		$json =  isset($_GET) ? json_encode($_GET) : json_encode('null');
		if(!isset($_SESSION['username'])){
			echo "
			<script>
				parameters = {
						get : ".$json.",
						server : 'ajax_server.php',
						search : {}
					};
			</script>
			";
		}else{
			echo "
			<script>
				parameters = {
						get : ".$json.",
						server : 'ajax_server.php',
						search : {},
						session:{
							username: '".$_SESSION['username']."',
							email: '".$_SESSION['email']."',
							createWatermark: ".$_SESSION['createWatermark'].",
							createIPHType: ".$_SESSION['createIPHType']."
						}
					};
			</script>
			";
		}
		
		?>
		<script>
		page = null;
		timer = 0;
	    timeout = 3000;
		lines = [];
		ongoingTouches = []; 
		WebApp = null;

		
		
		
		function copyTouch(touch) {
			  return { identifier: touch.identifier, pageX: touch.pageX, pageY: touch.pageY };
		}
			  
		function onLoad(){
				WebApp = new Filigrana();
				
				$.getApp = function(){
					return WebApp;
				}
		
				WebApp.instantiate(parameters);
				
				
				
				if(window.location.hash == ""){
				    $.getApp().switchPage("login_page", false);
				}else{
					 $.getApp().switchPage(window.location.hash, false);
				}
				//Touchinit();
				
			
				
			    
			}
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

		function Touchinit() {
		    document.getElementById('draw').addEventListener("touchstart", touchHandler, true);
		    document.getElementById('draw').addEventListener("touchmove", touchHandler, true);
		    document.getElementById('draw').addEventListener("touchend", touchHandler, true);
		    document.getElementById('draw').addEventListener("touchcancel", touchHandler, true);
		}
		
		
		
		function logRecursive(object, recLevel){
				if(recLevel == undefined){
						recLevel = 1;
					}
				if(recLevel == 2 || typeof object == "function" || object == null || object == undefined || typeof object != "object"){
					return object+"<br>";
				}else{
					string = "";
					for(var i in object){
							string += logRecursive(object);
						}
					return string;
				}

			}
		</script>
	
		
		


</head>
<body onload="onLoad()" class="canvas">
	 <div style="" class="navmenu navmenu-default navmenu-fixed-left offcanvas" id="side_nav_real">
      <a class="navmenu-brand">Filigrana WebApp</a>
      <ul class="nav navmenu-nav">
      	
      	<li>
	      	<div class="clickable btn-group" width="100%">
				  <button type='button' FiligranaAction="back" id="nav_back_button" type="button" class="clickable btn btn-default btn-default"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span></button>
				  <button type='button' FiligranaAction="forward" id="nav_forward_button" type="button" class="clickable btn btn-default"><span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></button>
						 
			</div>
      	</li>
      	<li class="clickable" topage="menu_page"><a href="">back to Menu</a></li>
        <li class="imageli_navmenu">Your Image:</li>
		<li class="imageli_navmenu"><img src="" alt="" id="navimage_picture" class="img-responsive"/></li> 
		 <li class="imageli_navmenu">Your Sketch:</li>
		<li class="imageli_navmenu"><img src="" alt="" id="navimage_sketch" class="img-responsive"/></li>
		<!--  because of some problems with some browsers 
		<li class="dropdown plusButton">
          <a href="" class="dropdown-toggle plusButton" data-toggle="dropdown">Navigation <b class="caret"></b></a>
          <ul class="dropdown-menu navmenu-nav plusButton">
            <li class="clickable plusButton" topage="menu_choose_picture_source_page"><a href="">Search by sketch</a></li>
            <li class="divider plusButton"></li>
            <li class="clickable plusButton" topage="search_page"><a href="">Search by text</a></li>
            <li class="divider plusButton"></li>
            <li class="clickable plusButton" topage="manage_user_page"><a href="">Manage Accounts</a></li>
             <li class="divider plusButton"></li>
            <li class="clickable plusButton" topage="manage_watermark_page"><a href="">add Watermark</a></li>
             <li class="divider plusButton"></li>
            <li class="clickable plusButton" topage="manage_iph_type_page"><a href="">add IPH Type</a></li>
          </ul>
        </li>
         -->
        <!-- Sketch Menu -->
        <li class="sketch_page">
        	<button type='button' FiligranaAction="SketchWithImage" id="sketch_with_image" type="button" class="clickable btn btn-default btn-default:active">With Image</button>
		</li>
		<li class="sketch_page">
			<button type='button' FiligranaAction="SketchWithoutImage" id="sketch_without_image" type="button" class="clickable btn btn-default">Without Image</button>
		</li>
		 <li class="sketch_page">
        	 <button type='button' FiligranaAction="SketchDraw" id="sketch_draw" type="button" class="clickable btn btn-default btn-default:active">Draw</button>
		</li>
		<li class="sketch_page">
			<button type='button' FiligranaAction="SketchErase" id="sketch_erase" type="button" class="clickable btn btn-default">Erase</button>
		</li>
		<li class="sketch_page">
			<button type='button' FiligranaAction="SketchClear" id="sketch_clear" type="button" class="clickable btn btn-default">Clear</button>
		</li>
		<!-- 
		<li class="sketch_page">
			<button type='button' FiligranaAction="DetectEdges" type="button" class="clickable btn btn-default">Detect Edges</button>
		</li>
		 -->
		<li class="sketch_page">
			<div>Pensize:</div>
				
			<div id="penSize_slider"></div>
					
		</li>
		<li class="sketch_page">
			<div>Opacity:</div>
		
			<div id="opacity_slider"></div>
					
		</li>			 
        
        <!-- Search Menu Button -->
		        
        <!-- Watermark edit buttons -->
        <li class="watermark admin owner clickable imageli_navmenu currentButtons" FiligranaName="WatermarkAppendImage" FiligranaType="Field" FiligranaAction="watermarkAppendImage"><a href="">+current image</a></li>
        <li class="watermark admin owner clickable imageli_navmenu currentButtons" FiligranaName="WatermarkAppendSketch" FiligranaType="Field" FiligranaAction="watermarkAppendSketch"><a href="">+current sketch</a></li>
      <!-- 
        <li class="watermark admin editor clickable subWatermarkAddon" FiligranaName="createWatermarkSubWatermark" FiligranaType="Field" FiligranaAction="createSubWatermark" topage="manage_watermark_page"><a href="">Create sub watermark</a></li>
		<li class="watermark admin editor clickable subWatermarkAddon" FiligranaName="createWatermarkSuperWatermark" FiligranaType="Field" FiligranaAction="createSuperWatermark" topage="manage_watermark_page"><a href="">Create super watermark</a></li>
		 -->	
		<li class="watermark admin owner clickable" FiligranaName="DeleteWatermark" FiligranaType="FieldContainer" FiligranaAction="DeleteWatermark" topage="menu_page"><a href="">Delete Watermark</a></li>
		
		<!-- IPH Type edit buttons -->
        <li class="iph admin owner clickable imageli_navmenu currentButtons" FiligranaName="IPHAppendImage" FiligranaType="Field" FiligranaAction="IPHAppendImage"><a href="">+current image</a></li>
        <li class="iph admin owner clickable imageli_navmenu currentButtons" FiligranaName="IPHAppendSketch" FiligranaType="Field" FiligranaAction="IPHAppendSketch"><a href="">+current sketch</a></li>
      
        <!-- <li class="iph admin editor clickable subWatermarkAddon" FiligranaName="createWatermarkSubIPHType" FiligranaType="Field" FiligranaAction="createSubWatermark" topage="manage_watermark_page"><a href="">Create sub watermark</a></li> -->
		<li class="iph admin owner clickable" FiligranaName="DeleteWatermark" FiligranaType="FieldContainer" FiligranaAction="DeleteIPHType" topage="menu_page"><a href="">Delete IPH type</a></li>
		<li class= "clickable" FiligranaName="LogoutLink" FiligranaType="Field" FiligranaAction="Logout" topage="login_page"><a href="">Logout</a></li>      
      </ul>
     
    </div>

    <div style="position: fixed; max-width: 20px; top: 0; left:0;" class="navbar navbar-default navbar-fixed-top clickable" id="side_nav" FiligranaAction="show_sidenav" >
      <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target=".navmenu" data-canvas="body">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>


	<div class = "container">
		<div id="login_page" class="page centered button_menu">
		 <form class="form-signin" role="form">
	        <h2 class="form-signin-heading">Webapp Filigrana: Login</h2>
	        <div class= "row">
	        	<div class= "col-xs-12 col-sm-6">
	        		<div>Email:</div>
	        	</div>
	        	<div class= "col-xs-12 col-sm-6">
	        		<input FiligranaName="loginEmail" FiligranaType="Field" type="email" class="form-control email textInputField" placeholder="Email address" required autofocus>
	        	</div>
	        </div>
	        <div class = "row">
	        	<div class= "col-xs-12 col-sm-6">
	        		<div>Password:</div>
	        	</div>
	        	<div class= "col-xs-12 col-sm-6">
			    	<input type="password" FiligranaName="loginPassword" FiligranaType="Field" class="form-control textInputField" placeholder="Password" required>
			    </div>
	        	<div class= "col-xs-12 col-sm-6">
	        		<a href="forgot_password.html" FiligranaAction="ForgotPassword">forgot Password</a>
	        	</div>
	        	
	        	
	        </div>	
	        <div class = "row">	
	         	<div class="col-xs-12">
						<span class="label label-danger alertMessage pull-right" id="danger_data_msg"></span>
						<span class="label label-warning alertMessage pull-right" id="warning_data_msg"></span>
				</div>
			</div>
			<div class = "row">
				<div>
	        		<button type='button' FiligranaAction="Login" id="login_button" topage="menu_page" class="clickable btn btn-lg btn-primary btn-block" >Log in</button>
	      		</div>
	      </div>
	      </form>
		
		</div>
		
		<div id="register_page" class="page centered button_menu center-block">
			<form class="form-signin" role="form">
		        <div class="centerHorizontallydiv">
		        	<h2 class="form-signin-heading">Webapp Filigrana: Register</h2>
		        
			        <div class= "row">
			        	<div class= "col-xs-12 col-sm-12">
			        		<div FiligranaType="Field" FiligranaName="ChoosePasswordTitle"></div>
			        	</div>
			        	
			        </div>
			         <div class= "row">
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>Username:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="text" FiligranaName="registerUsername" FiligranaType="Field" class="form-control text textInputField" placeholder="Username" required>
			        	</div>
			        </div>
			        <div class= "row">
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>Password:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="password" FiligranaName="registerPassword" FiligranaType="Field" class="form-control textInputField" placeholder="Password" required>
			        	</div>
			        </div>
			      
			        <div class= "row">
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>Repeat Password:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="password"  FiligranaName="registerPasswordRepeat" FiligranaType="Field" class="form-control repeat-password textInputField" placeholder="Repeat password" required>
			        		
			        	</div>
			        </div>
			        
			        	
				    <div class = "row">	
			         	<div class="col-xs-12">
								<span class="label label-danger alertMessage pull-right" id="danger_data_msg"></span>
								<span class="label label-warning alertMessage pull-right" id="warning_data_msg"></span>
						</div>     
				        <button type='button' id="register_button" topage="menu_page" FiligranaType="Field" FiligranaName="registerButton" 
				        FiligranaAction="register" class="clickable btn btn-lg btn-primary btn-block" >Register</button>
				    </div>
				</div>
			</form>	
		</div>
		
		<div id="set_password_page" class="page centered button_menu center-block">
			<form class="form-signin" role="form">
		        <div class="centerHorizontallydiv">
		        	<h2 class="form-signin-heading">Webapp Filigrana: Reset password</h2>
		        
			        <div class= "row">
			        	<div class= "col-xs-12 col-sm-12">
			        		<div FiligranaType="Field" FiligranaName="ChoosePasswordTitle"></div>
			        	</div>
			        	
			        </div>
			         
			        <div class= "row">
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>Password:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="password" FiligranaName="registerPassword" FiligranaType="Field" class="form-control textInputField" placeholder="Password" required>
			        	</div>
			        </div>
			      
			        <div class= "row">
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>Repeat Password:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="password"  FiligranaName="registerPasswordRepeat" FiligranaType="Field" class="form-control repeat-password textInputField" placeholder="Repeat password" required>
			        		
			        	</div>
			        </div>
			        
			        	
				    <div class = "row">	
			         	<div class="col-xs-12">
								<span class="label label-danger alertMessage pull-right" id="danger_data_msg"></span>
								<span class="label label-warning alertMessage pull-right" id="warning_data_msg"></span>
						</div>     
				        <button type='button' id="register_button" topage="menu_page" FiligranaType="Field" FiligranaName="registerButton" 
				        FiligranaAction="register" class="clickable btn btn-lg btn-primary btn-block" >Register</button>
				    </div>
				</div>
			</form>	
		</div>
		
	
		<div id="menu_page" class="page centered button_menu">
			<div class="row">
					<div class = "col-xs-12">
						<h2 class="form-signin-heading">Webapp Filigrana: Menu</h2>
					</div>
			</div>
			<div class="row">
				<div class = "col-xs-12">
					<button type='button' id="search_watermark" FiligranaAction="setToSearch" topage="menu_choose_picture_source_page" class="clickable btn btn-lg btn-primary btn-block">Search by sketch</button>
				</div>
			</div>
			<div class="row">
				<div class = "col-xs-12">
					<button type='button' id="search_by_text" topage="search_page" class="clickable btn btn-lg btn-primary btn-block">Search by Text</button>
				</div>
			</div>
			<div class="row">
				<div class = "col-xs-12">
					<button type='button' id="manage_accounts" topage="manage_user_page" class="clickable btn btn-lg btn-primary btn-block">Manage Accounts</button>
				</div>
			</div>
			<div class="row">
				<div class = "col-xs-12">
					<button type='button' id="add_watermark" FiligranaAction="createWatermark" topage="manage_watermark_page" class="clickable btn btn-lg btn-primary btn-block">Add Watermark</button>
				</div>
			</div>
			<div class="row">
				<div class = "col-xs-12">
					<button type='button' id="add_iph_type" FiligranaAction="createIPHType" topage="manage_iph_type_page" class="clickable btn btn-lg btn-primary btn-block">Add IPH Type</button>
				</div>
			</div>
		
		</div>
		
		<div id="menu_choose_picture_source_page" class="page centered button_menu">
		<div class="row">
					<div class = "col-xs-12">
						<h2 class="form-signin-heading">Webapp Filigrana: Choose Picture Source</h2>
					</div>
			</div>
		<div class="row">
				<div class = "col-xs-12">
					<button type='button' id="select_picture_camera" FiligranaAction="camera" topage="choose_picture_page" class="img_upload clickable btn btn-lg btn-primary btn-block">Select from Gallery/Folder/Camera</button>
					<form id="uploadImageForm" class = "img_form hidden" action='src/php/ajax_server.php' method='post' enctype="multipart/form-data">
										<input type='hidden' name='action' value='uploadImage'>
										<input type='hidden' name='index' value='image'>
										<input id='uploadImage' type='file' accept="image/*" name='image' >
										<input type='hidden' name='is_ajax' value='1'>
					</form>
				</div>
			
				<div class = "col-xs-12">
					<button type='button' id="sketch_without_picture" topage="sketch_page" class="clickable btn btn-lg btn-primary btn-block">Sketch without Picture</button>
				</div>
				
			
			
			</div>
		
		</div>
		
		<div id="choose_picture_page" class="page centered">
			<div class="row" id="choose_picture_page_header">
				<div class = "col-xs-6">
					<button type='button' id="choose_another_picture" topage="menu_choose_picture_source_page" class="clickable btn btn-lg btn-primary btn-block">Choose other picture</button>
				</div>
				<div class = "col-xs-6">
					<button type='button' id="select_this_picture"  topage="sketch_page" class="clickable btn btn-lg btn-primary btn-block">Sketch with this picture</button>
				</div>
			</div>
			<div class="row">
				
					<img id="choose_image_img" src="/img/test/testpictures/testwm.jpg" alt="" class ="img-responsive fill_screen_div"/>
				
				
			
			</div>
		
		</div>
		
		<div id="sketch_page" class="page centered">
			<!-- 
			<div class ="row" id="sketch-header">
				
				<div class="col-xs-12 col-sm-3">
					<div class="clickable btn-group">
					  <button type='button' FiligranaAction="SketchWithImage" id="sketch_with_image" type="button" class="clickable btn btn-default btn-default:active">With Image</button>
					  <button type='button' FiligranaAction="SketchWithoutImage" id="sketch_without_image" type="button" class="clickable btn btn-default">Without Image</button>
					 
					</div>
				
				</div>
				<div class="col-xs-12 col-sm-3">
					<div class="clickable btn-group">
					  <button type='button' FiligranaAction="SketchDraw" id="sketch_draw" type="button" class="clickable btn btn-default btn-default:active">Draw</button>
					  <button type='button' FiligranaAction="SketchErase" id="sketch_erase" type="button" class="clickable btn btn-default">Erase</button>
					 
					</div>
				
				</div>
				<div class= "col-xs-12 col-sm-3">
					<div>Pensize:</div>
				
					<div id="penSize_slider"></div>
					<script>
					$(function(){
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
						document.getElementsByClassName('slider')[0].style.cssText = "width: undefined;";
					});
					</script>
				</div>
				<div class= "col-xs-12 col-sm-3">
					<div>Opacity:</div>
		
					<div id="opacity_slider"></div>
					<script>
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
						document.getElementsByClassName('slider')[1].style.cssText = "width: undefined;";
					});
					</script>
				
				
				</div>
				
				
			
			</div>
			 -->
			<div class="row">
				<!-- TO DO: SKETCH LAYOUT -->
				<div id="drawarea" class = "fill_screen_div" >
					<div id="background_between" class= "background-between">
					</div>	
					<canvas id="draw" data-processing-sources="pde/draw/draw.pde"></canvas>
					<img id="sketch_image_img" src="/img/app/white.jpg" alt="" class ="img-responsive fill_screen_div"/>
					
				</div>
			
				
			
			
			</div>
			<div class="row" id="sketch_footer">
				<div class = "col-xs-12">
					<button type='button' id="finished_sketching" FiligranaAction="SearchWatermarkSketch" topage="choose_watermark_page" class="clickable btn btn-lg btn-primary btn-block">Search</button>
					<button type='button' style="display: none" id="addSketchButton" FiligranaAction="addSketch" class="clickable btn btn-lg btn-primary btn-block">add sketch</button>
					
				</div>
				<div id="Logger">
				</div>
			</div>
			
		
		</div>
		
		<div id="choose_watermark_page" class="page centered">
			
			<div class="row">
				<div class="col-xs-0 col-sm-6">
				</div>
				<div class="col-xs-12 col-sm-6">
					 <button type='button' FiligranaAction="lookupIPHType" id="lookupIPhButton" topage="choose_iph_type_page" type="button" class="clickable btn btn-default">Watermark not found
					 																																							, lookup IPH Type</button>
				</div>
				
			</div>
				
				
			<div FiligranaType="FieldContainer" FiligranaName="chooseWatermarkContainer" id= "WatermarkContainer">
			
			
			</div>
				
			
			
			
			<div class="row">
				<div class="col-xs-0 col-sm-6">
				</div>
				<div class="col-xs-12 col-sm-6">
					 <button type='button' FiligranaAction="lookupIPHType" id="lookupIPhButton" topage="choose_iph_type_page" type="button" class="clickable btn btn-default">Watermark not found
					 																																							,lookup IPH Type</button>
				</div>
				
			</div>
		
		</div>
		
		<div id="choose_iph_type_page" class="page centered">
			
			
					<div  FiligranaType="FieldContainer" FiligranaName="chooseIPHTypeContainer" id= "IPHTypeContainer">
					
					
					</div>
				
		
		</div>
		
		
		<div id="manage_user_page" class="page centered">
			<h2 class="form-signin-heading">Webapp Filigrana: Manage User(s)</h2>
			<div class= "row">
			 	<form class="form-signin" role="form">
		        	<div class= "col-xs-12 col-sm-4">
		        		<div>Username:</div>
		        	</div>
		        	<div class= "col-xs-10 col-sm-4">
		        		<input FiligranaName="Username" FiligranaType="Field" type="text" class="form-control text textInputField" >
		        	</div>
		        	<div class = "col-xs-2 col-sm-4">
						<button type='button' id="change-username" FiligranaAction="goToChangeUsername" topage="change_username_page" class="clickable btn  plusButton"><span class="glyphicon glyphicon-pencil"></span></button>
					</div>
				</form>
	        	
	        	
	        </div>
			<div class= "row">
			 	<form class="form-signin" role="form">
		        	<div class= "col-xs-12 col-sm-4">
		        		<div>Email:</div>
		        	</div>
		        	<div class= "col-xs-10 col-sm-4">
		        		<input FiligranaName="loginEmail" FiligranaType="Field" type="email" class="form-control email textInputField" >
		        	</div>
		        	<div class = "col-xs-2 col-sm-4">
						<button type='button' id="change-email" FiligranaAction="goToChangeEmail" topage="change_email_page" class="clickable btn  plusButton"><span class="glyphicon glyphicon-pencil"></span></button>
					</div>
				</form>
	        	
	        	
	       </div>
	       <div class= "row">
	        	<div class= "col-xs-12 col-sm-4">
	        		<div>Password:</div>
	        	</div>
	        	<div class= "col-xs-10 col-sm-4">
	        	
	        		<div>**********</div>
		        		
		        
	        	</div>
	        	<div class = "col-xs-2 col-sm-4">
					<button type='button' id="change-password" FiligranaAction="goToChangePassword" topage = "change_password_page" class="clickable btn  plusButton"><span class="glyphicon glyphicon-pencil"></span></button>
				</div>
	        	
	        	
	        </div>
   
	        <div class = "row" FiligranaName = "ResearchGroupContainer" FiligranaType="FieldContainer">
	        	
				
	        </div>
	        
		    	        	
		</div>
		
		<div id="confirm" class = "page">
			<div class = "row">
				<form class="form-signin" role="form">	
					 <h2 class="form-signin-heading">Confirm with Password</h2>
		        	<div class= "col-xs-12 col-sm-6">
		        		<div>Password:</div>
		        	</div>
		        	<div class= "col-xs-12 col-sm-6">
		        		<input type="password" class="form-control password textInputField" placeholder="Password" required>
		        		
		        	</div>
		        	<div class = "col-xs-12 col-sm-3">
						<button type='button' id="confirm" class="clickable btn btn-lg btn-primary btn-block" >Confirm</button>
					</div>
		        </form>	
	        	
	        </div>	
		</div>
		
		<div id="change_password_page" class = "page">
			<form class="form-signin" role="form">	
				<h2 class="form-signin-heading">Change Password</h2>
				<div class = "row">
					
						 
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>Old Password:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="password" FiligranaName="changePasswordOld" FiligranaType="Field" class="form-control password textInputField" placeholder="Password" required>
			        		
			        	</div>
			        	
			    
		        	
		        </div>
				<div class = "row">
					
						 
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>New Password:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="password" FiligranaName="changePasswordNew" FiligranaType="Field" class="form-control password textInputField" placeholder="Password" required>
			        		
			        	</div>
			        	
			    
		        	
		        </div>
		        
		        <div class = "row">
					
						 
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>Repeat New Password:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="password" FiligranaName="changePasswordNew2" FiligranaType="Field" class="form-control password textInputField" placeholder="Password" required>
			        		
			        	</div>
			        	
			    
		        	
		        </div>
		        <div class="row">
		        	<div class = "col-xs-12 col-sm-3">
							<button type='button' id="confirm" FiligranaName="saveNewPasswordButton" FiligranaType="Field" 
							FiligranaAction="saveNewPassword" topage="manage_user_page" class="clickable btn btn-lg btn-primary btn-block textInputField" >Change</button>
						</div>
		        </div>
			</form>
		</div>
		
		<div id="change_username_page" class = "page">
			<form class="form-signin" role="form">	
				<h2 class="form-signin-heading">Change Username</h2>
				<div class = "row">
					
						 
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>Old Username:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input disabled type="text" FiligranaName="Username" FiligranaType="Field" class="form-control password textInputField" placeholder="" required>
			        		
			        	</div>
			        	
			    
		        	
		        </div>
				<div class = "row">
					
						 
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>New Username:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="text" FiligranaName="newUsername" FiligranaType="Field" class="form-control password textInputField" placeholder="New username" required>
			        		
			        	</div>
			        	
			    
		        	
		        </div>
		        
		        
		        <div class="row">
		        	<div class = "col-xs-12 col-sm-3">
							<button type='button'  FiligranaName="saveNewUsernameButton" FiligranaType="Field" 
							FiligranaAction="changeUsername" topage="manage_user_page" class="clickable btn btn-lg btn-primary btn-block textInputField" >Change</button>
						</div>
		        </div>
			</form>
		</div>
		
		<div id="change_email_page" class = "page">
			<form class="form-signin" role="form">	
				<h2 class="form-signin-heading">Change Email</h2>
				<div class = "row">
					
						 
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>Old Email:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input disabled type="text" FiligranaName="loginEmail" FiligranaType="Field" class="form-control password textInputField" placeholder="" required>
			        		
			        	</div>
			        	
			    
		        	
		        </div>
				<div class = "row">
					
						 
			        	<div class= "col-xs-12 col-sm-6">
			        		<div>New Email:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="text" FiligranaName="newEmail" FiligranaType="Field" class="form-control password textInputField" placeholder="New email" required>
			        		
			        	</div>
			        	
			    
		        	
		        </div>
		        <div class = "row">
		       		 <div class= "col-xs-12 col-sm-6">
			        		<div>Confirm with password:</div>
			        	</div>
			        	<div class= "col-xs-12 col-sm-6">
			        		<input type="password" FiligranaName="changeEmailPassword" FiligranaType="Field" class="form-control password textInputField" placeholder="Password" required>
			        		
			        	</div>
			    </div>
		        
		        
		        <div class="row">
		        	<div class = "col-xs-12 col-sm-3">
							<button type='button'  FiligranaName="saveNewUsernameButton" FiligranaType="Field" 
							FiligranaAction="changeEmail" topage="manage_user_page" class="clickable btn btn-lg btn-primary btn-block textInputField" >Change</button>
						</div>
		        </div>
			</form>
		</div>		        
		
		
		
		<div id="manage_watermark_page" class="page centered">
			<div class = "row">
				<div class = "col-xs-6">
					<h1>Watermark Detail</h1>
				</div>
				<div class="col-xs-6">
					<a href="http://www.memoryofpaper.eu:8080/BernsteinPortal/appl_start.disp#" target="_blank">open Bernstein</a>
				</div>
				<!-- 
				<div class = "col-xs-6" FiligranaName="DeleteWatermark" FiligranaType="FieldContainer">
					
				</div> -->
			</div>
			<!-- 
			<div class = "row">
				<div class = "col-xs-3 col-sm-3">
					Name:
				</div>
				<div class = "col-xs-6 col-sm-6">
					<input type="text" disabled FiligranaName="WatermarkName" FiligranaType="Field" class="form-control text wmname textInputField" placeholder="Watermarkname">
				</div>
				<div class = "col-xs-3 col-sm-3" FiligranaName="WatermarkNameEditButtonContainer" FiligranaType="FieldContainer">
					<button type='button' id="editWMName" class="clickable btn btn-lg btn-primary btn-block" 
					FiligranaName="WatermarkNameEditButton" FiligranaType="Field" FiligranaAction="editWMName">Edit</button>
				</div>
				
			</div>- -->
			
			<div class="panel panel-default">
		    <div class="panel-heading" role="tab" id="MetadataHeading">
		      <h4 class="panel-title">
		        <a class= "collapseA" data-toggle="collapse" href="" aria-expanded="true" aria-controls="collapseMetadata"
		        target= "collapseMetadata" >
		          Metadata of the Watermark:
		        </a>
		      </h4>
		    </div>
		    <div id="collapseMetadata" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="MetadataHeading"
		    area-expanded = "true">
		      <div class="panel-body">
		        <div class = "row" FiligranaName = "watermarkMetadataContainer" FiligranaType="FieldContainer">
				
				</div>
		      </div>
		    </div>
		  </div>
			
			
		<div class="panel panel-default">
		    <div class="panel-heading" role="tab" id="ImageHeading">
		      <h4 class="panel-title">
		        <a class= "collapseA" data-toggle="collapse" href="" aria-expanded="true" aria-controls="collapseImage"
		        target= "collapseImage" >
		          Images of the Watermark:
		        </a>
		      </h4>
		    </div>
		    <div id="collapseImage" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="ImageHeading"
		    area-expanded = "true">
		      <div class="panel-body">
		        <div class = "row" FiligranaName = "watermarkImageContainer" FiligranaType="FieldContainer"> 
				
				</div>
		      </div>
		    </div>
		  </div>
	
			
		<div class="panel panel-default">
		    <div class="panel-heading" role="tab" id="SketchHeading">
		      <h4 class="panel-title">
		        <a class= "collapseA" data-toggle="collapse" href="" aria-expanded="true" aria-controls="collapseSketch"
		        target= "collapseSketch" >
		          Sketches of the watermark:
		        </a>
		      </h4>
		    </div>
		    <div id="collapseSketch" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="SketchHeading"
		    area-expanded = "true">
		      <div class="panel-body">
		        <div class = "row" FiligranaName = "watermarkSketchContainer" FiligranaType="FieldContainer"> 
				
				</div>
		      </div>
		    </div>
		  </div>
		
			
		
		
		</div>
		
		<div id="manage_iph_type_page" class="page centered">
			<div class = "row">
				<div class = "col-xs-4">
					<h1>IPH Type Detail</h1>
				</div>
				<div class = "col-xs-6" FiligranaName="DeleteIPHType" FiligranaType="FieldContainer">
					<a href="http://www.memoryofpaper.eu:8080/BernsteinPortal/appl_start.disp#" target="_blank">open Bernstein</a>
				</div>
				<div class = "col-xs-6" FiligranaName="IPHAppendWatermarkContainer" FiligranaType="FieldContainer">
					
				</div>
				
				
			</div>
			<!-- 
			<div class = "row">
				<div class = "col-xs-3 col-sm-3">
					Name:
				</div>
				<div class = "col-xs-6 col-sm-6">
					<input type="text" disabled FiligranaName="IPHName" FiligranaType="Field" class="form-control text iphname textInputField" placeholder="IPHname">
				</div>
				<div class = "col-xs-3 col-sm-3" FiligranaName="IPHNameEditButtonContainer" FiligranaType="FieldContainer">
					<button type='button' id="editIPHName" class="clickable btn btn-lg btn-primary btn-block" 
					FiligranaName="IPHNameEditButton" FiligranaType="Field" FiligranaAction="editIPHName">Edit</button>
				</div>
				
			</div> -->
			
				
			<div class="panel panel-default">
		    <div class="panel-heading" role="tab" id="IPHMetadataHeading">
		      <h4 class="panel-title">
		        <a class= "collapseA" data-toggle="collapse" href="" aria-expanded="true" aria-controls="IPHcollapseMetadata"
		        target= "IPHcollapseMetadata" >
		          Metadata of the IPH-type:
		        </a>
		      </h4>
		    </div>
		    <div id="IPHcollapseMetadata" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="IPHMetadataHeading"
		    area-expanded = "true">
		      <div class="panel-body">
		        <div class = "row" FiligranaName = "IPHMetadataContainer" FiligranaType="FieldContainer">
				
				</div>
		      </div>
		    </div>
		  </div>
			
			
		<div class="panel panel-default">
		    <div class="panel-heading" role="tab" id="IPHImageHeading">
		      <h4 class="panel-title">
		        <a class= "collapseA" data-toggle="collapse" href="" aria-expanded="true" aria-controls="IPHcollapseImage"
		        target= "IPHcollapseImage" >
		          Images of the IPH-type:
		        </a>
		      </h4>
		    </div>
		    <div id="IPHcollapseImage" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="IPHImageHeading"
		    area-expanded = "true">
		      <div class="panel-body">
		        <div class = "row" FiligranaName = "IPHImageContainer" FiligranaType="FieldContainer"> 
				
				</div>
		      </div>
		    </div>
		  </div>
	
			
		<div class="panel panel-default">
		    <div class="panel-heading" role="tab" id="IPHSketchHeading">
		      <h4 class="panel-title">
		        <a class= "collapseA" data-toggle="collapse" href="" aria-expanded="true" aria-controls="IPHcollapseSketch"
		        target= "IPHcollapseSketch" >
		          Sketches of the IPH-type:
		        </a>
		      </h4>
		    </div>
		    <div id="IPHcollapseSketch" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="IPHSketchHeading"
		    area-expanded = "true">
		      <div class="panel-body">
		        <div class = "row" FiligranaName = "IPHSketchContainer" FiligranaType="FieldContainer"> 
				
				</div>
		      </div>
		    </div>
		  </div>
		
		
		</div>
		
		<div id="search_page" class="page centered">
			<h1>Text Search For IPHTypes and Watermarks</h1>
			<div class="row">
				<div class = "col-xs-6">
					<input type="text" FiligranaName="Search" FiligranaAction="Search" FiligranaType="Field" class="form-control text search textInputField" placeholder="Search">
				</div>
				
			</div>
			<div FiligranaName="searchContainer" FiligranaType="FieldContainer" id="textSearchContainer">
			
			</div>
			<div class="row" id="loaderrow">
				<img class="img-responsive fill_screen_div" src="img/app/loader.gif"></img>
			</div>	
					
		</div>
		
		<div id="loader_page" class="page centered">
			<div class="row">
				<img class="img-responsive fill_screen_div" src="img/app/loader.gif"></img>
			</div>	
		</div>
		
		<form id="uploadImageFormMan" class = "img_form hidden" action='src/php/ajax_server.php' method='post' enctype="multipart/form-data">
					<input type='hidden' name='action' value='uploadImageAdd'>
					<input type='hidden' name='index' value='imageAdd'>
					<input id='uploadImageManage' type='file' accept="image/*" name='imageAdd' >
					<input id="typeField" name="type" type='hidden' value=''></input>
					<input id="idField" name="id" type='hidden' value=''></input>
					<input type='hidden' name='is_ajax' value='1'>
		</form>
		
		
		
		
		
		<div class="navmenu navmenu-default navmenu-fixed-left offcanvas offcanvas-clone"></div>
		<object id="wtPlugin" type="application/x-wacomtabletplugin" style="display: none;">
			<!-- <param name="onload" value="pluginLoaded" /> -->
		</object>
	</div>
	<div class="modal fade page centered" id="newUserModal_page" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		        <h4 class="modal-title" id="myModalLabel">Add User</h4>
		      </div>
		      <div class="modal-body">
		      	<div class= "row">
		        	<div class= "col-xs-12 col-sm-6">
		        		<div>Type email:</div>
		        	</div>
		        	<div class= "col-xs-12 col-sm-6">
		        		<input FiligranaName="newUserEmail" FiligranaType="Field" type="email" class="form-control email textInputField" >
		        	</div>      	
	      		 </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        <button type="button clickable" class="btn btn-primary" id="submitAddUser" FiligranaAction="submitAddUser">Add User</button>
		      </div>
		    </div>
		  </div>
		</div>
	
</body>
</html>
