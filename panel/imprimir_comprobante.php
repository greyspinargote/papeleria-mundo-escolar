<?php
require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

$id_venta = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_venta <= 0) {
    die("ID de venta no válido.");
}

/* 1. Consultar únicamente la venta principal */
$queryVenta = "SELECT * FROM ventas WHERE id = $id_venta LIMIT 1";
$resVenta   = mysqli_query($conexion, $queryVenta);

if (!$resVenta) {
    die("Error en la consulta de venta: " . mysqli_error($conexion));
}

if (mysqli_num_rows($resVenta) === 0) {
    die("La venta #{$id_venta} no existe en la base de datos.");
}

$venta = mysqli_fetch_assoc($resVenta);

/* 2. Obtener nombre del cliente si existe cliente_id */
$clienteNombre = 'Consumidor Final';
if (!empty($venta['cliente_id'])) {
    $c_id = (int)$venta['cliente_id'];
    $resCliente = mysqli_query($conexion, "SELECT nombres, apellidos FROM clientes WHERE id = $c_id LIMIT 1");
    if ($resCliente && $rowC = mysqli_fetch_assoc($resCliente)) {
        $clienteNombre = $rowC['nombres'] . ' ' . $rowC['apellidos'];
    }
}

/* 3. Obtener nombre del usuario/vendedor si existe usuario_id */
$usuarioNombre = 'Sistema';
if (!empty($venta['usuario_id'])) {
    $u_id = (int)$venta['usuario_id'];
    $resUsuario = mysqli_query($conexion, "SELECT nombres FROM usuarios WHERE id = $u_id LIMIT 1");
    if ($resUsuario && $rowU = mysqli_fetch_assoc($resUsuario)) {
        $usuarioNombre = $rowU['nombres'];
    }
}

/* 4. Consultar los detalles de los productos */
$queryDetalle = "SELECT dv.*, p.nombre AS producto_nombre 
                 FROM detalle_ventas dv
                 LEFT JOIN productos p ON dv.producto_id = p.id
                 WHERE dv.venta_id = $id_venta";

$resDetalle = mysqli_query($conexion, $queryDetalle);
if (!$resDetalle) {
    die("Error en la consulta de detalles: " . mysqli_error($conexion));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Comprobante de Venta #<?php echo $venta['id']; ?></title>
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 13px;
        color: #333;
        margin: 0;
        padding: 20px;
        background-color: #f8f9fa;
    }

    .comprobante-card {
        max-width: 550px;
        margin: 0 auto;
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border: 1px solid #e0e0e0;
    }

    .encabezado-ticket {
        text-align: center;
        border-bottom: 2px solid #107c41;
        padding-bottom: 12px;
        margin-bottom: 15px;
    }

    .encabezado-ticket h2 {
        margin: 0;
        color: #107c41;
        font-size: 1.4rem;
    }

    .encabezado-ticket p {
        margin: 3px 0;
        color: #555;
        font-size: 0.85rem;
    }

    .info-empresa {
        font-family: monospace, sans-serif;
        font-size: 0.88rem;
        line-height: 1.3;
        margin: 5px 0 8px 0;
    }

    .info-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }

    .tabla-comprobante {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .tabla-comprobante th {
        background-color: #f1f3f5;
        border-bottom: 2px solid #dee2e6;
        padding: 8px;
        text-align: left;
        font-size: 0.85rem;
    }

    .tabla-comprobante td {
        padding: 8px;
        border-bottom: 1px solid #eee;
    }

    .totales-box {
        width: 200px;
        margin-left: auto;
        font-size: 0.9rem;
    }

    .fila-total {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
    }

    .total-destacado {
        font-weight: bold;
        font-size: 1.1rem;
        color: #107c41;
        border-top: 1px solid #ccc;
        padding-top: 6px;
    }

    .btn-imprimir-flotante {
        text-align: center;
        margin-top: 20px;
    }

    .btn-print {
        background-color: #107c41;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 0.95rem;
        border-radius: 5px;
        cursor: pointer;
    }

    @media print {
        body {
            background: #fff;
            padding: 0;
        }
        .comprobante-card {
            box-shadow: none;
            border: none;
            width: 100%;
            max-width: 100%;
        }
        .btn-imprimir-flotante {
            display: none;
        }
    }
</style>
</head>
<body>

<div class="comprobante-card">

    <div class="encabezado-ticket">
        <h2>Papelería Mundo Escolar</h2>
        <div class="info-empresa">
            RUC: 1792345678001<br>
            Av. Principal y Calle 2<br>
            Tel: 0991234567
        </div>
        <p>Comprobante de Venta</p>
        <p>N° de Registro: <strong>#<?php echo str_pad($venta['id'], 6, '0', STR_PAD_LEFT); ?></strong></p>
    </div>

    <div class="info-meta">
        <div>
            <strong>Cliente:</strong> <?php echo htmlspecialchars($clienteNombre); ?><br>
            <strong>Atendido por:</strong> <?php echo htmlspecialchars($usuarioNombre); ?>
        </div>
        <div style="text-align: right;">
            <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?>
        </div>
    </div>

    <table class="tabla-comprobante">
        <thead>
            <tr>
                <th>Cant.</th>
                <th>Producto</th>
                <th>P. Unit</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($d = mysqli_fetch_assoc($resDetalle)): ?>
                <tr>
                    <td><?php echo $d['cantidad']; ?></td>
                    <td><?php echo htmlspecialchars($d['producto_nombre'] ?? 'Producto no disponible'); ?></td>
                    <td>$<?php echo number_format($d['precio_unitario'], 2); ?></td>
                    <td>$<?php echo number_format($d['subtotal'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="totales-box">
        <div class="fila-total">
            <span>Subtotal:</span>
            <span>$<?php echo number_format($venta['subtotal'], 2); ?></span>
        </div>
        <div class="fila-total">
            <span>IVA (15%):</span>
            <span>$<?php echo number_format($venta['iva'], 2); ?></span>
        </div>
        <div class="fila-total total-destacado">
            <span>Total:</span>
            <span>$<?php echo number_format($venta['total'], 2); ?></span>
        </div>
    </div>

   <div class="btn-imprimir-flotante">
        <button onclick="window.print();" class="btn-print">🖨️ Imprimir Comprobante</button>
        <a href="ventas.php" class="btn-print" style="background-color: #0d529c; text-decoration: none; display: inline-block; margin-left: 10px;">➕ Nueva Venta</a>
    </div>
</div>

<script>
window.onload = function() {
    window.print();
};
</script>

</body>
</html>