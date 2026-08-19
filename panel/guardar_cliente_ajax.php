<?php
require "proteger.php";
require_once "../includes/conexion.php";

header('Content-Type: application/json');

$nombres   = isset($_POST['nombres']) ? trim($_POST['nombres']) : '';
$apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
$correo    = isset($_POST['correo']) ? trim($_POST['correo']) : '';

if (empty($nombres) || empty($apellidos)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Nombres y apellidos son obligatorios.']);
    exit;
}

$stmt = mysqli_prepare($conexion, "INSERT INTO clientes (nombres, apellidos, correo) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sss", $nombres, $apellidos, $correo);

if (mysqli_stmt_execute($stmt)) {
    $nuevo_id = mysqli_insert_id($conexion);
    echo json_encode(['exito' => true, 'id' => $nuevo_id]);
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar en la base de datos.']);
}