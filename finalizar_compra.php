<?php

require_once "includes/conexion.php";
require_once "includes/funciones.php";

include "includes/header.php";
include "includes/navbar.php";

/* Si el cliente inició sesión, precargamos sus datos */

$datosCliente = null;

if (isset($_SESSION['cliente_id'])) {

    $idCliente = (int)$_SESSION['cliente_id'];

    $resultado = mysqli_query($conexion, "SELECT * FROM clientes WHERE id = $idCliente");
    $datosCliente = mysqli_fetch_assoc($resultado);

}

/* Si el carrito está vacío, no tiene sentido mostrar el formulario */

$carritoVacio = !isset($_SESSION['carrito']) || count($_SESSION['carrito']) === 0;

?>

<section class="finalizar-compra">

    <div class="titulo-seccion">

        <h2>Finalizar Compra</h2>

        <p>
            Complete los siguientes datos para registrar su pedido.
        </p>

    </div>

    <?php if ($carritoVacio): ?>

        <div class="carrito-vacio">

            <i class="fa-solid fa-cart-shopping"></i>

            <h3>Tu carrito está vacío</h3>

            <p>Agrega productos antes de finalizar la compra.</p>

            <a href="productos.php" class="btn">Ver productos</a>

        </div>

    <?php else: ?>

    <form action="guardar_pedido.php" method="POST" class="formulario-compra">

        <div class="grupo">

         <label>Nombres</label>

         <input
        type="text"
        name="nombres"
        value="<?php echo $datosCliente ? htmlspecialchars($datosCliente['nombres']) : ''; ?>"
        required>

       </div>

       <div class="grupo">

       <label>Apellidos</label>

       <input
        type="text"
        name="apellidos"
        value="<?php echo $datosCliente ? htmlspecialchars($datosCliente['apellidos']) : ''; ?>"
        required>

        </div>

        <div class="grupo">

            <label>Correo Electrónico</label>

            <input
            type="email"
            name="correo"
            value="<?php echo $datosCliente ? htmlspecialchars($datosCliente['correo']) : ''; ?>"
            required>

        </div>

        <div class="grupo">

            <label>Teléfono</label>

            <input
            type="text"
            name="telefono"
            value="<?php echo $datosCliente ? htmlspecialchars($datosCliente['telefono']) : ''; ?>"
            required>

        </div>

        <div class="grupo">

            <label>Dirección</label>

            <textarea
            name="direccion"
            rows="4"
            required><?php echo $datosCliente ? htmlspecialchars($datosCliente['direccion']) : ''; ?></textarea>

        </div>

        <div class="grupo">

            <label>Método de pago</label>

            <select name="pago" id="metodoPago" required>
                <option value="">Seleccione...</option>
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia</option>
                <option value="Tarjeta">Tarjeta</option>
            </select>

        </div>

        <!-- Contenedor dinámico para los datos de la tarjeta -->
        <div id="contenedorTarjeta" class="contenedor-tarjeta-oculto">

            <h3 class="subtitulo-tarjeta">Datos de la Tarjeta</h3>

            <div class="grupo">
                <label>Número de tarjeta</label>
                <input type="text" name="numero_tarjeta" placeholder="1234 5678 9012 3456" maxlength="16">
            </div>

            <div class="grupo">
                <label>Fecha de expiración</label>
                <input type="text" name="expiracion_tarjeta" placeholder="MM/AA" maxlength="5">
            </div>

            <div class="grupo">
                <label>CVV</label>
                <input type="password" name="cvv_tarjeta" placeholder="123" maxlength="4">
            </div>

            <div class="grupo">
                <label>Nombre del titular</label>
                <input type="text" name="titular_tarjeta" placeholder="Como aparece en la tarjeta">
            </div>

        </div>

        <button
        type="submit"
        class="btn-comprar">

            Confirmar Pedido

        </button>

    </form>

    <?php endif; ?>

</section>

<script>
document.getElementById('metodoPago').addEventListener('change', function() {
    var contenedorTarjeta = document.getElementById('contenedorTarjeta');
    
    if (this.value === 'Tarjeta') {
        contenedorTarjeta.style.display = 'block';
    } else {
        contenedorTarjeta.style.display = 'none';
        
        // Limpiamos los campos si cambia de opinión
        document.querySelector('input[name="numero_tarjeta"]').value = '';
        document.querySelector('input[name="expiracion_tarjeta"]').value = '';
        document.querySelector('input[name="cvv_tarjeta"]').value = '';
        document.querySelector('input[name="titular_tarjeta"]').value = '';
    }
});
</script>

<?php

include "includes/footer.php";

?>