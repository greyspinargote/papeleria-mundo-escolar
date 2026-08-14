<?php

require_once "includes/conexion.php";

/* Si no vino por POST o el carrito está vacío, no seguimos */

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['carrito'])) {

    header("Location: carrito.php");
    exit;

}

$nombres   = trim($_POST['nombres'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$correo    = trim($_POST['correo'] ?? '');
$telefono  = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$pago      = trim($_POST['pago'] ?? 'Efectivo');

if ($nombres === "" || $apellidos === "" || $correo === "" || $telefono === "" || $direccion === "") {

    header("Location: finalizar_compra.php?error=1");
    exit;

}

$nombresEsc   = mysqli_real_escape_string($conexion, $nombres);
$apellidosEsc = mysqli_real_escape_string($conexion, $apellidos);
$correoEsc    = mysqli_real_escape_string($conexion, $correo);
$telefonoEsc  = mysqli_real_escape_string($conexion, $telefono);
$direccionEsc = mysqli_real_escape_string($conexion, $direccion);

/* 1) IDENTIFICAR AL CLIENTE (si inició sesión, se usa esa cuenta) */

if (isset($_SESSION['cliente_id'])) {

    $cliente_id = (int)$_SESSION['cliente_id'];

    // Mantenemos sus datos actualizados por si los cambió en el formulario
    mysqli_query($conexion, "UPDATE clientes SET
        nombres = '$nombresEsc',
        apellidos = '$apellidosEsc',
        telefono = '$telefonoEsc',
        direccion = '$direccionEsc'
        WHERE id = $cliente_id");

} else {

    // Cliente sin cuenta: buscamos por correo, o lo creamos
    $buscar = mysqli_query($conexion, "SELECT id FROM clientes WHERE correo = '$correoEsc'");

    if ($fila = mysqli_fetch_assoc($buscar)) {

        $cliente_id = (int)$fila['id'];

    } else {

        mysqli_query($conexion, "INSERT INTO clientes (nombres, apellidos, telefono, correo, direccion)
            VALUES ('$nombresEsc', '$apellidosEsc', '$telefonoEsc', '$correoEsc', '$direccionEsc')");

        $cliente_id = mysqli_insert_id($conexion);

    }

}

/* 2) CALCULAR TOTAL DEL CARRITO */

$total = 0;

foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

/* 3) CREAR EL PEDIDO */

mysqli_query($conexion, "INSERT INTO pedidos (cliente_id, fecha, total, estado)
    VALUES ($cliente_id, NOW(), $total, 'Pendiente')");

$pedido_id = mysqli_insert_id($conexion);

/* 4) GUARDAR CADA PRODUCTO DEL CARRITO EN detalle_pedido */

foreach ($_SESSION['carrito'] as $item) {

    $producto_id = (int)$item['id'];
    $cantidad    = (int)$item['cantidad'];
    $precio      = (float)$item['precio'];
    $subtotal    = $precio * $cantidad;

    mysqli_query($conexion, "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio, subtotal)
        VALUES ($pedido_id, $producto_id, $cantidad, $precio, $subtotal)");

}

/* 5) VACIAR EL CARRITO Y CONFIRMAR */

unset($_SESSION['carrito']);

header("Location: pedido_confirmado.php?id=$pedido_id");
exit;