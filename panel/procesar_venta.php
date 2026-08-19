<?php
require "proteger.php";
require_once "../includes/conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ventas.php");
    exit;
}

$cliente_id  = !empty($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : "NULL";
$usuario_id  = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : "NULL";
$carritoJson = isset($_POST['carrito_json']) ? $_POST['carrito_json'] : '';

$carrito = json_decode($carritoJson, true);

if (empty($carrito) || !is_array($carrito)) {
    header("Location: ventas.php?error=1");
    exit;
}

/* Calcular Totales */
$subtotal = 0;
foreach ($carrito as $item) {
    $subtotal += ($item['precio'] * $item['cantidad']);
}

$iva   = $subtotal * 0.15;
$total = $subtotal + $iva;

/* Iniciar Transacción en BD */
mysqli_begin_transaction($conexion);

try {
    /* 1. Insertar venta principal */
    $queryVenta = "INSERT INTO ventas (cliente_id, usuario_id, subtotal, iva, total, fecha) 
                   VALUES ($cliente_id, $usuario_id, $subtotal, $iva, $total, NOW())";
    
    if (!mysqli_query($conexion, $queryVenta)) {
        throw new Exception("Error al insertar la venta.");
    }

    $venta_id = mysqli_insert_id($conexion);

    /* 2. Insertar detalle de venta y actualizar stock */
    foreach ($carrito as $item) {
        $p_id     = (int)$item['id'];
        $cant     = (int)$item['cantidad'];
        $precio   = (float)$item['precio'];
        $itemSub  = $precio * $cant;

        $queryDetalle = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
                         VALUES ($venta_id, $p_id, $cant, $precio, $itemSub)";
        
        if (!mysqli_query($conexion, $queryDetalle)) {
            throw new Exception("Error al insertar el detalle.");
        }

        /* Descontar Stock */
        $queryStock = "UPDATE productos SET stock = stock - $cant WHERE id = $p_id AND stock >= $cant";
        mysqli_query($conexion, $queryStock);

        if (mysqli_affected_rows($conexion) === 0) {
            throw new Exception("Stock insuficiente.");
        }
    }

    /* Confirmar Cambios */
    mysqli_commit($conexion);

    /* Redirigir a ventas.php enviando el ID de éxito */
    header("Location: ventas.php?exito=" . $venta_id);
    exit;

} catch (Exception $e) {
    mysqli_rollback($conexion);
    header("Location: ventas.php?error=1");
    exit;
}