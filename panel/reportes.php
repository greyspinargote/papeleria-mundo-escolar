<?php

require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

/* FILTROS */

$fechaInicio = isset($_GET['inicio']) && $_GET['inicio'] !== "" ? $_GET['inicio'] : date('Y-m-01');
$fechaFin    = isset($_GET['fin']) && $_GET['fin'] !== "" ? $_GET['fin'] : date('Y-m-d');
$origen      = isset($_GET['origen']) && in_array($_GET['origen'], ['web', 'tienda']) ? $_GET['origen'] : '';

$fechaInicioEsc = mysqli_real_escape_string($conexion, $fechaInicio);
$fechaFinEsc    = mysqli_real_escape_string($conexion, $fechaFin);

$condiciones = "WHERE DATE(p.fecha) BETWEEN '$fechaInicioEsc' AND '$fechaFinEsc'";

if ($origen !== '') {
    $condiciones .= " AND p.origen = '$origen'";
}

/* RESUMEN */

$resumen = mysqli_fetch_assoc(mysqli_query($conexion, "
    SELECT COUNT(*) AS total_pedidos, COALESCE(SUM(p.total),0) AS total_ventas
    FROM pedidos p
    $condiciones
"));

$promedio = $resumen['total_pedidos'] > 0 ? $resumen['total_ventas'] / $resumen['total_pedidos'] : 0;

/* LISTADO DE PEDIDOS */

$pedidos = mysqli_query($conexion, "
    SELECT p.*, c.nombres, c.apellidos
    FROM pedidos p
    LEFT JOIN clientes c ON c.id = p.cliente_id
    $condiciones
    ORDER BY p.fecha DESC
");

/* PRODUCTOS MÁS VENDIDOS EN EL RANGO */

$topProductos = mysqli_query($conexion, "
    SELECT pr.nombre, SUM(d.cantidad) AS total_cantidad, SUM(d.subtotal) AS total_dinero
    FROM detalle_pedido d
    INNER JOIN pedidos p ON p.id = d.pedido_id
    LEFT JOIN productos pr ON pr.id = d.producto_id
    $condiciones
    GROUP BY d.producto_id
    ORDER BY total_cantidad DESC
    LIMIT 10
");

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reportes - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <!-- Encabezado con diseño unificado -->
        <div class="encabezado-panel">

            <div class="header-info">
                <h1>Reportes</h1>
                <p class="subtitulo-panel">Gestión de reportes Mundo Escolar 👋</p>
            </div>

            <div class="usuario-tarjeta">
                <div class="avatar-inicial">
                    <?php 
                        $inicial = !empty($_SESSION['usuario_nombres']) ? strtoupper(substr(trim($_SESSION['usuario_nombres']), 0, 1)) : 'U';
                        echo htmlspecialchars($inicial);
                    ?>
                </div>
                <div class="datos-usuario">
                    <span class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?></span>
                    <span class="rol-usuario"><?php echo htmlspecialchars(ucfirst($_SESSION['usuario_rol'])); ?></span>
                </div>
            </div>

        </div>

        <div class="tarjeta-panel">

            <form method="GET" class="form-filtros-reporte">

                <div class="grupo-filtros">
                    <div class="campo">
                        <label>Desde</label>
                        <input type="date" name="inicio" value="<?php echo htmlspecialchars($fechaInicio); ?>">
                    </div>

                    <div class="campo">
                        <label>Hasta</label>
                        <input type="date" name="fin" value="<?php echo htmlspecialchars($fechaFin); ?>">
                    </div>

                    <div class="campo">
                        <label>Origen</label>
                        <select name="origen">
                            <option value="" <?php echo $origen === '' ? 'selected' : ''; ?>>Todos</option>
                            <option value="web" <?php echo $origen === 'web' ? 'selected' : ''; ?>>Tienda Web</option>
                            <option value="tienda" <?php echo $origen === 'tienda' ? 'selected' : ''; ?>>Mostrador</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-panel">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>

                <div class="acciones-exportar">
                    <a href="exportar_reporte.php?inicio=<?php echo urlencode($fechaInicio); ?>&fin=<?php echo urlencode($fechaFin); ?>&origen=<?php echo urlencode($origen); ?>"
                       class="btn-panel btn-csv">
                        <i class="fa-solid fa-file-csv"></i> Exportar CSV
                    </a>

                    <button type="button" onclick="window.print();" class="btn-panel btn-pdf">
                        <i class="fa-solid fa-file-pdf"></i> Imprimir / PDF
                    </button>
                </div>

            </form>

        </div>

        <div class="tarjetas-resumen">

            <div class="tarjeta-resumen">
                <div class="icono azul"><i class="fa-solid fa-receipt"></i></div>
                <div>
                    <div class="numero"><?php echo $resumen['total_pedidos']; ?></div>
                    <div class="etiqueta">Pedidos en el rango</div>
                </div>
            </div>

            <div class="tarjeta-resumen">
                <div class="icono verde"><i class="fa-solid fa-dollar-sign"></i></div>
                <div>
                    <div class="numero">$<?php echo number_format($resumen['total_ventas'], 2); ?></div>
                    <div class="etiqueta">Total vendido</div>
                </div>
            </div>

            <div class="tarjeta-resumen">
                <div class="icono amarillo"><i class="fa-solid fa-calculator"></i></div>
                <div>
                    <div class="numero">$<?php echo number_format($promedio, 2); ?></div>
                    <div class="etiqueta">Promedio por venta</div>
                </div>
            </div>

        </div>

        <div class="tarjeta-panel">

            <h2>Productos más vendidos en el rango</h2>

            <table class="tabla-panel">

                <tr>
                    <th>Producto</th>
                    <th>Cantidad vendida</th>
                    <th>Total generado</th>
                </tr>

                <?php if (mysqli_num_rows($topProductos) === 0): ?>

                    <tr><td colspan="3" style="text-align:center; padding:20px; color:#888;">Sin datos en este rango.</td></tr>

                <?php else: ?>

                    <?php while ($tp = mysqli_fetch_assoc($topProductos)) { ?>

                        <tr>
                            <td><?php echo htmlspecialchars($tp['nombre'] ?? 'Producto eliminado'); ?></td>
                            <td style="text-align:center;"><?php echo $tp['total_cantidad']; ?></td>
                            <td><?php echo moneda($tp['total_dinero']); ?></td>
                        </tr>

                    <?php } ?>

                <?php endif; ?>

            </table>

        </div>

        <div class="tarjeta-panel">

            <h2>Detalle de pedidos</h2>

            <table class="tabla-panel">

                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Origen</th>
                    <th>Estado</th>
                    <th>Total</th>
                </tr>

                <?php if (mysqli_num_rows($pedidos) === 0): ?>

                    <tr><td colspan="6" style="text-align:center; padding:20px; color:#888;">No hay pedidos en este rango de fechas.</td></tr>

                <?php else: ?>

                    <?php while ($p = mysqli_fetch_assoc($pedidos)) { ?>

                        <tr>
                            <td>#<?php echo $p['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?></td>
                            <td><?php echo $p['nombres'] ? htmlspecialchars($p['nombres'] . ' ' . $p['apellidos']) : 'Mostrador'; ?></td>
                            <td>
                                <?php if ($p['origen'] === 'web'): ?>
                                    <i class="fa-solid fa-globe"></i> Web
                                <?php else: ?>
                                    <i class="fa-solid fa-store"></i> Mostrador
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($p['estado']); ?></td>
                            <td><?php echo moneda($p['total']); ?></td>
                        </tr>

                    <?php } ?>

                <?php endif; ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>