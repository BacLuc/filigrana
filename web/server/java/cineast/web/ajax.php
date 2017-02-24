<?php
error_reporting(E_ALL);

$service_port = 12345;
$address = "localhost";
if (!empty($_POST)) {
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket === false) {
		echo "socket_create() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error()) . "\n";
	}
	$result = socket_connect($socket, $address, $service_port);
	if ($result === false) {
		echo "socket_connect() fehlgeschlagen.\nGrund: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
	}

	$in = 'j' . stripslashes($_POST['query']);
	socket_write($socket, $in, strlen($in));
	socket_write($socket, ';', 1);

	while ($out = socket_read($socket, 2048)) {
		echo $out;
	}

	socket_close($socket);
} elseif (!empty($_GET) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket === false) {
		echo "socket_create() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error()) . "\n";
	}
	$result = socket_connect($socket, $address, $service_port);
	if ($result === false) {
		echo "socket_connect() fehlgeschlagen.\nGrund: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
	}

	$in = 'i' . ($_GET['id']);
	if(!empty($_GET['w'])){$in .= '|' . stripcslashes($_GET['w']);}
	socket_write($socket, $in, strlen($in));
	socket_write($socket, ';', 1);

	while ($out = socket_read($socket, 2048)) {
		echo $out;
	}

	socket_close($socket);
} elseif (!empty($_GET) && !empty($_GET['p'])) {
	$p_string = $_GET['p'];
	$p = array();
	$pTemp = explode(",", $p_string);

	foreach ($pTemp as $pos) {
		if (is_numeric($pos)) {
			$p[] = $pos;
		}
	}
	$positive = implode(",", $p);
	$negative = "";
	echo "\n";
	if (!empty($_GET['n'])) {
		$n_string = $_GET['n'];
		$n = array();
		$nTemp = explode(",", $n_string);

		foreach ($nTemp as $pos) {
			if (is_numeric($pos)) {
				$n[] = $pos;
			}
		}
		$negative = implode(",", $n);
	}


	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket === false) {
		echo "socket_create() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error()) . "\n";
	}
	$result = socket_connect($socket, $address, $service_port);
	if ($result === false) {
		echo "socket_connect() fehlgeschlagen.\nGrund: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
	}

	$in = 'r';
	if(!empty($_GET['w'])){$in .= stripcslashes($_GET['w']) . '|';}
	$in .=  $positive . "|" . $negative;
	socket_write($socket, $in, strlen($in));
	socket_write($socket, ';', 1);

	while ($out = socket_read($socket, 2048)) {
		echo $out;
	}

	socket_close($socket);

}
?>