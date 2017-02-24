<?php

function endsWith($haystack, $needle) {
	return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

session_start();
session_regenerate_id(TRUE);
if (isset($_GET['v']) && isset($_GET['t'])) {
	if (is_numeric($_GET['t'])) {
		$_SESSION['video'] = $_GET['v'];
		$_SESSION['time'] = $_GET['t'];
		$file = '../eval/' . session_id() . '.txt';
		$query = 'Video: ' . $_GET['v'] . "\r\nTime: " . $_GET['t'] . "\r\n\r\n";
		file_put_contents($file, $query, FILE_APPEND | LOCK_EX);
		header('Location: eval_ui.php');
		die();
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Cineast evaluation Step 1</title>

		<!-- Bootstrap -->
		<link href="css/bootstrap.min.css" rel="stylesheet">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script src="js/jquery-1.10.2.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="js/bootstrap.min.js"></script>
		<script src="video-js/video.js" type="text/javascript"></script>
		<link href="video-js/video-js.css" rel="stylesheet" />
		<script>
			videojs.options.flash.swf = "video-js/video-js.swf";
		</script>
		<script>
			function formatTime(seconds) {
				var min = Math.floor(seconds / 60);
				seconds -= (min * 60);
				var sec = Math.floor(seconds);
				return min + ':' + (sec < 10 ? "0" + sec : sec);
			}

			function playVideo(path, name) {
				var player = videojs('videoPlayer');
				player.src(path);
				player.on('loadeddata', function() {
					//alert("pause");
					//videojs('videoPlayer').currentTime(shotStartTime);
					videojs('videoPlayer').play();
				});
				$('#videotitle').html(name);
				$('#videoname').val(name);
			}

			$(function() {
				var player = videojs('videoPlayer');
				player.on("timeupdate", function() {
					var time = videojs('videoPlayer').currentTime();
					$('#videotime').html(formatTime(time));
					$('#videoseconds').val(time);

				}, false);
			});
		</script>
		<style>
			a {
				cursor: pointer;
				cursor: hand;
			}
			#list {
				height: 600px;
				overflow-y: scroll;
			}
			body {
				overflow-x: hidden;
			}
		</style>
	</head>
	<body>
		<div class="alert alert-info clearfix">
			<form class="form-inline">
				<strong>Video : <span id="videotitle">none</span>, Time: <span id="videotime">none</span>
				<input name="t" value="0" type="hidden" id="videoseconds" />
				<input name="v" value="none" type="hidden" id="videoname" />
				<div class="pull-right">
					<input value="Select sequence and continue" type="submit" class="btn btn-info"/>
				</div> </strong>
			</form>
		</div>
		<div class="row container-fluid">
			<div class="col-lg-7 col-md-12">
				<video id="videoPlayer" class="video-js vjs-default-skin center-block" controls preload="auto" width="1000" height="600" data-setup="{}"></video>
			</div>
			<div class="col-lg-5 col-md-12">
				<div id="list">
					<ul>
						<?php

						$directory = '../video/';
						$files = scandir($directory);
						foreach ($files as $file) {
							if (strlen($file) > 2) {
								$videoName = mb_convert_encoding($file, "utf-8", "ISO-8859-1");
								$videoPath = $directory . $file . '/';
								$localfiles = scandir($directory . $file);
								foreach ($localfiles as $localfile) {
									if (endsWith($localfile, ".mp4")) {
										$videoPath .= $localfile;
									}
								}

								echo '<li><a onClick="playVideo(\'';
								echo rawurlencode($videoPath) . '\', \'' . $videoName;
								echo '\')"; >';
								echo $videoName;
								echo '</a></li>';
							}

						}
						?>
					</ul>
				</div>
			</div>
		</div>

	</body>
</html>
