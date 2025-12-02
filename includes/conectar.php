<?php

//Most of the echo here are just for debug. Remove them if necessary
echo "iniciando";
/* Get the port for the Matlab service. */
$service_port = 1111;

/* Get the IP address for the target host. */
$address = gethostbyname('localhost');
#$address='127.0.0.1';
/* Create a TCP/IP socket. */

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
	show_error("Imposible conectar con servidor de Recomendacion. Motivo: socket_create " . socket_strerror(socket_last_error()) . "\n");
} else {
	echo "OK.\n";
}

echo "Attempting to connect to '$address' on port '$service_port'...";
$result = socket_connect($socket, $address, $service_port);
if ($result === false) {
	show_error("Imposible conectar con servidor de Recomendacion. Motivo: socket_connect" . socket_strerror(socket_last_error()) . "\n");
} else {
	echo "OK.\n";
}
//Put here the user and path to Matlab files
//$user 
//$pathtomat="/home/alumnos/aiXX/matlab\r\n";
//$funcall="recomendar(".$user.")\r\n";

$info=$pathtomat.$funcall.chr(0);
echo $info;

$sent=socket_write($socket, $info, strlen($info));
if ($sent!==FALSE) {
	echo $sent;
}
echo "Reading response:\n\n";
while ($out = @socket_read($socket, 2048,PHP_NORMAL_READ )) {
	echo $out;
}
echo 'Finalizado';
socket_close($socket);

?>
