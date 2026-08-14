<?php

require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

$cliente_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$resultado = mysqli_query($conexion, "SELECT * FROM clientes WHERE id = $cliente_id");
$cliente   = mysqli_fetch_assoc($resultado);

if (!$cliente) {

    header("Location: clientes.php");
    exit;

}

$pedidos = mysqli_query($conexion, "SELECT * FROM pedidos WHERE cliente_id = $cliente_id ORDER BY fecha DESC");

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pedidos de <?php echo htmlspecialchars($cliente['nombres']); ?> - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Pedidos de <?php echo htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apellidos']); ?></h1>

            <a href="clientes.php" class="btn-panel" style="text-decoration:none;">
                <i class="fa-solid fa-arrow-left"></i> Volver a Clientes
            </a>

        </div>

        <div class="tarjeta-panel">

            <p style="margin-bottom:20px; color:#555;">
                <i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($cliente['correo']); ?>
                &nbsp;&nbsp;
                <i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($cliente['telefono']); ?>
            </p>

            <?php if (mysqli_num_rows($pedidos) === 0): ?>

                <p style="text-align:center; padding:30px; color:#888;">
                    Este cliente todavía no ha realizado pedidos.
                </p>

            <?php else: ?>

                <?php while ($pedido = mysqli_fetch_assoc($pedidos)) { ?>

                    <div style="border:1px solid #eee; border-radius:10px; padding:18px; margin-bottom:18px;">

                        <div style="display:flex; justify-content:space-between; margin-bottom:12px;">

                            <strong>Pedido #<?php echo $pedido['id']; ?></strong>

                            <span><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></span>

                            <span style="font-weight:700; color:var(--verde, #28A745);">
                                <?php echo moneda($pedido['total']); ?>
                            </span>

                            <span class="badge-rol"><?php echo htmlspecialchars($pedido['estado']); ?></span>

                        </div>

                        <?php

                        $detalle = mysqli_query($conexion, "
                            SELECT d.*, pr.nombre AS producto_nombre, pr.imagen AS producto_imagen
                            FROM detalle_pedido d
                            LEFT JOIN productos pr ON pr.id = d.producto_id
                            WHERE d.pedido_id = " . $pedido['id']
                        );

                        ?>

                        <table class="tabla-panel">

                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>

                            <?php while ($item = mysqli_fetch_assoc($detalle)) { ?>

                                <tr>
                                    <td><?php echo htmlspecialchars($item['producto_nombre'] ?? 'Producto eliminado'); ?></td>
                                    <td style="text-align:center;"><?php echo $item['cantidad']; ?></td>
                                    <td><?php echo moneda($item['precio']); ?></td>
                                    <td><?php echo moneda($item['subtotal']); ?></td>
                                </tr>

                            <?php } ?>

                        </table>

                    </div>

                <?php } ?>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>