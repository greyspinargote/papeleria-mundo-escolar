<?php

require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

$mensaje = "";
$error   = "";

/* REGISTRAR ENTRADA O SALIDA DE STOCK */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $producto_id = (int)$_POST['producto_id'];
    $tipo        = $_POST['tipo'] === 'salida' ? 'salida' : 'entrada';
    $cantidad    = (int)$_POST['cantidad'];
    $motivo      = trim($_POST['motivo'] ?? '');
    $usuario_id  = (int)$_SESSION['usuario_id'];

    if ($producto_id <= 0 || $cantidad <= 0) {

        $error = "Selecciona un producto y una cantidad válida.";

    } else {

        $resultado = mysqli_query($conexion, "SELECT stock FROM productos WHERE id = $producto_id");
        $producto  = mysqli_fetch_assoc($resultado);

        if (!$producto) {

            $error = "El producto no existe.";

        } elseif ($tipo === 'salida' && $producto['stock'] < $cantidad) {

            $error = "No puedes retirar más stock del que hay disponible (" . $producto['stock'] . ").";

        } else {

            $operador = $tipo === 'entrada' ? '+' : '-';

            mysqli_query($conexion, "UPDATE productos SET stock = stock $operador $cantidad WHERE id = $producto_id");

            $motivoEsc = mysqli_real_escape_string($conexion, $motivo);

            mysqli_query($conexion, "INSERT INTO movimientos_inventario (producto_id, tipo, cantidad, motivo, usuario_id)
                VALUES ($producto_id, '$tipo', $cantidad, '$motivoEsc', $usuario_id)");

            $mensaje = "Movimiento de inventario registrado correctamente.";

        }

    }

}

$productos = mysqli_query($conexion, "SELECT * FROM productos WHERE estado = 1 ORDER BY stock ASC");

$movimientos = mysqli_query($conexion, "
    SELECT m.*, p.nombre AS producto_nombre, u.nombres AS usuario_nombre
    FROM movimientos_inventario m
    LEFT JOIN productos p ON p.id = m.producto_id
    LEFT JOIN usuarios u ON u.id = m.usuario_id
    ORDER BY m.fecha DESC
    LIMIT 20
");

$productosSelect = mysqli_query($conexion, "SELECT id, nombre, stock FROM productos WHERE estado = 1 ORDER BY nombre ASC");

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inventario - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Inventario</h1>

            <div class="usuario-actual">
                <i class="fa-solid fa-circle-user"></i>
                <?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?>
                <span class="badge-rol"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
            </div>

        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mensaje-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="tarjeta-panel">

            <h2>Registrar entrada / salida de stock</h2>

            <form method="POST">

                <div class="campo">
                    <label>Producto</label>
                    <select name="producto_id" required>
                        <option value="">-- Selecciona --</option>
                        <?php while ($p = mysqli_fetch_assoc($productosSelect)) { ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo htmlspecialchars($p['nombre']); ?> (stock actual: <?php echo $p['stock']; ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="campo">
                    <label>Tipo de movimiento</label>
                    <select name="tipo" required>
                        <option value="entrada">Entrada (ingreso de mercadería)</option>
                        <option value="salida">Salida (daño, pérdida, ajuste)</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Cantidad</label>
                    <input type="number" name="cantidad" min="1" required>
                </div>

                <div class="campo">
                    <label>Motivo (opcional)</label>
                    <input type="text" name="motivo" placeholder="Ej: Compra a proveedor, producto dañado...">
                </div>

                <button type="submit" class="btn-panel">
                    <i class="fa-solid fa-right-left"></i> Registrar movimiento
                </button>

            </form>

        </div>

        <div class="tarjeta-panel">

            <h2>Stock actual</h2>

            <table class="tabla-panel">

                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Estado</th>
                </tr>

                <?php while ($p = mysqli_fetch_assoc($productos)) { ?>

                    <tr>
                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td><?php echo moneda($p['precio']); ?></td>
                        <td style="text-align:center; font-weight:700;"><?php echo $p['stock']; ?></td>
                        <td>
                            <?php if ($p['stock'] == 0): ?>
                                <span style="color:#dc3545; font-weight:700;">Agotado</span>
                            <?php elseif ($p['stock'] <= 5): ?>
                                <span style="color:#e67e22; font-weight:700;">Stock bajo</span>
                            <?php else: ?>
                                <span style="color:#28A745; font-weight:700;">Disponible</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php } ?>

            </table>

        </div>

        <div class="tarjeta-panel">

            <h2>Últimos movimientos</h2>

            <table class="tabla-panel">

                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Motivo</th>
                    <th>Registrado por</th>
                </tr>

                <?php if (mysqli_num_rows($movimientos) === 0): ?>

                    <tr>
                        <td colspan="6" style="text-align:center; padding:20px; color:#888;">
                            Todavía no hay movimientos registrados.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php while ($m = mysqli_fetch_assoc($movimientos)) { ?>

                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($m['fecha'])); ?></td>
                            <td><?php echo htmlspecialchars($m['producto_nombre'] ?? 'Producto eliminado'); ?></td>
                            <td>
                                <?php if ($m['tipo'] === 'entrada'): ?>
                                    <span style="color:#28A745; font-weight:600;"><i class="fa-solid fa-arrow-up"></i> Entrada</span>
                                <?php else: ?>
                                    <span style="color:#dc3545; font-weight:600;"><i class="fa-solid fa-arrow-down"></i> Salida</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $m['cantidad']; ?></td>
                            <td><?php echo htmlspecialchars($m['motivo']); ?></td>
                            <td><?php echo htmlspecialchars($m['usuario_nombre'] ?? '-'); ?></td>
                        </tr>

                    <?php } ?>

                <?php endif; ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>