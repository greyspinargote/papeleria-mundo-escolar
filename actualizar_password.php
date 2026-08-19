<?php
require_once "includes/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $nueva_password = password_hash($_POST['nueva_password'], PASSWORD_DEFAULT);

    $tokenEsc = mysqli_real_escape_string($conexion, $token);
    $passwordEsc = mysqli_real_escape_string($conexion, $nueva_password);

    $sql = "UPDATE clientes SET password = '$passwordEsc', token_recuperacion = NULL, token_expiracion = NULL WHERE token_recuperacion = '$tokenEsc' AND token_expiracion > NOW()";
    
    if (mysqli_query($conexion, $sql)) {
        echo "<script>alert('Contraseña actualizada con éxito. Ya puedes iniciar sesión.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Hubo un error al actualizar la contraseña.'); window.history.back();</script>";
    }
}
?>