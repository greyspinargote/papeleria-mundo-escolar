<?php

$totalCarrito = 0;

if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $itemCarrito) {
        $totalCarrito += $itemCarrito['cantidad'];
    }
}

?>

<header>

<div class="topbar">

<div>

<i class="fa-solid fa-truck"></i>

Envíos a todo Ecuador

</div>

</div>

<nav class="navbar">

<a href="index.php" class="logo">

<img src="assets/img/logo.png">

<span>Mundo Escolar</span>

</a>

<ul class="menu">

<li>

<a href="index.php">

Inicio

</a>

</li>

<li>

<a href="productos.php">

Productos

</a>

</li>

<li>

<a href="nosotros.php">

Nosotros

</a>

</li>

<li>

<a href="contacto.php">

Contacto

</a>

</li>

</ul>

<div class="acciones">

<form class="buscar" action="productos.php" method="GET">

<input
type="text"
id="buscarNav"
name="buscar"
placeholder="Buscar productos..."
autocomplete="off">

<button type="submit">

<i class="fa fa-search"></i>

</button>

</form>

<a href="carrito.php" class="carrito-icono">

    <i class="fa-solid fa-cart-shopping"></i>

    <span id="contadorCarrito"><?php echo $totalCarrito; ?></span>

</a>

<?php if (isset($_SESSION['cliente_id'])): ?>

    <div class="acciones-cliente" style="display:flex; align-items:center; gap:12px;">

        <span style="color:var(--azul); font-weight:600;">
            <i class="fa-solid fa-circle-user"></i>
            Hola, <?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?>
        </span>

        <a href="logout.php" class="login">Cerrar sesión</a>

    </div>

<?php else: ?>

    <a href="login.php" class="login">Iniciar sesión</a>

<?php endif; ?>

</div>

</nav>

</header>