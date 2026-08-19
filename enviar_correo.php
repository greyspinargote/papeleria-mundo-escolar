<?php
require_once "includes/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $correoEsc = mysqli_real_escape_string($conexion, $correo);

    $resultado = mysqli_query($conexion, "SELECT id FROM clientes WHERE correo = '$correoEsc'");
    $cliente = mysqli_fetch_assoc($resultado);

    if ($cliente) {
        $token = bin2hex(random_bytes(32));
        $expiracion = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Guardamos el token en la base de datos
        mysqli_query($conexion, "UPDATE clientes SET token_recuperacion = '$token', token_expiracion = '$expiracion' WHERE correo = '$correoEsc'");

        // EN VEZ DE ENVIAR CORREO (que falla en local), REDIRIGIMOS DIRECTAMENTE A LA VISTA DE RESTABLECER:
        header("Location: restablecer.php?token=" . $token);
        exit;

    } else {
        echo "<script>alert('El correo electrónico no está registrado.'); window.history.back();</script>";
    }
}
?>