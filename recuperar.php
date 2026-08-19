<?php
require_once "includes/conexion.php";
include "includes/header.php";
include "includes/navbar.php";
?>

<section class="finalizar-compra">
    <div class="titulo-seccion">
        <h2>Recuperar Contraseña</h2>
        <p>Ingresa tu correo electrónico registrado y te enviaremos las instrucciones.</p>
    </div>

    <form action="enviar_correo.php" method="POST" class="formulario-compra">
        <div class="grupo">
            <label>Correo electrónico</label>
            <input type="email" name="correo" required autofocus>
        </div>
        
        <button type="submit" class="btn-comprar">Enviar enlace</button>

        <p style="text-align:center; margin-top:20px;">
            <a href="login.php" style="color:var(--azul); font-weight:600;">Volver al inicio de sesión</a>
        </p>
    </form>
</section>

<?php include "includes/footer.php"; ?>