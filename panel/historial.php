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

            <div class="header-info">
                <h1>Historial de Ventas</h1>
                <p class="subtitulo-panel">Consulta y gestión de ventas de Mundo Escolar 👋</p>
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
                    <div class="campo campo-busqueda">
                        <label>Buscar (# pedido, nombre o correo)</label>
                        <input type="text" name="buscar" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Ej: 12 o María">
                    </div>

                    <div class="campo">
                        <label>Origen</label>
                        <select name="origen">
                            <option value="" <?php echo $origen === '' ? 'selected' : ''; ?>>Todos</option>
                            <option value="web" <?php echo $origen === 'web' ? 'selected' : ''; ?>>Tienda Web</option>
                            <option value="tienda" <?php echo $origen === 'tienda' ? 'selected' : ''; ?>>Mostrador</option>
                        </select>
                    </div>

                    <div class="campo">
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
                </div>

                <div class="acciones-exportar">
                    <a href="historial.php" class="btn-panel btn-limpiar">
                        <i class="fa-solid fa-rotate-left"></i> Limpiar
                    </a>
                </div>

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
                        <td colspan="7" class="tabla-vacia">
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
                                <a href="ver_pedido.php?id=<?php echo $p['id']; ?>" class="link-detalle">
                                    Ver
                                </a>
                            </td>
                        </tr>

                    <?php } ?>

                <?php endif; ?>

            </table>

            <p class="nota-registros">
                Mostrando los últimos 100 registros. Usa los filtros para encontrar ventas específicas.
            </p>

        </div>

    </div>

</div>

</body>
</html>