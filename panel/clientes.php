<?php

require "proteger.php";
require_once "../includes/conexion.php";

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

$sql = "SELECT c.*,
        (SELECT COUNT(*) FROM pedidos p WHERE p.cliente_id = c.id) AS total_pedidos,
        (SELECT COALESCE(SUM(p.total),0) FROM pedidos p WHERE p.cliente_id = c.id) AS total_gastado
        FROM clientes c";

if ($busqueda !== "") {

    $busquedaEsc = mysqli_real_escape_string($conexion, $busqueda);
    $sql .= " WHERE c.nombres LIKE '%$busquedaEsc%'
              OR c.apellidos LIKE '%$busquedaEsc%'
              OR c.correo LIKE '%$busquedaEsc%'";

}

$sql .= " ORDER BY c.id DESC";

$clientes = mysqli_query($conexion, $sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Clientes - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Clientes</h1>

            <div class="usuario-actual">
                <i class="fa-solid fa-circle-user"></i>
                <?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?>
                <span class="badge-rol"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
            </div>

        </div>

        <div class="tarjeta-panel">

            <form method="GET" style="display:flex; gap:10px; margin-bottom:20px;">

                <input
                    type="text"
                    name="buscar"
                    placeholder="Buscar por nombre o correo..."
                    value="<?php echo htmlspecialchars($busqueda); ?>"
                    style="flex:1; padding:12px; border:1px solid #ccc; border-radius:8px;">

                <button type="submit" class="btn-panel">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>

                <?php if ($busqueda !== ""): ?>
                    <a href="clientes.php" class="btn-panel rojo" style="display:flex; align-items:center;">Limpiar</a>
                <?php endif; ?>

            </form>

            <table class="tabla-panel">

                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Pedidos</th>
                    <th>Total gastado</th>
                    <th>Acciones</th>
                </tr>

                <?php if (mysqli_num_rows($clientes) === 0): ?>

                    <tr>
                        <td colspan="7" style="text-align:center; padding:30px; color:#888;">
                            No se encontraron clientes.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php while ($c = mysqli_fetch_assoc($clientes)) { ?>

                        <tr>
                            <td><?php echo htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($c['correo']); ?></td>
                            <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($c['direccion']); ?></td>
                            <td style="text-align:center;"><?php echo $c['total_pedidos']; ?></td>
                            <td>$<?php echo number_format($c['total_gastado'], 2); ?></td>
                            <td>
                                <a href="pedidos_cliente.php?id=<?php echo $c['id']; ?>" style="color:#0A4DA3; font-weight:600;">
                                    Ver pedidos
                                </a>
                            </td>
                        </tr>

                    <?php } ?>

                <?php endif; ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>