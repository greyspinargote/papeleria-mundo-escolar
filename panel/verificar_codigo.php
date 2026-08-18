<?php

session_start();

// Verificar que exista una recuperación iniciada
if (
    !isset($_SESSION['recuperacion_usuario_id']) ||
    !isset($_SESSION['recuperacion_codigo']) ||
    !isset($_SESSION['recuperacion_expira'])
) {

    header("Location: recuperar_password.php");
    exit;
}

$error = "";

// Verificar si el código ya expiró
if (time() > $_SESSION['recuperacion_expira']) {

    // Limpiar datos de recuperación
    unset($_SESSION['recuperacion_usuario_id']);
    unset($_SESSION['recuperacion_correo']);
    unset($_SESSION['recuperacion_codigo']);
    unset($_SESSION['recuperacion_expira']);
    unset($_SESSION['codigo_recuperacion_demo']);

    header("Location: recuperar_password.php");
    exit;
}


// Código de demostración
$codigoDemo = $_SESSION['codigo_recuperacion_demo'] ?? "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigoIngresado = trim(
        $_POST['codigo'] ?? ''
    );


    // Validar que sean 6 números
    if ($codigoIngresado === "") {

        $error = "Ingresa el código de recuperación.";

    } elseif (!preg_match('/^[0-9]{6}$/', $codigoIngresado)) {

        $error = "El código debe tener 6 números.";

    } else {

        // Verificar el código
        if (
            password_verify(
                $codigoIngresado,
                $_SESSION['recuperacion_codigo']
            )
        ) {

            /*
             * Código correcto.
             *
             * Guardamos una marca en la sesión
             * para permitir cambiar la contraseña.
             */

            $_SESSION['codigo_verificado'] = true;

            // Ya no necesitamos mostrar el código
            unset($_SESSION['codigo_recuperacion_demo']);

            header(
                "Location: cambiar_password.php"
            );

            exit;

        } else {

            $error = "El código ingresado es incorrecto.";
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
        Verificar código - Mundo Escolar
    </title>

    <link
        rel="stylesheet"
        href="panel.css"
    >

</head>

<body>

<div class="login-caja">

    <h2>
        Verificar código
    </h2>

    <p class="login-marca">
        Mundo Escolar
    </p>

    <p class="login-subtitulo">
        Ingresa el código de 6 dígitos
        enviado a tu correo.
    </p>


    <!-- MENSAJE DE ERROR -->

    <?php if ($error): ?>

        <div class="mensaje-error">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>


    <!-- CÓDIGO DEMOSTRACIÓN -->

    <?php if ($codigoDemo !== ""): ?>

        <div
            class="mensaje-exito"
            style="text-align:center;"
        >

            <strong>
                Código de prueba:
            </strong>

            <br>

            <span
                style="
                    font-size:24px;
                    font-weight:700;
                    letter-spacing:5px;
                "
            >
                <?php
                echo htmlspecialchars($codigoDemo);
                ?>
            </span>

        </div>

    <?php endif; ?>


    <!-- FORMULARIO -->

    <form method="POST">

        <div class="campo">

            <label for="codigo">
                Código de recuperación
            </label>

            <input
                type="text"
                id="codigo"
                name="codigo"
                placeholder="Ejemplo: 123456"
                maxlength="6"
                inputmode="numeric"
                autocomplete="one-time-code"
                required
                autofocus
            >

        </div>


        <button
            type="submit"
            class="btn-ingresar"
        >
            Verificar código
        </button>

    </form>


    <div class="login-footer">

        <a
            href="recuperar_password.php"
            style="
                color:#0A4DA3;
                font-weight:600;
            "
        >
            ← Volver
        </a>

    </div>

</div>

</body>

</html>