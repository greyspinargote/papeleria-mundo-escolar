<?php

require_once "includes/conexion.php";

include "includes/header.php";
include "includes/navbar.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo   = trim($_POST['nombre'] ?? '');
    $password = $_POST['nombre'] ?? '';

    if ($correo === "" || $password === "") {

        $error = "Ingresa tu nombre y contraseña.";

    } else {

        $correoEsc = mysqli_real_escape_string($conexion, $nombre);

        $resultado = mysqli_query($conexion, "SELECT * FROM clientes WHERE correo = '$correoEsc'");
        $cliente   = mysqli_fetch_assoc($resultado);

        if ($cliente && password_verify($password, $cliente['password'])) {

            $_SESSION['cliente_id']     = $cliente['id'];
            $_SESSION['cliente_nombre'] = $cliente['nombres'];

            header("Location: index.php");
            exit;

        } else {

            $error = "Correo o contraseña incorrectos.";

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

            <div style="background:#f8d7da; color:#721c24; padding:14px; border-radius:8px; margin-bottom:20px;">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <div class="grupo">
            <label>Correo electrónico</label>
            <input type="email" name="correo" required autofocus>
        </div>

        <div class="grupo">
            <label>Contraseña</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn-comprar">Ingresar</button>

        <p style="text-align:center; margin-top:20px;">
            ¿No tienes cuenta? <a href="registro.php" style="color:var(--azul); font-weight:600;">Regístrate aquí</a>
        </p>

    </form>

</section>

<?php include "includes/footer.php"; ?>