<?php
session_start();

require_once "includes/conexion.php";

if (!isset($_GET['id'])) {
    header("Location: productos.php");
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM productos WHERE id = $id";
$resultado = mysqli_query($conexion, $sql);

if (mysqli_num_rows($resultado) == 0) {
    header("Location: productos.php");
    exit();
}

$producto = mysqli_fetch_assoc($resultado);

// Crear carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$encontrado = false;

// Verificar si el producto ya está en el carrito
foreach ($_SESSION['carrito'] as &$item) {

    if ($item['id'] == $producto['id']) {

        $item['cantidad']++;

        $encontrado = true;

        break;

    }

}

// Si no existe, agregarlo
if (!$encontrado) {

    $_SESSION['carrito'][] = [

        'id' => $producto['id'],
        'nombre' => $producto['nombre'],
        'precio' => $producto['precio'],
        'imagen' => $producto['imagen'],
        'cantidad' => 1

    ];

}

header("Location: carrito.php");
exit();

?>