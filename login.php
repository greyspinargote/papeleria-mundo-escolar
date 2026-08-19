<?php

require_once "includes/conexion.php";

include "includes/header.php";
include "includes/navbar.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre   = trim($_POST['nombre'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nombre === "" || $password === "") {

        $error = "Ingresa tu nombre y contraseña.";

    } else {

        $nombreEsc = mysqli_real_escape_string($conexion, $nombre);

        $resultado = mysqli_query($conexion, "SELECT * FROM clientes WHERE nombres = '$nombreEsc'");
        $cliente   = mysqli_fetch_assoc($resultado);

        if ($cliente && password_verify($password, $cliente['password'])) {

            $_SESSION['cliente_id']     = $cliente['id'];
            $_SESSION['cliente_nombre'] = $cliente['nombres'];

            header("Location: index.php");
            exit;

        } else {

            $error = "Nombre o contraseña incorrectos.";

        }

    }

}

?>

<section class="finalizar-compra">

    <div class="titulo-seccion">

        <h2>Iniciar Sesión</h2>

        <p>Ingresa a tu cuenta para continuar.</p>

    </div>

    <form method="POST" class="formulario-compra">

        <?php if ($error): ?>
            <div class="alerta-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="grupo">
            <label>Nombre de usuario</label>
            <input type="text" name="nombre" required autofocus>
        </div>

        <div class="grupo">
            <label>Contraseña</label>
            <input type="password" name="password" required>
        </div>

        <div class="grupo-enlace-derecha">
            <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn-comprar">Ingresar</button>

        <p style="text-align:center; margin-top:20px;">
            ¿No tienes cuenta? <a href="registro.php" style="color:var(--azul); font-weight:600;">Regístrate aquí</a>
        </p>

    </form>

</section>

<?php include "includes/footer.php"; ?>