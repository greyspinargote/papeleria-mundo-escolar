<?php

require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$resultado = mysqli_query($conexion, "
    SELECT p.*, c.nombres, c.apellidos, c.correo, c.telefono, c.direccion
    FROM pedidos p
    LEFT JOIN clientes c ON c.id = p.cliente_id
    WHERE p.id = $id
");

$pedido = mysqli_fetch_assoc($resultado);

if (!$pedido) {

    header("Location: historial.php");
    exit;

}

$detalle = mysqli_query($conexion, "
    SELECT d.*, pr.nombre AS producto_nombre, pr.imagen AS producto_imagen
    FROM detalle_pedido d
    LEFT JOIN productos pr ON pr.id = d.producto_id
    WHERE d.pedido_id = $id
");

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pedido #<?php echo $pedido['id']; ?> - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Pedido #<?php echo $pedido['id']; ?></h1>

            <a href="historial.php" class="btn-panel" style="text-decoration:none;">
                <i class="fa-solid fa-arrow-left"></i> Volver al historial
            </a>

        </div>

        <div class="tarjeta-panel">

            <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:20px; margin-bottom:20px;">

                <div>
                    <strong>Cliente:</strong>
                    <?php echo $pedido['nombres'] ? htmlspecialchars($pedido['nombres'] . ' ' . $pedido['apellidos']) : 'Venta de mostrador (sin cliente)'; ?>
                    <?php if ($pedido['correo']): ?>
                        <br><span style="color:#666;"><?php echo htmlspecialchars($pedido['correo']); ?> — <?php echo htmlspecialchars($pedido['telefono']); ?></span>
                    <?php endif; ?>
                    <?php if ($pedido['direccion']): ?>
                        <br><span style="color:#666;"><?php echo htmlspecialchars($pedido['direccion']); ?></span>
                    <?php endif; ?>
                </div>

                <div style="text-align:right;">
                    <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?><br>
                    <strong>Origen:</strong> <?php echo $pedido['origen'] === 'web' ? 'Tienda Web' : 'Mostrador'; ?><br>
                    <strong>Estado:</strong> <span class="badge-rol"><?php echo htmlspecialchars($pedido['estado']); ?></span>
                </div>

            </div>

            <table class="tabla-panel">

                <tr>
                    <th>Foto</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>

                <?php while ($item = mysqli_fetch_assoc($detalle)) { ?>

                    <tr>
                        <td>
                            <?php if (!empty($item['producto_imagen'])): ?>
                                <img src="../assets/img/productos/<?php echo htmlspecialchars($item['producto_imagen']); ?>" alt="">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['producto_nombre'] ?? 'Producto eliminado'); ?></td>
                        <td style="text-align:center;"><?php echo $item['cantidad']; ?></td>
                        <td><?php echo moneda($item['precio']); ?></td>
                        <td><?php echo moneda($item['subtotal']); ?></td>
                    </tr>

                <?php } ?>

            </table>

            <div style="text-align:right; margin-top:20px; font-size:22px; font-weight:700; color:#0A4DA3;">
                Total: <?php echo moneda($pedido['total']); ?>
            </div>

        </div>

    </div>

</div>

</body>
</html>
