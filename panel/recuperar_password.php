<?php

session_start();
require_once "../includes/conexion.php";

$error = "";
$exito = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo'] ?? '');

    // Validar correo
    if ($correo === "") {

        $error = "Ingresa tu correo electrónico.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $error = "Ingresa un correo electrónico válido.";

    } else {

        // Proteger correo
        $correoEsc = mysqli_real_escape_string(
            $conexion,
            $correo
        );

        // Buscar usuario activo
        $resultado = mysqli_query(
            $conexion,
            "SELECT id, nombres, correo
             FROM usuarios
             WHERE correo = '$correoEsc'
             AND estado = 1
             LIMIT 1"
        );

        if (!$resultado) {

            $error = "Ocurrió un error al consultar el correo.";

        } else {

            $usuario = mysqli_fetch_assoc($resultado);

            if ($usuario) {

                /*
                 * Generar código de recuperación
                 * de 6 números.
                 */
                $codigo = random_int(100000, 999999);

                /*
                 * Guardamos temporalmente los datos
                 * en la sesión.
                 *
                 * NO guardamos la contraseña.
                 */
                $_SESSION['recuperacion_usuario_id'] =
                    $usuario['id'];

                $_SESSION['recuperacion_correo'] =
                    $usuario['correo'];

                $_SESSION['recuperacion_codigo'] =
                    password_hash($codigo, PASSWORD_DEFAULT);

                $_SESSION['recuperacion_expira'] =
                    time() + 600; // 10 minutos

                /*
                 * Por ahora mostramos un mensaje.
                 *
                 * Después podemos conectar SMTP
                 * para enviar el código realmente
                 * al correo.
                 */
                $_SESSION['codigo_recuperacion_demo'] =
                    $codigo;

                header(
                    "Location: verificar_codigo.php"
                );

                exit;

            } else {

                $error =
                    "No existe una cuenta activa asociada a ese correo.";
            }
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
        Recuperar contraseña - Mundo Escolar
    </title>

    <link
        rel="stylesheet"
        href="panel.css"
    >

</head>

<body>

<div class="login-caja">

    <h2>
        Recuperar contraseña
    </h2>

    <p class="login-marca">
        Mundo Escolar
    </p>

    <p class="login-subtitulo">
        Ingresa el correo asociado a tu cuenta
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

            <label for="correo">
                Correo electrónico
            </label>

            <input
                type="email"
                id="correo"
                name="correo"
                placeholder="Ingresa tu correo"
                autocomplete="email"
                required
                autofocus
            >

        </div>


        <button
            type="submit"
            class="btn-ingresar"
        >
            Enviar código
        </button>

    </form>


    <div class="login-footer">

        <a
            href="login.php"
            style="color:#0A4DA3; font-weight:600;"
        >
            ← Volver al inicio de sesión
        </a>

    </div>

</div>

</body>

</html>