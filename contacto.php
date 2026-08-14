<?php

require_once "includes/conexion.php";

include "includes/header.php";
include "includes/navbar.php";

$mensaje_enviado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre  = trim($_POST['nombre'] ?? '');
    $correo  = trim($_POST['correo'] ?? '');
    $asunto  = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($nombre !== "" && $correo !== "" && $mensaje !== "") {

        // Aquí más adelante se puede guardar en una tabla "mensajes"
        // o enviar por correo con PHPMailer.

        $mensaje_enviado = true;

    }

}

?>

<!-- CONTACTO -->

<section class="contacto">

    <h2>Contáctanos</h2>

    <p>
        ¿Tienes alguna pregunta? Escríbenos y te responderemos lo antes posible.
    </p>

    <div class="datos-contacto">

        <div>

            <i class="fa-solid fa-location-dot"></i>

            <h3>Dirección</h3>

            <p>Av. Principal, Santo Domingo de los Tsáchilas</p>

        </div>

        <div>

            <i class="fa-solid fa-phone"></i>

            <h3>Teléfono</h3>

            <p>+593 99 999 9999</p>

        </div>

        <div>

            <i class="fa-solid fa-envelope"></i>

            <h3>Correo</h3>

            <p>contacto@mundoescolar.com</p>

        </div>

        <div>

            <i class="fa-solid fa-clock"></i>

            <h3>Horario</h3>

            <p>Lunes a Sábado: 8:00 am - 6:00 pm</p>

        </div>

    </div>

    <div class="formulario-compra" style="margin-top:50px; text-align:left;">

        <?php if ($mensaje_enviado): ?>

            <div class="mensaje-exito" style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;">
                ¡Gracias! Tu mensaje fue enviado correctamente. Te contactaremos pronto.
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="grupo">
                <label>Nombre completo</label>
                <input type="text" name="nombre" required>
            </div>

            <div class="grupo">
                <label>Correo electrónico</label>
                <input type="email" name="correo" required>
            </div>

            <div class="grupo">
                <label>Asunto</label>
                <input type="text" name="asunto">
            </div>

            <div class="grupo">
                <label>Mensaje</label>
                <textarea name="mensaje" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn-comprar">Enviar mensaje</button>

        </form>

    </div>

</section>

<?php include "includes/footer.php"; ?>