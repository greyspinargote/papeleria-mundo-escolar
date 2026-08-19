<?php

require "proteger.php";
require_once "../includes/conexion.php";

$mensaje = "";
$error   = "";

/* GUARDAR NUEVO CLIENTE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_cliente'])) {

    $nombres   = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $correo    = trim($_POST['correo']);
    $telefono  = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);

    if ($nombres === "" || $correo === "") {
        $error = "Nombre y correo son campos obligatorios.";
    } else {

        $nombresEsc   = mysqli_real_escape_string($conexion, $nombres);
        $apellidosEsc = mysqli_real_escape_string($conexion, $apellidos);
        $correoEsc    = mysqli_real_escape_string($conexion, $correo);
        $telefonoEsc  = mysqli_real_escape_string($conexion, $telefono);
        $direccionEsc = mysqli_real_escape_string($conexion, $direccion);

        // Verificar si el correo ya existe
        $checkCorreo = mysqli_query($conexion, "SELECT id FROM clientes WHERE correo = '$correoEsc'");
        
        if (mysqli_num_rows($checkCorreo) > 0) {
            $error = "El correo ya se encuentra registrado.";
        } else {
            $sqlInsert = "INSERT INTO clientes (nombres, apellidos, correo, telefono, direccion) 
                          VALUES ('$nombresEsc', '$apellidosEsc', '$correoEsc', '$telefonoEsc', '$direccionEsc')";

            if (mysqli_query($conexion, $sqlInsert)) {
                $mensaje = "Cliente registrado correctamente.";
            } else {
                $error = "Error al registrar cliente: " . mysqli_error($conexion);
            }
        }
    }
}

/* BÚSQUEDA DE CLIENTES */
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

$sql = "SELECT c.*,
        (SELECT COUNT(*) FROM pedidos p WHERE p.cliente_id = c.id) AS total_pedidos,
        (SELECT COALESCE(SUM(p.total),0) FROM pedidos p WHERE p.cliente_id = c.id) AS total_gastado
        FROM clientes c";

if ($busqueda !== "") {
    $busquedaEsc = mysqli_real_escape_string($conexion, $busqueda);
    $sql .= " WHERE c.nombres LIKE '%$busquedaEsc%'
              OR c.apellidos LIKE '%$busquedaEsc%'
              OR c.correo LIKE '%$busquedaEsc%'
              OR c.telefono LIKE '%$busquedaEsc%'";
}

$sql .= " ORDER BY c.id DESC";

$clientes = mysqli_query($conexion, $sql);

// Inicial del usuario logueado para el círculo superior
$inicialUsuario = !empty($_SESSION['usuario_nombres']) ? strtoupper(substr($_SESSION['usuario_nombres'], 0, 1)) : 'G';

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

        <!-- ENCABEZADO IDÉNTICO AL PANEL PRINCIPAL -->
        <div class="encabezado-panel">
            <div class="titulos-header">
                <h1>Clientes</h1>
                <p class="subtitulo-header">Gestión de clientes Mundo Escolar 👋</p>
            </div>

            <div class="tarjeta-usuario-header">
                <div class="avatar-inicial"><?php echo $inicialUsuario; ?></div>
                <div class="info-usuario-header">
                    <span class="nombre-user"><?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?></span>
                    <span class="rol-user"><?php echo htmlspecialchars(ucfirst($_SESSION['usuario_rol'])); ?></span>
                </div>
            </div>
        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje-exito" style="background:#d1fae5; color:#065f46; padding:12px; border-radius:8px; margin-bottom:15px;">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mensaje-error" style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="tarjeta-panel">

            <!-- BOTÓN "+ NUEVO CLIENTE" -->
            <div style="margin-bottom: 20px;">
                <button type="button" class="btn-agregar-verde" onclick="toggleFormularioCliente()">
                    <i class="fa-solid fa-plus"></i> Nuevo cliente
                </button>
            </div>

            <!-- FORMULARIO OCULTO / DESPLEGABLE -->
            <div id="formNuevoCliente" class="contenedor-form-desplegable">
                <h3 class="titulo-form-cliente">Registrar Nuevo Cliente</h3>
                <form method="POST">
                    <div class="grid-formulario">
                        <div class="campo-grupo">
                            <label>Nombres</label>
                            <input type="text" name="nombres" required>
                        </div>
                        <div class="campo-grupo">
                            <label>Apellidos</label>
                            <input type="text" name="apellidos">
                        </div>
                        <div class="campo-grupo">
                            <label>Correo Electrónico</label>
                            <input type="email" name="correo" required>
                        </div>
                        <div class="campo-grupo">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" placeholder="Ej. 0987654321">
                        </div>
                        <div class="campo-grupo completo">
                            <label>Dirección</label>
                            <input type="text" name="direccion" placeholder="Ej. Av. Principal #123">
                        </div>
                    </div>
                    
                    <div class="acciones-form">
                        <button type="submit" name="guardar_cliente" class="btn-agregar-verde">Guardar Cliente</button>
                        <button type="button" class="btn-cancelar" onclick="toggleFormularioCliente()">Cancelar</button>
                    </div>
                </form>
            </div>

            <!-- BUSCADOR CON ALINEACIÓN Y ESTILO APLICADO -->
            <form method="GET" action="clientes.php" class="form-busqueda-producto">
                <input
                    type="text"
                    name="buscar"
                    placeholder="Buscar por nombre, correo o teléfono..."
                    value="<?php echo htmlspecialchars($busqueda); ?>"
                    class="input-busqueda-producto"
                >
                <button type="submit" class="btn-buscar-producto">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>

                <?php if ($busqueda !== ""): ?>
                    <a href="clientes.php" class="btn-limpiar-busqueda">
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <table class="tabla-panel" style="margin-top:20px;">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th style="text-align:center;">Pedidos</th>
                        <th>Total gastado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
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
                                <td><?php echo htmlspecialchars(!empty($c['telefono']) ? $c['telefono'] : 'S/N'); ?></td>
                                <td><?php echo htmlspecialchars(!empty($c['direccion']) ? $c['direccion'] : 'S/N'); ?></td>
                                <td style="text-align:center;"><?php echo $c['total_pedidos']; ?></td>
                                <td>$<?php echo number_format($c['total_gastado'], 2); ?></td>
                                <td>
                                    <a href="pedidos_cliente.php?id=<?php echo $c['id']; ?>" style="color:#0A4DA3; font-weight:600; text-decoration:none;">
                                        Ver pedidos
                                    </a>
                                </td>
                            </tr>

                        <?php } ?>

                    <?php endif; ?>
                </tbody>
            </table>

        </div>

    </div>

</div>

<script>
function toggleFormularioCliente() {
    var form = document.getElementById('formNuevoCliente');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>

</body>
</html>