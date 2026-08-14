<?php

require_once "includes/conexion.php";
require_once "includes/funciones.php";

$categorias = obtenerCategorias($conexion);
$destacados = obtenerDestacados($conexion);

include "includes/header.php";
include "includes/navbar.php";

?>

<!-- SLIDER -->

<section class="banner">

    <div class="slider">

        <div class="slide active">

            <img src="assets/img/banner1.jpg" alt="banner1">

        </div>

        <div class="slide">

            <img src="assets/img/banner2.jpg" alt="banner2">

        </div>

        <div class="slide">

            <img src="assets/img/banner3.jpg" alt="banner3">

        </div>

        <div class="texto-banner">

            <h1>Todo para tu regreso a clases</h1>

            <p>

                Encuentra útiles escolares, material de oficina,
                mochilas, tecnología y mucho más.

            </p>

            <a href="productos.php" class="btn">

                Comprar ahora

            </a>

        </div>

    </div>

</section>

<!-- CATEGORÍAS -->

<section class="categorias">

    <div class="titulo-seccion">

        <h2>Nuestras Categorías</h2>

        <p>

            Explora nuestras principales categorías.

        </p>

    </div>

    <div class="contenedor-categorias">

        <?php while($categoria=mysqli_fetch_assoc($categorias)){ ?>

            <div class="categoria">

                <i class="fa-solid fa-folder-open"></i>

                <h3>

                    <?php echo $categoria['nombre']; ?>

                </h3>

            </div>

        <?php } ?>

    </div>

</section>

<!-- PRODUCTOS DESTACADOS -->

<section class="productos">

    <div class="titulo-seccion">

        <h2>Productos Destacados</h2>

        <p>

            Los productos más vendidos.

        </p>

    </div>

    <div class="grid-productos">

            <?php while($producto = mysqli_fetch_assoc($destacados)){ ?>

        <div class="producto">

            <img src="assets/img/productos/<?php echo $producto['imagen']; ?>"
                 alt="<?php echo $producto['nombre']; ?>">

            <h3>

                <?php echo $producto['nombre']; ?>

            </h3>

            <span>

                <?php echo moneda($producto['precio']); ?>

            </span>

            <a href="agregar_carrito.php?id=<?php echo $producto['id']; ?>" class="btn-agregar-destacado">

    <i class="fa-solid fa-cart-shopping"></i>

    Agregar al carrito

</a>
        </div>

        <?php } ?>

    </div>

</section>

<!-- BENEFICIOS -->

<section class="beneficios">

    <div class="beneficio">

        <i class="fa-solid fa-truck-fast"></i>

        <h3>Envíos Rápidos</h3>

        <p>

            Entregamos tus productos de forma segura y rápida.

        </p>

    </div>

    <div class="beneficio">

        <i class="fa-solid fa-shield-halved"></i>

        <h3>Compra Segura</h3>

        <p>

            Tu información está protegida durante todo el proceso.

        </p>

    </div>

    <div class="beneficio">

        <i class="fa-solid fa-tags"></i>

        <h3>Mejores Precios</h3>

        <p>

            Ofertas permanentes en útiles escolares y de oficina.

        </p>

    </div>

    <div class="beneficio">

        <i class="fa-solid fa-headset"></i>

        <h3>Atención al Cliente</h3>

        <p>

            Estamos disponibles para ayudarte cuando lo necesites.

        </p>

    </div>

</section>

<!-- NOSOTROS -->

<section class="nosotros">

    <div class="contenido-nosotros">

        <div class="texto">

            <h2>¿Quiénes Somos?</h2>

            <p>

                Mundo Escolar es una papelería dedicada a ofrecer
                útiles escolares, artículos de oficina,
                tecnología educativa y accesorios de calidad,
                brindando una excelente atención y precios accesibles.

            </p>

            <a href="nosotros.php" class="btn">

                Conocer más

            </a>

        </div>

        <div class="imagen">

            <img src="assets/img/nosotros.jpg" alt="Nosotros">

        </div>

    </div>

</section>

<!-- CONTACTO -->

<section class="contacto">

    <h2>Contáctanos</h2>

    <p>

        Estamos listos para atender todas tus consultas.

    </p>

    <div class="datos-contacto">

        <div>

            <i class="fa-solid fa-location-dot"></i>

            <h3>Dirección</h3>

            <p>Santo Domingo - Ecuador</p>

        </div>

        <div>

            <i class="fa-solid fa-phone"></i>

            <h3>Teléfono</h3>

            <p>0982852504</p>

        </div>

        <div>

            <i class="fa-solid fa-envelope"></i>

            <h3>Correo</h3>

            <p>contacto@mundoescolar.com</p>

        </div>

    </div>

</section>

<?php

include "includes/footer.php";

?>