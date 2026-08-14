<?php

require "proteger.php";
require_once "../includes/conexion.php";

$mensaje = "";
$error   = "";

/* GUARDAR CAMBIOS */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombreTienda   = trim($_POST['nombre_tienda']);
    $telefono       = trim($_POST['telefono']);
    $correo         = trim($_POST['correo']);
    $direccion      = trim($_POST['direccion']);
    $horario        = trim($_POST['horario']);
    $ivaPorcentaje  = (float)$_POST['iva_porcentaje'];
    $mensajeEnvios  = trim($_POST['mensaje_envios']);

    if ($nombreTienda === "") {

        $error = "El nombre de la tienda es obligatorio.";

    } else {

        $nombreEsc    = mysqli_real_escape_string($conexion, $nombreTienda);
        $telefonoEsc  = mysqli_real_escape_string($conexion, $telefono);
        $correoEsc    = mysqli_real_escape_string($conexion, $correo);
        $direccionEsc = mysqli_real_escape_string($conexion, $direccion);
        $horarioEsc   = mysqli_real_escape_string($conexion, $horario);
        $enviosEsc    = mysqli_real_escape_string($conexion, $mensajeEnvios);

        mysqli_query($conexion, "UPDATE configuracion SET
            nombre_tienda = '$nombreEsc',
            telefono = '$telefonoEsc',
            correo = '$correoEsc',
            direccion = '$direccionEsc',
            horario = '$horarioEsc',
            iva_porcentaje = $ivaPorcentaje,
            mensaje_envios = '$enviosEsc',
            actualizado_en = NOW()
            WHERE id = 1");

        $mensaje = "Configuración actualizada correctamente.";

    }

}

$config = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM configuracion WHERE id = 1"));

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Configuración - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Configuración</h1>

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

            <h2>Datos generales de la tienda</h2>

            <form method="POST">

                <div class="campo">
                    <label>Nombre de la tienda</label>
                    <input type="text" name="nombre_tienda" value="<?php echo htmlspecialchars($config['nombre_tienda']); ?>" required>
                </div>

                <div class="campo">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($config['telefono']); ?>">
                </div>

                <div class="campo">
                    <label>Correo de contacto</label>
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($config['correo']); ?>">
                </div>

                <div class="campo">
                    <label>Dirección</label>
                    <input type="text" name="direccion" value="<?php echo htmlspecialchars($config['direccion']); ?>">
                </div>

                <div class="campo">
                    <label>Horario de atención</label>
                    <input type="text" name="horario" value="<?php echo htmlspecialchars($config['horario']); ?>">
                </div>

                <div class="campo">
                    <label>Porcentaje de IVA (%)</label>
                    <input type="number" step="0.01" name="iva_porcentaje" value="<?php echo $config['iva_porcentaje']; ?>">
                </div>

                <div class="campo">
                    <label>Mensaje de la barra superior (ej: "Envíos a todo Ecuador")</label>
                    <input type="text" name="mensaje_envios" value="<?php echo htmlspecialchars($config['mensaje_envios']); ?>">
                </div>

                <button type="submit" class="btn-panel">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>