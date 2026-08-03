<?php
session_start();

include "includes/header.php";
include "includes/navbar.php";

$total = 0;
?>

<section class="carrito">

    <div class="titulo-seccion">

        <h2>Mi Carrito de Compras</h2>

        <p>Revisa los productos antes de confirmar tu pedido.</p>

    </div>

    <?php if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0){ ?>

    <table class="tabla-carrito">

        <thead>

            <tr>

                <th>Imagen</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acción</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach($_SESSION['carrito'] as $producto){

                $subtotal = $producto['precio'] * $producto['cantidad'];

                $total += $subtotal;

            ?>

            <tr>

                <td>

                    <img
                    src="assets/img/productos/<?php echo $producto['imagen']; ?>"
                    width="80">

                </td>

                <td>

                    <?php echo $producto['nombre']; ?>

                </td>

                <td>

                    $ <?php echo number_format($producto['precio'],2); ?>

                </td>

                <td>

                    <?php echo $producto['cantidad']; ?>

                </td>

                <td>

                    $ <?php echo number_format($subtotal,2); ?>

                </td>

                <td>

                    <a
                    href="eliminar_carrito.php?id=<?php echo $producto['id']; ?>"
                    class="btn-eliminar">

                        Eliminar

                    </a>

                </td>

            </tr>

            <?php } ?>

        </tbody>

    </table>

    <div class="resumen-compra">

        <h2>

            Total: $

            <?php echo number_format($total,2); ?>

        </h2>

        <div class="botones-carrito">

            <a
            href="vaciar_carrito.php"
            class="btn-vaciar">

                Vaciar carrito

            </a>

            <a
            href="productos.php"
            class="btn-seguir">

                Seguir comprando

            </a>

            <a
            href="finalizar_compra.php"
            class="btn-finalizar">

                Finalizar compra

            </a>

        </div>

    </div>

    <?php }else{ ?>

    <div class="carrito-vacio">

        <i class="fa-solid fa-cart-shopping"></i>

        <h2>

            Tu carrito está vacío

        </h2>

        <a href="productos.php" class="btn">

            Ir a comprar

        </a>

    </div>

    <?php } ?>

</section>

<?php

include "includes/footer.php";

?>