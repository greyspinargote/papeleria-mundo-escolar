<?php

require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

$productos = mysqli_query($conexion, "SELECT id, nombre, precio, stock FROM productos WHERE estado = 1 AND stock > 0 ORDER BY nombre ASC");
$clientes  = mysqli_query($conexion, "SELECT id, nombres, apellidos, correo FROM clientes ORDER BY nombres ASC");

/* Pasamos los productos también como JSON para usarlos en JavaScript */

$productosJS = [];

mysqli_data_seek($productos, 0);

while ($p = mysqli_fetch_assoc($productos)) {
    $productosJS[] = $p;
}

mysqli_data_seek($productos, 0);

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ventas - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Registrar Venta</h1>

            <div class="usuario-actual">
                <i class="fa-solid fa-circle-user"></i>
                <?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?>
                <span class="badge-rol"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
            </div>

        </div>

        <?php if (isset($_GET['exito'])): ?>
            <div class="mensaje-exito">Venta #<?php echo (int)$_GET['exito']; ?> registrada correctamente.</div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje-error">Ocurrió un error al registrar la venta. Verifica el stock disponible.</div>
        <?php endif; ?>

        <div class="tarjeta-panel">

            <form method="POST" action="procesar_venta.php" id="formVenta">

                <div class="campo">
                    <label>Cliente (opcional)</label>
                    <select name="cliente_id">
                        <option value="">-- Cliente sin registrar (mostrador) --</option>
                        <?php while ($c = mysqli_fetch_assoc($clientes)) { ?>
                            <option value="<?php echo $c['id']; ?>">
                                <?php echo htmlspecialchars($c['nombres'] . ' ' . $c['apellidos'] . ' - ' . $c['correo']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

                <div class="campo">
                    <label>Agregar producto</label>

                    <div style="display:flex; gap:10px;">

                        <select id="selectorProducto" style="flex:2;">
                            <option value="">-- Selecciona un producto --</option>
                            <?php foreach ($productosJS as $p) { ?>
                                <option
                                    value="<?php echo $p['id']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($p['nombre']); ?>"
                                    data-precio="<?php echo $p['precio']; ?>"
                                    data-stock="<?php echo $p['stock']; ?>">
                                    <?php echo htmlspecialchars($p['nombre']); ?> — <?php echo moneda($p['precio']); ?> (stock: <?php echo $p['stock']; ?>)
                                </option>
                            <?php } ?>
                        </select>

                        <input type="number" id="cantidadProducto" min="1" value="1" style="flex:1; padding:12px; border:1px solid #ccc; border-radius:8px;">

                        <button type="button" id="btnAgregarItem" class="btn-panel">
                            <i class="fa-solid fa-plus"></i> Agregar
                        </button>

                    </div>

                </div>

                <table class="tabla-panel" style="margin-top:20px;">

                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>

                    <tbody id="cuerpoCarritoVenta">
                        <tr id="filaVacia">
                            <td colspan="5" style="text-align:center; color:#888; padding:20px;">
                                Todavía no has agregado productos.
                            </td>
                        </tr>
                    </tbody>

                </table>

                <div style="text-align:right; margin-top:20px; font-size:22px; font-weight:700; color:#0A4DA3;">
                    Total: $<span id="totalVenta">0.00</span>
                </div>

                <input type="hidden" name="carrito_json" id="carritoJson">

                <button type="submit" class="btn-panel" id="btnConfirmarVenta" style="margin-top:20px;" disabled>
                    <i class="fa-solid fa-cash-register"></i> Confirmar Venta
                </button>

            </form>

        </div>

    </div>

</div>

<script>

let carrito = [];

const selectorProducto = document.getElementById('selectorProducto');
const cantidadProducto = document.getElementById('cantidadProducto');
const cuerpoCarrito     = document.getElementById('cuerpoCarritoVenta');
const totalVentaSpan    = document.getElementById('totalVenta');
const carritoJsonInput  = document.getElementById('carritoJson');
const btnConfirmarVenta = document.getElementById('btnConfirmarVenta');

document.getElementById('btnAgregarItem').addEventListener('click', function () {

    const opcion = selectorProducto.options[selectorProducto.selectedIndex];

    if (!opcion.value) {
        alert('Selecciona un producto.');
        return;
    }

    const id      = opcion.value;
    const nombre  = opcion.dataset.nombre;
    const precio  = parseFloat(opcion.dataset.precio);
    const stock   = parseInt(opcion.dataset.stock);
    const cantidad = parseInt(cantidadProducto.value);

    if (cantidad < 1 || isNaN(cantidad)) {
        alert('Ingresa una cantidad válida.');
        return;
    }

    const existente = carrito.find(item => item.id === id);
    const cantidadActual = existente ? existente.cantidad : 0;

    if (cantidadActual + cantidad > stock) {
        alert('No hay suficiente stock. Disponible: ' + stock);
        return;
    }

    if (existente) {
        existente.cantidad += cantidad;
    } else {
        carrito.push({ id, nombre, precio, cantidad });
    }

    renderizarCarrito();

});

function renderizarCarrito() {

    if (carrito.length === 0) {
        cuerpoCarrito.innerHTML = '<tr id="filaVacia"><td colspan="5" style="text-align:center; color:#888; padding:20px;">Todavía no has agregado productos.</td></tr>';
        btnConfirmarVenta.disabled = true;
        totalVentaSpan.textContent = '0.00';
        return;
    }

    let html  = '';
    let total = 0;

    carrito.forEach((item, index) => {

        const subtotal = item.precio * item.cantidad;
        total += subtotal;

        html += `<tr>
            <td>${item.nombre}</td>
            <td>${item.cantidad}</td>
            <td>$${item.precio.toFixed(2)}</td>
            <td>$${subtotal.toFixed(2)}</td>
            <td><a href="#" onclick="quitarItem(${index}); return false;" style="color:#dc3545;">Quitar</a></td>
        </tr>`;

    });

    cuerpoCarrito.innerHTML = html;
    totalVentaSpan.textContent = total.toFixed(2);
    carritoJsonInput.value = JSON.stringify(carrito);
    btnConfirmarVenta.disabled = false;

}

function quitarItem(index) {
    carrito.splice(index, 1);
    renderizarCarrito();
}

document.getElementById('formVenta').addEventListener('submit', function (e) {

    if (carrito.length === 0) {
        e.preventDefault();
        alert('Agrega al menos un producto antes de confirmar la venta.');
        return;
    }

    carritoJsonInput.value = JSON.stringify(carrito);

});

</script>

</body>
</html>