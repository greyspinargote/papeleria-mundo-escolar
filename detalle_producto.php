<?php

require_once "includes/conexion.php";
require_once "includes/funciones.php";

if(!isset($_GET['id'])){

    header("Location: productos.php");
    exit();

}

$id = (int)$_GET['id'];

$resultado = obtenerProducto($conexion,$id);

if(mysqli_num_rows($resultado)==0){

    header("Location: productos.php");
    exit();

}

$producto = mysqli_fetch_assoc($resultado);

include "includes/header.php";
include "includes/navbar.php";

?>

<section class="detalle-producto">

    <div class="contenedor-detalle">

        <div class="imagen-detalle">

            <img src="assets/img/productos/<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">

        </div>

        <div class="informacion-detalle">

            <h2>

                <?php echo $producto['nombre']; ?>

            </h2>

            <h3>

                <?php echo moneda($producto['precio']); ?>

            </h3>

            <p>

                <?php echo $producto['descripcion']; ?>

            </p>

            <p>

                <strong>Stock disponible:</strong>

                <?php echo $producto['stock']; ?>

            </p>

            <button
            <a href="agregar_carrito.php?id=<?php echo $producto['id']; ?>" class="btn">

            <i class="fa-solid fa-cart-shopping"></i>

             Agregar al carrito

            </a>

            </button>

            <a href="productos.php" class="btn">

                Volver al catálogo

            </a>

        </div>

    </div>

</section>

<?php

include "includes/footer.php";

?>