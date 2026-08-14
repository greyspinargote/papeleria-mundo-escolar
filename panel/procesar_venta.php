<?php

require "proteger.php";
require_once "../includes/conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: ventas.php");
    exit;

}

$cliente_id   = !empty($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : null;
$carritoJson  = $_POST['carrito_json'] ?? '[]';
$carrito      = json_decode($carritoJson, true);

if (!is_array($carrito) || count($carrito) === 0) {

    header("Location: ventas.php?error=1");
    exit;

}

/* Verificamos stock disponible de cada producto antes de procesar */

foreach ($carrito as $item) {

    $producto_id = (int)$item['id'];
    $cantidad    = (int)$item['cantidad'];

    $resultado = mysqli_query($conexion, "SELECT stock FROM productos WHERE id = $producto_id");
    $producto  = mysqli_fetch_assoc($resultado);

    if (!$producto || $producto['stock'] < $cantidad) {

        header("Location: ventas.php?error=1");
        exit;

    }

}

/* Calculamos el total */

$total = 0;

foreach ($carrito as $item) {
    $total += (float)$item['precio'] * (int)$item['cantidad'];
}

/* Creamos el pedido (venta en tienda) */

$clienteSQL = $cliente_id !== null ? $cliente_id : "NULL";

mysqli_query($conexion, "INSERT INTO pedidos (cliente_id, fecha, total, estado, origen)
    VALUES ($clienteSQL, NOW(), $total, 'Completado', 'tienda')");

$pedido_id = mysqli_insert_id($conexion);

/* Guardamos cada producto y descontamos el stock */

foreach ($carrito as $item) {

    $producto_id = (int)$item['id'];
    $cantidad    = (int)$item['cantidad'];
    $precio      = (float)$item['precio'];
    $subtotal    = $precio * $cantidad;

    mysqli_query($conexion, "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio, subtotal)
        VALUES ($pedido_id, $producto_id, $cantidad, $precio, $subtotal)");

    mysqli_query($conexion, "UPDATE productos SET stock = stock - $cantidad WHERE id = $producto_id");

    mysqli_query($conexion, "INSERT INTO movimientos_inventario (producto_id, tipo, cantidad, motivo, usuario_id)
        VALUES ($producto_id, 'salida', $cantidad, 'Venta #$pedido_id (mostrador)', " . (int)$_SESSION['usuario_id'] . ")");

}

header("Location: ventas.php?exito=$pedido_id");
exit;