<?php

session_start();
require_once "../includes/conexion.php";

$error = "";

// Verificar que el usuario haya pasado por la verificación del código
if (
    !isset($_SESSION['recuperacion_usuario_id']) ||
    !isset($_SESSION['codigo_verificado']) ||
    $_SESSION['codigo_verificado'] !== true
) {

    header("Location: recuperar_password.php");
    exit;
}

// Verificar que el proceso no haya expirado
if (
    !isset($_SESSION['recuperacion_expira']) ||
    time() > $_SESSION['recuperacion_expira']
) {

    // Limpiar sesión de recuperación
    unset($_SESSION['recuperacion_usuario_id']);
    unset($_SESSION['recuperacion_correo']);
    unset($_SESSION['recuperacion_codigo']);
    unset($_SESSION['recuperacion_expira']);
    unset($_SESSION['codigo_verificado']);
    unset($_SESSION['codigo_recuperacion_demo']);

    header("Location: recuperar_password.php?expirado=1");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar_password'] ?? '';

    // Validar contraseña
    if ($password === "" || $confirmar === "") {

        $error = "Completa ambos campos.";

    } elseif (strlen($password) < 6) {

        $error = "La contraseña debe tener al menos 6 caracteres.";

    } elseif ($password !== $confirmar) {

        $error = "Las contraseñas no coinciden.";

    } else {

        $idUsuario = $_SESSION['recuperacion_usuario_id'];

        // Encriptar nueva contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Proteger datos
        $idEsc = mysqli_real_escape_string($conexion, $idUsuario);
        $hashEsc = mysqli_real_escape_string($conexion, $hash);

        // Actualizar contraseña en la base de datos
        $actualizar = mysqli_query(
            $conexion,
            "UPDATE usuarios
             SET password = '$hashEsc'
             WHERE id = '$idEsc'"
        );

        if (!$actualizar) {

            $error = "Ocurrió un error al actualizar la contraseña.";

        } else {

            /*
             * Contraseña actualizada con éxito.
             * Limpiamos toda la sesión de recuperación
             * para que el enlace/proceso no se pueda
             * reutilizar.
             */
            unset($_SESSION['recuperacion_usuario_id']);
            unset($_SESSION['recuperacion_correo']);
            unset($_SESSION['recuperacion_codigo']);
            unset($_SESSION['recuperacion_expira']);
            unset($_SESSION['codigo_verificado']);
            unset($_SESSION['codigo_recuperacion_demo']);

            header("Location: login.php?password_actualizada=1");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Cambiar contraseña - Mundo Escolar
    </title>

    <link
        rel="stylesheet"
        href="panel.css"
    >

</head>

<body>

<div class="login-caja">

    <h2>
        Cambiar contraseña
    </h2>

    <p class="login-marca">
        Mundo Escolar
    </p>

    <p class="login-subtitulo">
        Ingresa tu nueva contraseña
    </p>


    <!-- ERROR -->

    <?php if ($error): ?>

        <div class="mensaje-error">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>


    <!-- FORMULARIO -->

    <form method="POST">

        <div class="campo">

            <label for="password">
                Nueva contraseña
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Mínimo 6 caracteres"
                autocomplete="new-password"
                required
                autofocus
            >

        </div>

        <div class="campo">

            <label for="confirmar_password">
                Confirmar contraseña
            </label>

            <input
                type="password"
                id="confirmar_password"
                name="confirmar_password"
                placeholder="Repite la contraseña"
                autocomplete="new-password"
                required
            >

        </div>


        <button
            type="submit"
            class="btn-ingresar"
        >
            Cambiar contraseña
        </button>

    </form>


    <div class="login-footer"
    > 
        
            ← Volver al inicio de sesión
        </a>

    </div>

</div>

</body>

</html>