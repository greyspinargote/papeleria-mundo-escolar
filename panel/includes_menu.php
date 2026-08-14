<?php

$paginaActual = basename($_SERVER['PHP_SELF']);
$esAdmin      = $_SESSION['usuario_rol'] === 'admin';

function activo($nombreArchivo, $paginaActual) {
    return $nombreArchivo === $paginaActual ? 'activo' : '';
}

?>
<div class="sidebar">

    <div class="marca-sidebar">
        <i class="fa-solid fa-graduation-cap"></i>
        Mundo Escolar
    </div>

    <nav>

        <a href="dashboard.php" class="<?php echo activo('dashboard.php', $paginaActual); ?>">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>

        <a href="productos.php" class="<?php echo activo('productos.php', $paginaActual); ?>">
            <i class="fa-solid fa-box"></i> Productos
        </a>

        <a href="clientes.php" class="<?php echo activo('clientes.php', $paginaActual); ?>">
            <i class="fa-solid fa-users"></i> Clientes
        </a>

        <a href="ventas.php" class="<?php echo activo('ventas.php', $paginaActual); ?>">
            <i class="fa-solid fa-cash-register"></i> Ventas
        </a>

        <?php if ($esAdmin): ?>

            <a href="inventario.php" class="<?php echo activo('inventario.php', $paginaActual); ?>">
                <i class="fa-solid fa-warehouse"></i> Inventario
            </a>

            <a href="reportes.php" class="<?php echo activo('reportes.php', $paginaActual); ?>">
                <i class="fa-solid fa-file-lines"></i> Reportes
            </a>

        <?php endif; ?>

        <a href="historial.php" class="<?php echo activo('historial.php', $paginaActual); ?>">
            <i class="fa-solid fa-clock-rotate-left"></i> Historial de ventas
        </a>

        <?php if ($esAdmin): ?>

            <a href="usuarios.php" class="<?php echo activo('usuarios.php', $paginaActual); ?>">
                <i class="fa-solid fa-user-gear"></i> Usuarios
            </a>

            <a href="configuracion.php" class="<?php echo activo('configuracion.php', $paginaActual); ?>">
                <i class="fa-solid fa-gear"></i> Configuración
            </a>

        <?php endif; ?>

        <a href="perfil.php" class="<?php echo activo('perfil.php', $paginaActual); ?>">
            <i class="fa-solid fa-circle-user"></i> Mi perfil
        </a>

        <a href="logout.php" class="cerrar-sesion">
            <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
        </a>

    </nav>

</div>