<?php

require_once "includes/conexion.php";
require_once "includes/funciones.php";

/*
|--------------------------------------------------------------------------
| VALIDAR ID DEL PRODUCTO
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: productos.php");
    exit();

}

$id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| BUSCAR PRODUCTO
|--------------------------------------------------------------------------
*/

$resultado = obtenerProducto($conexion, $id);

if (!$resultado || mysqli_num_rows($resultado) === 0) {

    header("Location: productos.php");
    exit();

}

$producto = mysqli_fetch_assoc($resultado);

/*
|--------------------------------------------------------------------------
| DATOS DEL PRODUCTO
|--------------------------------------------------------------------------
*/

$nombre = htmlspecialchars($producto['nombre']);

$descripcion = htmlspecialchars(
    $producto['descripcion'] ?? 'Sin descripción disponible.'
);

$imagen = htmlspecialchars(
    $producto['imagen'] ?? ''
);

$precio = moneda($producto['precio']);

$stock = (int)$producto['stock'];

include "includes/header.php";
include "includes/navbar.php";

?>

<section class="detalle-producto">

    <div class="contenedor-detalle">

        <!-- IMAGEN DEL PRODUCTO -->
        <div class="imagen-detalle">

            <img
                src="assets/img/productos/<?php echo $imagen; ?>"
                alt="<?php echo $nombre; ?>">

        </div>

        <!-- INFORMACIÓN DEL PRODUCTO -->
        <div class="informacion-detalle">

            <span class="etiqueta-producto">
                Mundo Escolar
            </span>

            <h1>
                <?php echo $nombre; ?>
            </h1>

            <div class="precio-detalle">

                <?php echo $precio; ?>

            </div>

            <div class="separador"></div>

            <h3>
                Descripción
            </h3>

            <p class="descripcion-detalle">

                <?php echo $descripcion; ?>

            </p>

            <!-- STOCK -->
            <?php if ($stock > 0): ?>

                <div class="stock-detalle disponible">

                    <i class="fa-solid fa-circle-check"></i>

                    <strong>Producto disponible</strong>

                    <span>
                        <?php echo $stock; ?> unidades disponibles
                    </span>

                </div>

            <?php else: ?>

                <div class="stock-detalle agotado">

                    <i class="fa-solid fa-circle-xmark"></i>

                    <strong>Producto agotado</strong>

                    <span>
                        Actualmente no tenemos unidades disponibles.
                    </span>

                </div>

            <?php endif; ?>

            <!-- BOTONES -->
            <div class="acciones-detalle">

                <?php if ($stock > 0): ?>

                    <a
                        href="agregar_carrito.php?id=<?php echo (int)$producto['id']; ?>"
                        class="btn btn-carrito">

                        <i class="fa-solid fa-cart-shopping"></i>

                        Agregar al carrito

                    </a>

                <?php else: ?>

                    <button
                        type="button"
                        class="btn btn-agotado"
                        disabled>

                        <i class="fa-solid fa-ban"></i>

                        Producto agotado

                    </button>

                <?php endif; ?>

                <a
                    href="productos.php"
                    class="btn btn-volver">

                    <i class="fa-solid fa-arrow-left"></i>

                    Volver al catálogo

                </a>

            </div>

        </div>

    </div>

</section>

<?php

include "includes/footer.php";

?>