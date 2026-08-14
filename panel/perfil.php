<?php

require "proteger.php";
require_once "../includes/conexion.php";

$id = (int)$_SESSION['usuario_id'];

$mensaje = "";
$error   = "";

/* GUARDAR CAMBIOS */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres         = trim($_POST['nombres']);
    $passwordActual  = $_POST['password_actual'] ?? '';
    $passwordNuevo   = $_POST['password_nuevo'] ?? '';
    $passwordConfirm = $_POST['password_confirmar'] ?? '';

    if ($nombres === "") {

        $error = "El nombre no puede estar vacío.";

    } else {

        $resultado  = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id = $id");
        $usuarioBD  = mysqli_fetch_assoc($resultado);

        $sql = "UPDATE usuarios SET nombres = '" . mysqli_real_escape_string($conexion, $nombres) . "'";

        /* Si quiere cambiar la contraseña, valida la actual primero */

        if ($passwordNuevo !== "" || $passwordConfirm !== "" || $passwordActual !== "") {

            if (!password_verify($passwordActual, $usuarioBD['password'])) {

                $error = "La contraseña actual no es correcta.";

            } elseif (strlen($passwordNuevo) < 6) {

                $error = "La nueva contraseña debe tener al menos 6 caracteres.";

            } elseif ($passwordNuevo !== $passwordConfirm) {

                $error = "La confirmación de la nueva contraseña no coincide.";

            } else {

                $hash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
                $sql .= ", password = '$hash'";

            }

        }

        if ($error === "") {

            $sql .= " WHERE id = $id";

            mysqli_query($conexion, $sql);

            $_SESSION['usuario_nombres'] = $nombres;

            $mensaje = "Perfil actualizado correctamente.";

        }

    }

}

$usuario = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM usuarios WHERE id = $id"));

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Perfil - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Mi Perfil</h1>

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

        <div class="tarjeta-panel" style="max-width:600px;">

            <div style="text-align:center; margin-bottom:25px;">

                <i class="fa-solid fa-circle-user" style="font-size:80px; color:#0A4DA3;"></i>

                <h2 style="margin-top:10px;"><?php echo htmlspecialchars($usuario['nombres']); ?></h2>

                <span class="badge-rol"><?php echo htmlspecialchars($usuario['rol']); ?></span>

            </div>

            <form method="POST">

                <div class="campo">
                    <label>Nombres completos</label>
                    <input type="text" name="nombres" value="<?php echo htmlspecialchars($usuario['nombres']); ?>" required>
                </div>

                <div class="campo">
                    <label>Correo</label>
                    <input type="email" value="<?php echo htmlspecialchars($usuario['correo']); ?>" disabled style="background:#f0f0f0;">
                </div>

                <div class="campo">
                    <label>Cuenta creada el</label>
                    <input type="text" value="<?php echo date('d/m/Y', strtotime($usuario['creado_en'])); ?>" disabled style="background:#f0f0f0;">
                </div>

                <hr style="border:none; border-top:1px solid #eee; margin:25px 0;">

                <h3 style="color:#0A4DA3; margin-bottom:15px;">Cambiar contraseña (opcional)</h3>

                <div class="campo">
                    <label>Contraseña actual</label>
                    <input type="password" name="password_actual" placeholder="Requerida solo si vas a cambiarla">
                </div>

                <div class="campo">
                    <label>Nueva contraseña</label>
                    <input type="password" name="password_nuevo" minlength="6">
                </div>

                <div class="campo">
                    <label>Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmar" minlength="6">
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