<?php

require_once "includes/conexion.php";

include "includes/header.php";
include "includes/navbar.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres    = trim($_POST['nombres'] ?? '');
    $apellidos  = trim($_POST['apellidos'] ?? '');
    $telefono   = trim($_POST['telefono'] ?? '');
    $correo     = trim($_POST['correo'] ?? '');
    $direccion  = trim($_POST['direccion'] ?? '');
    $password   = $_POST['password'] ?? '';
    $password2  = $_POST['password2'] ?? '';

    if ($nombres === "" || $apellidos === "" || $correo === "" || $password === "") {

        $error = "Completa todos los campos obligatorios.";

    } elseif ($password !== $password2) {

        $error = "Las contraseñas no coinciden.";

    } elseif (strlen($password) < 6) {

        $error = "La contraseña debe tener al menos 6 caracteres.";

    } else {

        $correoEsc = mysqli_real_escape_string($conexion, $correo);

        $verificar = mysqli_query($conexion, "SELECT id FROM clientes WHERE correo = '$correoEsc'");

        if (mysqli_num_rows($verificar) > 0) {

            $error = "Ya existe una cuenta registrada con ese correo.";

        } else {

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $nombresEsc   = mysqli_real_escape_string($conexion, $nombres);
            $apellidosEsc = mysqli_real_escape_string($conexion, $apellidos);
            $telefonoEsc  = mysqli_real_escape_string($conexion, $telefono);
            $direccionEsc = mysqli_real_escape_string($conexion, $direccion);

            $sql = "INSERT INTO clientes (nombres, apellidos, telefono, correo, password, direccion)
                    VALUES ('$nombresEsc', '$apellidosEsc', '$telefonoEsc', '$correoEsc', '$passwordHash', '$direccionEsc')";

            if (mysqli_query($conexion, $sql)) {

                $nuevoId = mysqli_insert_id($conexion);

                $_SESSION['cliente_id']     = $nuevoId;
                $_SESSION['cliente_nombre'] = $nombres;

                header("Location: index.php");
                exit;

            } else {

                $error = "Ocurrió un error al crear la cuenta. Intenta de nuevo.";

            }

        }

    }

}

?>

<section class="finalizar-compra">

    <div class="titulo-seccion">

        <h2>Crear Cuenta</h2>

        <p>Regístrate para agilizar tus próximas compras.</p>

    </div>

    <form method="POST" class="formulario-compra">

        <?php if ($error): ?>

            <div style="background:#f8d7da; color:#721c24; padding:14px; border-radius:8px; margin-bottom:20px;">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <div class="grupo">
            <label>Nombres</label>
            <input type="text" name="nombres" required>
        </div>

        <div class="grupo">
            <label>Apellidos</label>
            <input type="text" name="apellidos" required>
        </div>

        <div class="grupo">
            <label>Correo electrónico</label>
            <input type="email" name="correo" required>
        </div>

        <div class="grupo">
            <label>Teléfono</label>
            <input type="text" name="telefono">
        </div>

        <div class="grupo">
            <label>Dirección</label>
            <textarea name="direccion" rows="3"></textarea>
        </div>

        <div class="grupo">
            <label>Contraseña</label>
            <input type="password" name="password" required>
        </div>

        <div class="grupo">
            <label>Confirmar contraseña</label>
            <input type="password" name="password2" required>
        </div>

        <button type="submit" class="btn-comprar">Crear cuenta</button>

        <p style="text-align:center; margin-top:20px;">
            ¿Ya tienes cuenta? <a href="login.php" style="color:var(--azul); font-weight:600;">Inicia sesión</a>
        </p>

    </form>

</section>

<?php include "includes/footer.php"; ?>