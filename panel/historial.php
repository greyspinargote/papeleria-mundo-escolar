<?php

require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";
$origen   = isset($_GET['origen']) && in_array($_GET['origen'], ['web', 'tienda']) ? $_GET['origen'] : '';
$estado   = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : '';

$condiciones = [];

if ($busqueda !== "") {

    $busquedaEsc = mysqli_real_escape_string($conexion, $busqueda);

    if (ctype_digit($busqueda)) {

        $condiciones[] = "p.id = " . (int)$busqueda;

    } else {

        $condiciones[] = "(c.nombres LIKE '%$busquedaEsc%' OR c.apellidos LIKE '%$busquedaEsc%' OR c.correo LIKE '%$busquedaEsc%')";

    }

}

if ($origen !== '') {
    $condiciones[] = "p.origen = '$origen'";
}

if ($estado !== '') {
    $estadoEsc = mysqli_real_escape_string($conexion, $estado);
    $condiciones[] = "p.estado = '$estadoEsc'";
}

$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

$pedidos = mysqli_query($conexion, "
    SELECT p.*, c.nombres, c.apellidos
    FROM pedidos p
    LEFT JOIN clientes c ON c.id = p.cliente_id
    $where
    ORDER BY p.fecha DESC
    LIMIT 100
");

$estadosDisponibles = mysqli_query($conexion, "SELECT DISTINCT estado FROM pedidos ORDER BY estado ASC");

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial de Ventas - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Historial de Ventas</h1>

            <div class="usuario-actual">
                <i class="fa-solid fa-circle-user"></i>
                <?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?>
                <span class="badge-rol"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
            </div>

        </div>

        <div class="tarjeta-panel">

            <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">

                <div class="campo" style="margin:0; flex:1; min-width:200px;">
                    <label>Buscar (# pedido, nombre o correo)</label>
                    <input type="text" name="buscar" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Ej: 12 o María">
                </div>

                <div class="campo" style="margin:0;">
                    <label>Origen</label>
                    <select name="origen">
                        <option value="" <?php echo $origen === '' ? 'selected' : ''; ?>>Todos</option>
                        <option value="web" <?php echo $origen === 'web' ? 'selected' : ''; ?>>Tienda Web</option>
                        <option value="tienda" <?php echo $origen === 'tienda' ? 'selected' : ''; ?>>Mostrador</option>
                    </select>
                </div>

                <div class="campo" style="margin:0;">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <?php while ($e = mysqli_fetch_assoc($estadosDisponibles)) { ?>
                            <option value="<?php echo htmlspecialchars($e['estado']); ?>"
                                <?php echo $estado === $e['estado'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($e['estado']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button type="submit" class="btn-panel">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>

                <a href="historial.php" class="btn-panel rojo" style="text-decoration:none; display:flex; align-items:center;">
                    Limpiar
                </a>

            </form>

        </div>

        <div class="tarjeta-panel">

            <table class="tabla-panel">

                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Origen</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Detalle</th>
                </tr>

                <?php if (mysqli_num_rows($pedidos) === 0): ?>

                    <tr>
                        <td colspan="7" style="text-align:center; padding:30px; color:#888;">
                            No se encontraron ventas con esos filtros.
                        </td>
                    </tr>

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
                            <td>
                                <a href="ver_pedido.php?id=<?php echo $p['id']; ?>" style="color:#0A4DA3; font-weight:600;">
                                    Ver
                                </a>
                            </td>
                        </tr>

                    <?php } ?>

                <?php endif; ?>

            </table>

            <p style="color:#999; font-size:13px; margin-top:15px;">
                Mostrando los últimos 100 registros. Usa los filtros para encontrar ventas específicas.
            </p>

        </div>

    </div>

</div>

</body>
</html>