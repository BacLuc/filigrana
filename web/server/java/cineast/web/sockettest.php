<!DOCTYPE html>
<html>
<head>
<title>Cineast API v0.001 test</title>
</head>
<body>
<pre>
<?php
error_reporting(E_ALL);
if(!empty($_POST)){

	$service_port = 12345;
	$address = "localhost";

	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket === false) {
		echo "socket_create() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error()) . "\n";
	}
	$result = socket_connect($socket, $address, $service_port);
	if ($result === false) {
		echo "socket_connect() fehlgeschlagen.\nGrund: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
	}

	$in = stripslashes ($_POST['json']);
	
	socket_write($socket, $in, strlen($in));
	socket_write($socket, ';', 1); //closes the request

	while ($out = socket_read($socket, 2048)) {
		echo $out;
	}

	socket_close($socket);
}
?>
</pre><br><br>

<form action="sockettest.php" method="post">
<textarea name="json" cols="50" rows="10"></textarea>
<input name="submit" type="submit" value=" Absenden ">
</form>

</body>

</html> 