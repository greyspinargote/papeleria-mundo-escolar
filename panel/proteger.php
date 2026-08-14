<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Debe estar logueado */

if (!isset($_SESSION['usuario_id'])) {

    header("Location: login.php");
    exit;

}

/* Páginas exclusivas de administrador */

$paginasSoloAdmin = [
    'usuarios.php',
    'configuracion.php',
    'reportes.php',
    'inventario.php'
];

$paginaActual = basename($_SERVER['PHP_SELF']);

if (in_array($paginaActual, $paginasSoloAdmin) && $_SESSION['usuario_rol'] !== 'admin') {

    header("Location: dashboard.php?sin_permiso=1");
    exit;

}