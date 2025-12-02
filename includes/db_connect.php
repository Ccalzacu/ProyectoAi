<?php
$servername = "localhost";
$username = "root"; // Cambiar si es necesario
$password = ""; // Cambiar si es necesario
$dbname = "proyecto_ai"; // Nombre de la base de datos basado en el esquema

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer charset a utf8
$conn->set_charset("utf8");
?>
