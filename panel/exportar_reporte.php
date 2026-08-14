<?php

require "proteger.php";
require_once "../includes/conexion.php";

$fechaInicio = isset($_GET['inicio']) && $_GET['inicio'] !== "" ? $_GET['inicio'] : date('Y-m-01');
$fechaFin    = isset($_GET['fin']) && $_GET['fin'] !== "" ? $_GET['fin'] : date('Y-m-d');
$origen      = isset($_GET['origen']) && in_array($_GET['origen'], ['web', 'tienda']) ? $_GET['origen'] : '';

$fechaInicioEsc = mysqli_real_escape_string($conexion, $fechaInicio);
$fechaFinEsc    = mysqli_real_escape_string($conexion, $fechaFin);

$condiciones = "WHERE DATE(p.fecha) BETWEEN '$fechaInicioEsc' AND '$fechaFinEsc'";

if ($origen !== '') {
    $condiciones .= " AND p.origen = '$origen'";
}

$pedidos = mysqli_query($conexion, "
    SELECT p.id, p.fecha, p.total, p.estado, p.origen, c.nombres, c.apellidos, c.correo
    FROM pedidos p
    LEFT JOIN clientes c ON c.id = p.cliente_id
    $condiciones
    ORDER BY p.fecha DESC
");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=reporte_ventas_' . $fechaInicio . '_a_' . $fechaFin . '.csv');

$salida = fopen('php://output', 'w');

// BOM para que Excel muestre bien los acentos
fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($salida, ['Pedido', 'Fecha', 'Cliente', 'Correo', 'Origen', 'Estado', 'Total']);

while ($p = mysqli_fetch_assoc($pedidos)) {

    fputcsv($salida, [
        '#' . $p['id'],
        $p['fecha'],
        $p['nombres'] ? $p['nombres'] . ' ' . $p['apellidos'] : 'Mostrador',
        $p['correo'] ?? '',
        $p['origen'] === 'web' ? 'Tienda Web' : 'Mostrador',
        $p['estado'],
        $p['total']
    ]);

}

fclose($salida);
exit;