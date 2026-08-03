<?php

session_start();

if (!isset($_GET['id'])) {

    header("Location: carrito.php");
    exit();

}

$id = (int)$_GET['id'];

if (isset($_SESSION['carrito'])) {

    foreach ($_SESSION['carrito'] as $indice => $producto) {

        if ($producto['id'] == $id) {

            unset($_SESSION['carrito'][$indice]);

            break;

        }

    }

    // Reorganizar el arreglo
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);

}

header("Location: carrito.php");
exit();

?>