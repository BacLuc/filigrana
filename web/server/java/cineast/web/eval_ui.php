<?php
function formatTime($seconds){
	$min = floor($seconds / 60);
	$seconds -= ($min * 60);
	$sec = floor($seconds);
	return $min . ':' . ($sec < 10 ? "0" + $sec : $sec);
	}

session_start();
if(!isset($_SESSION['video'])){
	header('Location: eval_start.php');
	die();
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Cineast evaluation Step 1</title>

		<script src="js/jquery-1.10.2.js"></script>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<script type="text/javascript" src="js/colorpicker.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/processing-1.4.1.js" type="text/javascript"></script>
		<script src="js/bootstrap-colorpicker.js" type="text/javascript"></script>
		<link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
		<script src="js/util.js"></script>
		<script src="js/bootstrap-slider.js" type="text/javascript"></script>
		<link href="css/slider.css" rel="stylesheet" />
		<script src="video-js/video.js" type="text/javascript"></script>
		<link href="video-js/video-js.css" rel="stylesheet" />
		<script>
			videojs.options.flash.swf = "video-js/video-js.swf";
		</script>
		<script>
			var good = [];
			var bad = [];
			var shotStartTime = 0;
			var videoRoot = "../video/";
			$(function() {
				$.ajaxSetup({
					error : function(jqXHR, exception) {
						if (jqXHR.status === 0) {
							printError('No connection.\n Verify Network.');
						} else if (jqXHR.status == 404) {
							printError('Requested page not found. [404]');
						} else if (jqXHR.status == 500) {
							printError('Internal Server Error [500].');
						} else if (exception === 'parsererror') {
							printError('Requested JSON parse failed. This is probably due to a server timeout');
						} else if (exception === 'timeout') {
							printError('Time out error.');
						} else if (exception === 'abort') {
							printError('Ajax request aborted.');
						} else {
							printError('Uncaught Error.\n' + jqXHR.responseText);
						}
					}
				});
			});
			function updateButton() {
				var button = $('#submitSelected');
				if (good.length > 0) {
					button.show();
				} else {
					button.hide();
				}
			}

			function resetButton() {
				good = [];
				bad = [];
				$('#submitSelected').hide();
			}

			function printError(message){
				var loadingHTML = '<div class="alert alert-danger"><strong>Error: </strong>' + message + '<br />Please reload the Page.</div>';
						$("#result").html(loadingHTML);
			}

			function buildTable(data) {
				resetButton();
				var resultHTML = "<table class=\"table table-hover table-bordered table-condensed\"><thead><tr><td>Select</td><td>Preview</td><td>Match</td><td>Video</td><td>Start</td><td>End</td></tr></thead><tbody>";
				if(data.length == 0){
					var loadingHTML = '<div class="alert alert-info"><strong>There are no results for this query</strong></div>';
					$("#result").html(loadingHTML);
					return;
				}else{
					$.each(data, function(key, val) {
					resultHTML += resultTableRow(val);
				});
				}
				
				resultHTML += "</tbody></table>";
				$("#result").html(resultHTML);
				$('#submit').removeClass('btn-warning').addClass('btn-success');
				$('.glyphicon-search').click(function(event) {
					var row = $(event.target).parent().parent();
					var id = row.find('img').attr('id');
					lookUpId(id);
				});
				$('.shotimg').click(function(event) {
					var id = event.target.id;
					var path = videoRoot + $(event.target).attr('data-videopath');
					shotStartTime = $(event.target).attr('data-starttime');
					var name = $(event.target).parent().parent().find('.videoname').html();
					$('#videoOverlayLabel').html(name);
					var player = videojs('videoPlayer');
					player.src(path);
					player.on('loadeddata', function() {
						//alert("pause");
						videojs('videoPlayer').currentTime(shotStartTime);
						videojs('videoPlayer').play();
					});

					$('#videoOverlay').modal('show');
				});
				$('.glyphicon-plus').click(function(event) {
					var row = $(event.target).parent().parent();
					if (!row.hasClass('success')) {
						row.addClass('success');
						row.removeClass('danger');
						var id = row.find('img').attr('id');
						removeFromArray(bad, id);
						good.push(id);
						updateButton();
					}

				});
				$('.glyphicon-minus').click(function(event) {
					var row = $(event.target).parent().parent();
					if (!row.hasClass('danger')) {
						row.addClass('danger');
						row.removeClass('success');
						var id = row.find('img').attr('id');
						removeFromArray(good, id);
						bad.push(id);
						updateButton();
					}

				});
				$('.glyphicon-remove').click(function(event) {
					var row = $(event.target).parent().parent();

					row.removeClass('success');
					row.removeClass('danger');
					var id = row.find('img').attr('id');
					removeFromArray(good, id);
					removeFromArray(bad, id);
					updateButton();
				});
			}

			function lookUpId(id) {
				resetButton();
				var loadingHTML = '<img src="images/loading.gif" alt="Searching, please wait..." class="img-responsive center-block" />';
				$("#result").html(loadingHTML);
				$('#submit').removeClass('btn-primary').removeClass('btn-success').addClass('btn-warning');

				$.ajax({
					type : "GET",
					url : "eval_ajax.php?id=" + id + "&w=" + getWeights(),
					dataType : "json",
					timeout : 25000
				}).done(function(data) {
					buildTable(data);
				});

			}

			function getWeights(){
				var json = "{ \"global\":" + $('#globalColorSlider').data('slider').getValue() + ",\n";
				json += "\"local\":" + $('#localColorSlider').data('slider').getValue() + ",\n";
				json += "\"edge\":" + $('#edgeSlider').data('slider').getValue() + ",\n";
				json += "\"text\":" + $('#textSlider').data('slider').getValue() + ",\n";
				json += "\"motion\":" + $('#motionSlider').data('slider').getValue() + ",\n";
				json += "\"complex\":" + $('#complexSlider').data('slider').getValue() + "}";
				return json;
			}

			$(function() {
				$('#toggleMotion').button().click(function() {
					if ($(this).hasClass('active')) {
						$('#motion').hide();
					} else {
						$('#motion').show();
					}
				});
				$('#motion').hide();
			});
			$(function() {
				$('#clearMotion').button().click(function() {
					Processing.getInstanceById('motion').clearPaths();
				});
			});
			$(function() {
				$('#fill').button().click(function() {
					Processing.getInstanceById('draw').fillCanvas();
				});
			});
			$(function() {
				$('#drawColor').colorpicker();
			});
			$(function() {
				$('#slider').slider({
					min : 0,
					max : 1,
					step : 0.001,
					value : 0.5,
					tooltip : 'hide'
				});
			});
			$(function() {
				$('#penSize').slider({
					min : 1,
					max : 50,
					value : 10,
					tooltip : 'hide'
				}).on('slide', function(ev) {
					Processing.getInstanceById('draw').setStrokeWidth(ev.value);
				});
			});
			$(function() {
				$("#submit").button().click(function() {
					resetButton();
					var loadingHTML = '<img src="images/loading.gif" alt="Searching, please wait..." class="img-responsive center-block" />';
					$("#result").html(loadingHTML);
					$('#submit').removeClass('btn-primary').removeClass('btn-success').addClass('btn-warning');
					var text = $('#text').val();
					var canvas = $('#draw')[0];
					var slider = $('#slider').data('slider').getValue();
					var motion = Processing.getInstanceById('motion').getPaths();

					var requestString;

					try {
						requestString = "{\"img\": \"" + canvas.toDataURL() + "\",\n";
						requestString += "\"motion\":" + motion + ",\n";
						requestString += "\"start\": " + slider + ",\n";
						requestString += "\"end\": " + slider + ",\n";
						requestString += "\"subelements\": [\"" + text + "\"], \n";
						requestString += "\"weights\": " + getWeights() + "}";
					} catch(err) {
						printError(err.message);
						return;
					}

					$.ajax({
						type : "POST",
						url : "eval_ajax.php",
						data : {
							query : requestString
						},
						dataType : "json"
					}).done(function(data) {

						buildTable(data);

					});

				});
			});
			$(function() {
				var holder = $('#drawArea')[0];

				holder.ondragover = function() {
					return false;
				};
				holder.ondragend = function() {
					return false;
				};
				holder.ondrop = function(e) {
					e.preventDefault();

					var file = e.dataTransfer.files[0];
					if ( typeof file === "undefined") {
						Processing.getInstanceById('draw').setImage(e.dataTransfer.getData("URL"));
					} else {
						var reader = new FileReader();
						reader.onload = function(event) {
							Processing.getInstanceById('draw').setImage(event.target.result);
						};
						reader.readAsDataURL(file);
					}
					return false;
				};
			});
			$(function() {
				$('#submitSelected').click(function() {
					$('#submit').removeClass('btn-primary').removeClass('btn-success').addClass('btn-warning');
					var loadingHTML = "";
					if (good.length + bad.length > 3) {
						loadingHTML += "<div class=\"alert alert-warning\"><strong>Warning!</strong> Selecting many elements will lead to a long search time</div></br>";
					}
					loadingHTML += '<img src="images/loading.gif" alt="Searching, please wait..." class="img-responsive center-block" />';
					$("#result").html(loadingHTML);
					$.ajax({
						type : "GET",
						url : "eval_ajax.php?p=" + good.toString() + "&n=" + bad.toString() + "&w=" + getWeights(),
						dataType : "json",
						timeout : 25000
					}).done(function(data) {
						buildTable(data);
					});
					resetButton();
				});
			});
			$(function() {
				$('#videoOverlay').modal({
					show : false
				});
				$('#videoOverlay').on('hide.bs.modal', function() {
					videojs('videoPlayer').pause();
				});
			});
			$(function() {
				$('#restartShotPlayback').button().click(function() {
					var player = videojs('videoPlayer');
					player.pause();
					player.currentTime(shotStartTime);
					player.play();
				});
			});
			$(function(){
				$('#globalColorSlider').slider({
					min : 0,
					max : 10,
					step : 0.1,
					value : 2,
					tooltip: 'hide'
				});
				$('#localColorSlider').slider({
					min : 0,
					max : 10,
					step : 0.1,
					value : 4,
					tooltip: 'hide'
				});
				$('#edgeSlider').slider({
					min : 0,
					max : 10,
					step : 0.1,
					value : 3,
					tooltip: 'hide'
				});
				$('#textSlider').slider({
					min : 0,
					max : 10,
					step : 0.1,
					value : 5,
					tooltip: 'hide'
				});
				$('#motionSlider').slider({
					min : 0,
					max : 10,
					step : 0.1,
					value : 0.2,
					tooltip: 'hide'
				});
				$('#complexSlider').slider({
					min : 0,
					max : 10,
					step : 0.1,
					value : 0,
					tooltip: 'hide'
				});
				$('#toggleSettings').button().click(function() {
					if ($(this).hasClass('active')) {
						$('#settings').hide();
					} else {
						$('#settings').show();
					}
				});
				$('#settings').hide();
			});
		</script>
		<style>
			body {
				overflow-x: hidden;
			}
			#result {
				height: 600px;
				overflow-y: scroll;
			}
			#slider {
				width: 500px;
			}
			#text {
				width: 500px;
				height: 100px;
				resize: none;
			}
			#drawColor {
				width: 80px;
			}
			#penSize {
				width: 275px;
			}
			.glyphicon {
				cursor: pointer;
				cursor: hand;
			}
			.shotimg {
				cursor: pointer;
				cursor: hand;
			}
			#videoOverlay {
				min-width: 510px;
			}
		</style>

	</head>
	<body>
		<div class="alert alert-info clearfix"><strong><?php
		 echo 'Target video: ';
		 echo $_SESSION['video'];
		 echo ' at ';
		 echo formatTime($_SESSION['time']);
		 ?> </strong>
		 <div class="pull-right">
		 <a href="eval_help.php" class="btn btn-info btn-lg" role="button">Help</a>
		 <a href="eval_done.php" class="btn btn-info btn-lg" role="button">Done</a>
		 </div>
		 &nbsp;
		 </div>
		<div id="videoOverlay" class="modal fade bs-modal-lg" tabindex="-1" role="dialog" aria-labelledby="videoOverlayLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title" id="videoOverlayLabel">Video</h4>
					</div>
					<div class="modal-body" id="videoContainer">
						<video id="videoPlayer" class="video-js vjs-default-skin center-block" controls preload="auto" width="500" height="300" data-setup="{}"></video>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="restartShotPlayback">
							Replay Shot
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="row container-fluid">
			<div class="col-lg-5 col-md-12">
				<div style="position:relative; width:502px; height:302px; border: 1px solid black;" id="drawArea">
					<canvas id="draw" data-processing-sources="pde/draw/draw.pde" width="500" height="300" style="z-index: 1;	position:absolute;	left:0px; top:0px;"></canvas>
					<canvas id="motion" data-processing-sources="pde/motion/motion.pde" width="500" height="300" style="z-index: 2;	position:absolute;	left:0px; top:0px;"></canvas>
				</div>

				<div id="slider"></div>
				<br />

				<button id="toggleMotion" data-toggle="button" class="btn">
					Draw Motion
				</button>
				<button id="clearMotion" class="btn">
					Clear Motion
				</button>
				<button id="fill" class="btn">
					Fill Canvas
				</button>
				<br />
				<br />
				Pencolor:
				<input type="text" id="drawColor" value="#555555" readonly=""/>
				Pensize:
				<div id="penSize"> </div>
				<br />
				Text:
				<br />
				<textarea id="text"> </textarea>
				<br />
				<br/>
				<br/>
				<input id="submit" type="submit" value=" Search " class="btn btn-primary">
				<input id="submitSelected" type="submit" value=" Search Selected" class="btn btn-primary" style="display: none;"><br/><br />
				<button id="toggleSettings" data-toggle="button" class="btn">
					Show Settings
				</button>
				<br />
				<table id="settings">
					<tr><td>global colors: </td><td><div id="globalColorSlider"> </div></td></tr>
					<tr><td>local colors: </td><td><div id="localColorSlider"> </div></td></tr>
					<tr><td>edges: </td><td><div id="edgeSlider"> </div></td></tr>
					<tr><td>text: </td><td><div id="textSlider"> </div></td></tr>
					<tr><td>motion: </td><td><div id="motionSlider"> </div></td></tr>
					<tr><td>complex: </td><td><div id="complexSlider"> </div></td></tr>
				</table>
			</div>
			<div class="col-lg-7 col-md-12">
				<div id="result"> </div>
			</div>
		</div>

		<object id="wtPlugin" type="application/x-wacomtabletplugin">
			<!-- <param name="onload" value="pluginLoaded" /> -->
		</object>
	</body>
</html>