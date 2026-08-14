<?php

require_once "includes/conexion.php";
require_once "includes/funciones.php";

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

$productos = obtenerProductos($conexion, $busqueda);

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

    <!-- BUSCADOR -->
    <div class="buscador-productos">

    <div class="caja-buscador">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
            type="text"
            id="buscarProducto"
            placeholder="Buscar productos..."
            value="<?php echo htmlspecialchars($busqueda); ?>"
            autocomplete="off">

    </div>

</div>

    <!-- CONTENEDOR DE PRODUCTOS -->
    <div class="grid-productos" id="listaProductos">

        <?php if ($productos && mysqli_num_rows($productos) > 0): ?>

            <?php while ($producto = mysqli_fetch_assoc($productos)): ?>

                <div class="producto">

                    <!-- IMAGEN -->
                    <div class="imagen-producto">

                        <img
                            src="assets/img/productos/<?php echo htmlspecialchars($producto['imagen'] ?? ''); ?>"
                            alt="<?php echo htmlspecialchars($producto['nombre']); ?>">

                    </div>

                    <!-- INFORMACIÓN -->
                    <div class="info-producto">

                        <h3>
                            <?php echo htmlspecialchars($producto['nombre']); ?>
                        </h3>

                        <p class="descripcion-producto">

                            <?php

                            $descripcion = $producto['descripcion'] ?? '';

                            if (strlen($descripcion) > 80) {
                                echo htmlspecialchars(substr($descripcion, 0, 80)) . "...";
                            } else {
                                echo htmlspecialchars($descripcion);
                            }

                            ?>

                        </p>

                        <div class="precio-producto">

                            <?php echo moneda($producto['precio']); ?>

                        </div>

                        <!-- STOCK -->
                        <?php if ($producto['stock'] > 0): ?>

                            <p class="stock disponible">

                                <i class="fa-solid fa-circle-check"></i>

                                Disponible:
                                <?php echo (int)$producto['stock']; ?>

                            </p>

                        <?php else: ?>

                            <p class="stock agotado">

                                <i class="fa-solid fa-circle-xmark"></i>

                                Producto agotado

                            </p>

                        <?php endif; ?>

                        <!-- ACCIONES -->
                        <div class="acciones-producto">

                            <a
                                href="detalle_producto.php?id=<?php echo (int)$producto['id']; ?>"
                                class="btn btn-detalle">

                                <i class="fa-solid fa-eye"></i>

                                Ver detalle

                            </a>

                            <?php if ($producto['stock'] > 0): ?>

                                <a
                                    href="agregar_carrito.php?id=<?php echo (int)$producto['id']; ?>"
                                    class="btn btn-carrito">

                                    <i class="fa-solid fa-cart-shopping"></i>

                                    Agregar

                                </a>

                            <?php else: ?>

                                <button
                                    type="button"
                                    class="btn btn-agotado"
                                    disabled>

                                    <i class="fa-solid fa-ban"></i>

                                    Agotado

                                </button>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="sin-productos">

    <i class="fa-solid fa-box-open"></i>

    <?php if ($busqueda !== ""): ?>

        <h3>No se encontraron productos para "<?php echo htmlspecialchars($busqueda); ?>"</h3>

        <p>Intenta con otra palabra o revisa el catálogo completo.</p>

    <?php else: ?>

        <h3>No hay productos disponibles</h3>

        <p>
            En este momento no tenemos productos disponibles en el catálogo.
        </p>

    <?php endif; ?>

</div>

        <?php endif; ?>

    </div>

</section>

<?php

include "includes/footer.php";

?>