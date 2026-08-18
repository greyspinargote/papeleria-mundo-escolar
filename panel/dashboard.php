<?php

require "proteger.php";
require_once "../includes/conexion.php";

/* Totales para las tarjetas de resumen */

$totalProductos = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) t FROM productos WHERE estado = 1"))['t'];
$totalClientes  = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) t FROM clientes"))['t'];
$totalPedidos   = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) t FROM pedidos"))['t'];
$totalVentas    = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COALESCE(SUM(total),0) t FROM pedidos"))['t'];

/* Últimas 5 ventas */

$ultimasVentas = mysqli_query(
    $conexion,
    "SELECT id, fecha, total, estado
     FROM pedidos
     ORDER BY fecha DESC
     LIMIT 5"
);

/* Últimos 5 productos activos */

$productosRecientes = mysqli_query(
    $conexion,
    "SELECT id, nombre, precio, stock
     FROM productos
     WHERE estado = 1
     ORDER BY id DESC
     LIMIT 5"
);

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

            <div>
                <h1>Panel principal</h1>
                <p class="subtitulo-panel">
                    Bienvenido al sistema Mundo Escolar 👋
                </p>
            </div>

            <div class="usuario-actual">
                <div class="avatar-usuario">
                    <?php echo strtoupper(substr($_SESSION['usuario_nombres'], 0, 1)); ?>
                </div>
                <div class="datos-usuario">
                    <div class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?></div>
                    <div class="rol-usuario"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></div>
                </div>
            </div>

        </div>

        <?php if (isset($_GET['sin_permiso'])): ?>
            <div class="acceso-denegado">
                <i class="fa-solid fa-triangle-exclamation"></i>
                No tienes permiso para acceder a esa sección. Contacta a un administrador.
            </div>
        <?php endif; ?>

        <!-- TARJETAS RESUMEN -->

        <div class="tarjetas-resumen">

            <div class="tarjeta-resumen">
                <div class="blob-decorativo"></div>
                <div class="icono-emoji azul">📦</div>
                <div class="numero"><?php echo $totalProductos; ?></div>
                <div class="etiqueta">Productos registrados</div>
                <div class="detalle-tarjeta">Total de productos disponibles</div>
            </div>

            <div class="tarjeta-resumen">
                <div class="blob-decorativo"></div>
                <div class="icono-emoji verde">👥</div>
                <div class="numero"><?php echo $totalClientes; ?></div>
                <div class="etiqueta">Clientes registrados</div>
                <div class="detalle-tarjeta">Clientes guardados en el sistema</div>
            </div>

            <div class="tarjeta-resumen">
                <div class="blob-decorativo"></div>
                <div class="icono-emoji amarillo">🧾</div>
                <div class="numero"><?php echo $totalPedidos; ?></div>
                <div class="etiqueta">Ventas realizadas</div>
                <div class="detalle-tarjeta">Ventas registradas correctamente</div>
            </div>

            <div class="tarjeta-resumen">
                <div class="blob-decorativo"></div>
                <div class="icono-emoji rojo">💰</div>
                <div class="numero">$<?php echo number_format($totalVentas, 2); ?></div>
                <div class="etiqueta">Total vendido</div>
                <div class="detalle-tarjeta">Ingresos acumulados</div>
            </div>

        </div>

        <!-- FILA: ULTIMAS VENTAS + SISTEMA ACTIVO / ACCESOS RAPIDOS -->

        <div class="panel-grid">

            <!-- ULTIMAS VENTAS -->

            <div class="tarjeta-panel sin-margen">

                <div class="tarjeta-panel-encabezado">
                    <h2>Últimas ventas</h2>
                    <span class="etiqueta-mini">Recientes</span>
                </div>

                <table class="tabla-panel">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($ultimasVentas) === 0): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; color:#777;">
                                    Todavía no hay ventas registradas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php while ($venta = mysqli_fetch_assoc($ultimasVentas)): ?>
                                <tr>
                                    <td>#<?php echo $venta['id']; ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($venta['fecha'])); ?></td>
                                    <td>$<?php echo number_format($venta['total'], 2); ?></td>
                                    <td>
                                        <span class="badge-estado <?php echo strtolower($venta['estado']) === 'completada' ? 'completada' : 'pendiente'; ?>">
                                            <?php echo htmlspecialchars(ucfirst($venta['estado'])); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>

            <!-- COLUMNA DERECHA -->

            <div class="panel-columna-lateral">

                <div class="sistema-activo">
                    <h3><i class="fa-solid fa-circle-check"></i> Sistema activo</h3>
                    <p>Desde este panel puedes controlar productos, clientes, ventas y reportes de Papelería Mundo Escolar.</p>
                </div>

                <div class="tarjeta-panel sin-margen">

                    <h2 style="font-size:18px; margin-bottom:16px;">Accesos rápidos</h2>

                    <div class="accesos-rapidos">

                        <a href="productos.php" class="acceso-rapido">
                            Registrar producto
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                        <a href="clientes.php" class="acceso-rapido">
                            Agregar cliente
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                        <a href="ventas.php" class="acceso-rapido">
                            Nueva venta
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                        <a href="reportes.php" class="acceso-rapido">
                            Ver reportes
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                </div>

            </div>

        </div>

        <!-- PRODUCTOS RECIENTES -->

        <div class="tarjeta-panel">

            <div class="tarjeta-panel-encabezado">
                <h2>Productos recientes</h2>
                <a href="inventario.php" class="etiqueta-mini enlace">Inventario</a>
            </div>

            <table class="tabla-panel">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($productosRecientes) === 0): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; color:#777;">
                                Todavía no hay productos registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while ($producto = mysqli_fetch_assoc($productosRecientes)): ?>
                            <tr>
                                <td>#<?php echo $producto['id']; ?></td>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                <td>
                                    <?php if ($producto['stock'] == 0): ?>
                                        <span class="badge-estado pendiente">Sin stock</span>
                                    <?php else: ?>
                                        <?php echo $producto['stock']; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>

    </div>

</div>

</body>
</html>