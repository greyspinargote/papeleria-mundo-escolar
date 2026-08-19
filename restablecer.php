<?php
require_once "includes/conexion.php";
include "includes/header.php";
include "includes/navbar.php";

$token = $_GET['token'] ?? '';
$valido = false;

if ($token) {
    $tokenEsc = mysqli_real_escape_string($conexion, $token);
    $resultado = mysqli_query($conexion, "SELECT id FROM clientes WHERE token_recuperacion = '$tokenEsc' AND token_expiracion > NOW()");
    if (mysqli_num_rows($resultado) > 0) {
        $valido = true;
    }
}
?>

<section class="finalizar-compra">
    <div class="titulo-seccion">
        <h2>Nueva Contraseña</h2>
        <p>Ingresa tu nueva contraseña para tu cuenta.</p>
    </div>

    <?php if ($valido): ?>
        <form action="actualizar_password.php" method="POST" class="formulario-compra">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="grupo">
                <label>Nueva contraseña</label>
                <input type="password" name="nueva_password" required autofocus>
            </div>
            
            <button type="submit" class="btn-comprar">Actualizar contraseña</button>
        </form>
    <?php else: ?>
        <div style="text-align:center; padding: 20px;">
            <p class="alerta-error">El enlace es inválido o ha expirado.</p>
            <a href="login.php" style="color: var(--azul); font-weight: 600;">Ir al inicio de sesión</a>
        </div>
    <?php endif; ?>
</section>

<?php include "includes/footer.php"; ?>