<?php

require_once "includes/conexion.php";
require_once "includes/funciones.php";

include "includes/header.php";
include "includes/navbar.php";

$pedido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pedido = null;

if ($pedido_id > 0) {

    $resultado = mysqli_query($conexion, "SELECT * FROM pedidos WHERE id = $pedido_id");
    $pedido    = mysqli_fetch_assoc($resultado);

}

?>

<section class="carrito">

    <div class="carrito-vacio">

        <i class="fa-solid fa-circle-check" style="color:var(--verde);"></i>

        <h2>¡Gracias por tu pedido!</h2>

        <?php if ($pedido): ?>

            <p>
                Tu pedido <strong>#<?php echo $pedido['id']; ?></strong> fue registrado
                correctamente por un total de <strong><?php echo moneda($pedido['total']); ?></strong>.
            </p>

            <p>Nos pondremos en contacto contigo para coordinar la entrega.</p>

        <?php else: ?>

            <p>Tu pedido fue registrado correctamente.</p>

        <?php endif; ?>

        <a href="productos.php" class="btn" style="margin-top:20px; display:inline-block;">
            Seguir comprando
        </a>

    </div>

</section>

<?php include "includes/footer.php"; ?>