<?php

include "includes/header.php";
include "includes/navbar.php";

?>

<section class="finalizar-compra">

    <div class="titulo-seccion">

        <h2>Finalizar Compra</h2>

        <p>

            Complete los siguientes datos para registrar su pedido.

        </p>

    </div>

    <form action="guardar_pedido.php" method="POST" class="formulario-compra">

        <div class="grupo">

         <label>Nombres</label>

         <input
        type="text"
        name="nombres"
        required>

       </div>

       <div class="grupo">

       <label>Apellidos</label>

       <input
        type="text"
        name="apellidos"
        required>

        </div>

        <div class="grupo">

            <label>Correo Electrónico</label>

            <input
            type="email"
            name="correo"
            required>

        </div>

        <div class="grupo">

            <label>Teléfono</label>

            <input
            type="text"
            name="telefono"
            required>

        </div>

        <div class="grupo">

            <label>Dirección</label>

            <textarea
            name="direccion"
            rows="4"
            required></textarea>

        </div>

        <div class="grupo">

            <label>Método de pago</label>

            <select name="pago">

                <option>Efectivo</option>

                <option>Transferencia</option>

                <option>Tarjeta</option>

            </select>

        </div>

        <button
        type="submit"
        class="btn-comprar">

            Confirmar Pedido

        </button>

    </form>

</section>

<?php

include "includes/footer.php";

?>