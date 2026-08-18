<?php

session_start();
require_once "../includes/conexion.php";

$error = "";

// Si ya existe una sesión
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Usuario guardado por "Recordarme"
$usuarioRecordado = $_COOKIE['usuario_recordado'] ?? "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuarioInput = trim($_POST['usuario'] ?? '');
    $password     = $_POST['password'] ?? '';
    $recordar     = isset($_POST['recordar']);

    // Validar campos
    if ($usuarioInput === "" || $password === "") {

        $error = "Ingresa tu usuario y contraseña.";

    } else {

        $usuarioEsc = mysqli_real_escape_string(
            $conexion,
            $usuarioInput
        );

        // Buscar usuario activo
        $resultado = mysqli_query(
            $conexion,
            "SELECT *
             FROM usuarios
             WHERE nombres = '$usuarioEsc'
             AND estado = 1
             LIMIT 1"
        );

        if (!$resultado) {

            $error = "Ocurrió un error al consultar el usuario.";

        } else {

            $usuario = mysqli_fetch_assoc($resultado);

            // Verificar contraseña
            if (
                $usuario &&
                password_verify(
                    $password,
                    $usuario['password']
                )
            ) {

                // Crear sesión
                $_SESSION['usuario_id']      = $usuario['id'];
                $_SESSION['usuario_nombres'] = $usuario['nombres'];
                $_SESSION['usuario_rol']     = $usuario['rol'];

                // Recordarme
                if ($recordar) {

                    setcookie(
                        "usuario_recordado",
                        $usuario['nombres'],
                        [
                            'expires'  => time() + (86400 * 30),
                            'path'     => '/',
                            'secure'   => isset($_SERVER['HTTPS']),
                            'httponly' => true,
                            'samesite' => 'Lax'
                        ]
                    );

                } else {

                    setcookie(
                        "usuario_recordado",
                        "",
                        [
                            'expires'  => time() - 3600,
                            'path'     => '/',
                            'secure'   => isset($_SERVER['HTTPS']),
                            'httponly' => true,
                            'samesite' => 'Lax'
                        ]
                    );
                }

                header("Location: dashboard.php");
                exit;

            } else {

                $error =
                    "Usuario o contraseña incorrectos, o usuario inactivo.";
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
        Panel Administrativo - Mundo Escolar
    </title>

    <link rel="stylesheet" href="panel.css">

</head>

<body>

<div class="login-caja">

    <h2>
        Panel Administrativo
    </h2>

    <p class="login-marca">
        Mundo Escolar
    </p>

    <p class="login-subtitulo">
        Ingresa tus datos para continuar
    </p>


    <!-- MENSAJE DE ERROR -->

    <?php if ($error): ?>

        <div class="mensaje-error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- FORMULARIO -->

    <form method="POST">

        <!-- USUARIO -->

        <div class="campo">

            <label for="usuario">
                Usuario
            </label>

            <input
                type="text"
                id="usuario"
                name="usuario"
                placeholder="Ingresa tu usuario"
                value="<?php echo htmlspecialchars($usuarioRecordado); ?>"
                autocomplete="username"
                required
                autofocus
            >

        </div>


        <!-- CONTRASEÑA -->

        <div class="campo">

            <label for="password">
                Contraseña
            </label>

            <div class="password-contenedor">

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Ingresa tu contraseña"
                    autocomplete="current-password"
                    required
                >

                <button
                    type="button"
                    class="mostrar-password"
                    onclick="mostrarPassword()"
                >
                    Mostrar
                </button>

            </div>

        </div>


        <!-- RECORDAR Y RECUPERAR -->

        <div class="login-opciones">

            <label class="recordar">

                <input
                    type="checkbox"
                    name="recordar"
                    <?php
                    echo $usuarioRecordado !== ''
                        ? 'checked'
                        : '';
                    ?>
                >

                <span>
                    Recordarme
                </span>

            </label>


            <a href="recuperar_password.php">
                ¿Olvidaste tu contraseña?
            </a>

        </div>


        <!-- BOTÓN -->

        <button
            type="submit"
            class="btn-ingresar"
        >
            Ingresar
        </button>

    </form>


    <!-- PIE -->

    <div class="login-footer">

        © <?php echo date('Y'); ?> Mundo Escolar

    </div>

</div>


<script>

function mostrarPassword() {

    const password =
        document.getElementById("password");

    const boton =
        document.querySelector(".mostrar-password");

    if (password.type === "password") {

        password.type = "text";

        boton.textContent = "Ocultar";

    } else {

        password.type = "password";

        boton.textContent = "Mostrar";
    }
}

</script>

</body>

</html>