<?php

require_once "includes/conexion.php";
require_once "includes/funciones.php";

$productos = obtenerProductos($conexion);

include "includes/header.php";
include "includes/navbar.php";

?>

<section class="productos">

    <div class="titulo-seccion">

        <h2>Catálogo de Productos</h2>

        <p>
            Encuentra todo lo que necesitas para la escuela, colegio, universidad y oficina.
        </p>

    </div>

    <div class="buscador-productos">

        <input
            type="text"
            id="buscarProducto"
            placeholder="Buscar productos...">

    </div>

    <div class="grid-productos">

        <?php while($producto = mysqli_fetch_assoc($productos)){ ?>

            <div class="producto">

                <img
                    src="assets/img/productos/<?php echo $producto['imagen']; ?>"
                    alt="<?php echo $producto['nombre']; ?>">

                <h3>

                    <?php echo $producto['nombre']; ?>

                </h3>

                <span>

                    <?php echo moneda($producto['precio']); ?>

                </span>

                <div class="acciones-producto">

                    <a
                        href="detalle_producto.php?id=<?php echo $producto['id']; ?>"
                        class="btn">

                        Ver detalle

                    </a>

                    <button
                       <a href="agregar_carrito.php?id=<?php echo $producto['id']; ?>" class="btn">

                        <i class="fa-solid fa-cart-shopping"></i>

                        Agregar al carrito

                    </a>

                    </button>

                </div>

            </div>

        <?php } ?>

    </div>

</section>

<?php

include "includes/footer.php";

?>