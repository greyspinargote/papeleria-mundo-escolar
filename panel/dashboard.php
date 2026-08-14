<?php

require "proteger.php";
require_once "../includes/conexion.php";

/* Totales para las tarjetas de resumen */

$totalProductos = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) t FROM productos WHERE estado = 1"))['t'];
$totalClientes  = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) t FROM clientes"))['t'];
$totalPedidos   = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) t FROM pedidos"))['t'];
$totalVentas    = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COALESCE(SUM(total),0) t FROM pedidos"))['t'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Dashboard</h1>

            <div class="usuario-actual">
                <i class="fa-solid fa-circle-user"></i>
                <?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?>
                <span class="badge-rol"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
            </div>

        </div>

        <?php if (isset($_GET['sin_permiso'])): ?>
            <div class="acceso-denegado">
                <i class="fa-solid fa-triangle-exclamation"></i>
                No tienes permiso para acceder a esa sección. Contacta a un administrador.
            </div>
        <?php endif; ?>

        <div class="tarjetas-resumen">

            <div class="tarjeta-resumen">
                <div class="icono azul"><i class="fa-solid fa-box"></i></div>
                <div>
                    <div class="numero"><?php echo $totalProductos; ?></div>
                    <div class="etiqueta">Productos activos</div>
                </div>
            </div>

            <div class="tarjeta-resumen">
                <div class="icono verde"><i class="fa-solid fa-users"></i></div>
                <div>
                    <div class="numero"><?php echo $totalClientes; ?></div>
                    <div class="etiqueta">Clientes registrados</div>
                </div>
            </div>

            <div class="tarjeta-resumen">
                <div class="icono amarillo"><i class="fa-solid fa-cart-shopping"></i></div>
                <div>
                    <div class="numero"><?php echo $totalPedidos; ?></div>
                    <div class="etiqueta">Pedidos recibidos</div>
                </div>
            </div>

            <div class="tarjeta-resumen">
                <div class="icono rojo"><i class="fa-solid fa-dollar-sign"></i></div>
                <div>
                    <div class="numero">$<?php echo number_format($totalVentas, 2); ?></div>
                    <div class="etiqueta">Total en ventas</div>
                </div>
            </div>

        </div>

        <div class="tarjeta-panel">
            <h2>Bienvenido/a al panel</h2>
            <p>Usa el menú de la izquierda para gestionar productos, clientes, ventas y más.</p>
        </div>

    </div>

</div>

</body>
</html>