<?php

session_start();
require_once "../includes/conexion.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($correo === "" || $password === "") {

        $error = "Ingresa tu correo y contraseña.";

    } else {

        $correoEsc = mysqli_real_escape_string($conexion, $correo);

        $resultado = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo = '$correoEsc' AND estado = 1");
        $usuario   = mysqli_fetch_assoc($resultado);

        if ($usuario && password_verify($password, $usuario['password'])) {

            $_SESSION['usuario_id']      = $usuario['id'];
            $_SESSION['usuario_nombres'] = $usuario['nombres'];
            $_SESSION['usuario_rol']     = $usuario['rol'];

            header("Location: dashboard.php");
            exit;

        } else {

            $error = "Correo o contraseña incorrectos, o usuario inactivo.";

        }

    }

}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Administrativo - Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
</head>
<body>

<div class="login-caja">

    <h2>Panel Administrativo</h2>
    <p>Mundo Escolar</p>

    <?php if ($error): ?>
        <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Correo</label>
        <input type="email" name="correo" required autofocus>

        <label>Contraseña</label>
        <input type="password" name="password" required>

        <button type="submit">Ingresar</button>

    </form>

</div>

</body>
</html>