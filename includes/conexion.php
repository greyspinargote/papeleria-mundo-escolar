<?php

/* INICIAR SESIÓN (una sola vez, la necesita el carrito) */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* CONEXIÓN A LA BASE DE DATOS */

$servidor = "localhost";
$usuario  = "root";
$password = "";
$basedatos = "papeleria_web"; 

$conexion = mysqli_connect($servidor, $usuario, $password, $basedatos);

if (!$conexion) {

    die("Error de conexión: " . mysqli_connect_error());

}

mysqli_set_charset($conexion, "utf8");

?>