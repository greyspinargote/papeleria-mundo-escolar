<?php

require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

$productos = mysqli_query($conexion, "SELECT id, nombre, precio, stock FROM productos WHERE estado = 1 AND stock > 0 ORDER BY nombre ASC");
$clientes  = mysqli_query($conexion, "SELECT id, nombres, apellidos, correo, telefono, direccion FROM clientes ORDER BY nombres ASC");

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
            <div>
                <h1>Registrar Venta</h1>
                <p class="subtitulo-encabezado">Gestión de ventas Mundo Escolar 👋</p>
            </div>

            <div class="usuario-actual">
                <div class="avatar-inicial">
                    <?php echo strtoupper(substr($_SESSION['usuario_nombres'], 0, 1)); ?>
                </div>
                <div class="info-usuario-header">
                    <span class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?></span>
                    <span class="rol-usuario-verde"><?php echo ucfirst(strtolower($_SESSION['usuario_rol'])); ?></span>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje-error">Ocurrió un error al registrar la venta. Verifica el stock disponible.</div>
        <?php endif; ?>

        <div class="tarjeta-panel">

            <form method="POST" action="procesar_venta.php" id="formVenta">

                <div class="campo">
                    <label><strong>Buscar y Agregar Producto</strong></label>

                    <!-- FILA 1: BUSCADOR EN LÍNEA + BOTÓN NUEVA VENTA -->
                    <div class="fila-buscador">
                        <input type="text" id="inputBuscarProducto" placeholder="Buscar producto por nombre..." class="input-estandar input-expandir">
                        <button type="button" class="btn-guardar-verde" onclick="limpiarVenta()">Nueva venta</button>
                    </div>

                    <!-- FILA 2: SELECTOR DE PRODUCTOS -->
                    <select id="selectorProducto" class="select-producto select-ancho-total">
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

                    <!-- FILA 3: CANTIDAD + BOTÓN AGREGAR (EN LA MISMA FILA) -->
                    <div class="fila-cantidad">
                        <input type="number" id="cantidadProducto" min="1" value="1" class="input-cantidad input-ancho-fijo">
                        <button type="button" id="btnAgregarItem" class="btn-guardar-verde">
                            <i class="fa-solid fa-plus"></i> Agregar
                        </button>
                    </div>

                </div>

                <table class="tabla-panel tabla-carrito">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoCarritoVenta">
                        <tr id="filaVacia">
                            <td colspan="5" class="texto-vacio-tabla">
                                Todavía no has agregado productos.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="grid-resumen-venta">
                    
                    <div class="tarjeta-resumen-box">
                        <h4 class="titulo-box">Datos del cliente</h4>
                        <div class="fila-cliente-selector">
                            <select name="cliente_id" id="selectCliente" class="input-estandar">
                                <option value="">Consumidor final</option>
                                <?php while ($c = mysqli_fetch_assoc($clientes)) { ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <button type="button" class="btn-guardar-verde" onclick="abrirModalCliente()">
                                <i class="fa-solid fa-plus"></i> Nuevo cliente
                            </button>
                        </div>

                        <div class="acciones-finalizar">
                            <button type="button" class="btn-cancelar" onclick="limpiarVenta()">Cancelar</button>
                            <button type="submit" class="btn-guardar-verde" id="btnConfirmarVenta" disabled>
                                Finalizar venta
                            </button>
                        </div>
                    </div>

                    <div class="tarjeta-resumen-box">
                        <h4 class="titulo-box">Totales y Pago</h4>
                        <div class="linea-resumen">
                            <span>Subtotal:</span>
                            <span>$<span id="lblSubtotal">0.00</span></span>
                        </div>
                        <div class="linea-resumen">
                            <span>IVA (15%):</span>
                            <span>$<span id="lblIva">0.00</span></span>
                        </div>
                        <hr class="divisor-totales">
                        <div class="linea-resumen total-destacado">
                            <span>Total:</span>
                            <span>$<span id="lblTotal">0.00</span></span>
                        </div>
                        <hr class="divisor-totales">
                        
                        <div class="linea-resumen">
                            <label for="inputPagaCon"><strong>Paga con ($):</strong></label>
                            <input type="number" step="0.01" min="0" id="inputPagaCon" class="input-estandar input-ancho-fijo" placeholder="0.00">
                        </div>
                        <div class="linea-resumen">
                            <span><strong>Cambio:</strong></span>
                            <span>$<strong><span id="lblCambio">0.00</span></strong></span>
                        </div>
                    </div>

                </div>

                <input type="hidden" name="carrito_json" id="carritoJson">

            </form>

            <?php if (isset($_GET['exito'])): ?>
                <div class="alerta-exito-panel">
                    <div class="titulo-alerta">
                        <i class="fa-solid fa-circle-check"></i> Venta registrada correctamente. <strong>N° de venta: #<?php echo (int)$_GET['exito']; ?></strong>
                    </div>
                    <div>
                        <a href="imprimir_comprobante.php?id=<?php echo (int)$_GET['exito']; ?>" target="_blank" class="btn-guardar-verde">
                            <i class="fa-solid fa-print"></i> Imprimir comprobante
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<!-- MODAL REGISTRO CLIENTE -->
<div id="modalCliente" class="modal-backdrop">
    <div class="modal-contenido">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-plus"></i> Registrar Nuevo Cliente</h3>
            <button type="button" class="btn-cerrar-modal" onclick="cerrarModalCliente()">&times;</button>
        </div>
        
        <form id="formNuevoClienteModal">
            <div class="campo-modal">
                <label>Nombres</label>
                <input type="text" id="modalNombres" required class="input-estandar">
            </div>
            <div class="campo-modal">
                <label>Apellidos</label>
                <input type="text" id="modalApellidos" required class="input-estandar">
            </div>
            <div class="campo-modal">
                <label>Teléfono</label>
                <input type="text" id="modalTelefono" class="input-estandar">
            </div>
            <div class="campo-modal">
                <label>Dirección</label>
                <input type="text" id="modalDireccion" class="input-estandar">
            </div>
            <div class="campo-modal">
                <label>Correo</label>
                <input type="email" id="modalCorreo" class="input-estandar">
            </div>
            <div class="modal-acciones">
                <button type="button" class="btn-cancelar" onclick="cerrarModalCliente()">Cancelar</button>
                <button type="submit" class="btn-guardar-verde">Guardar Cliente</button>
            </div>
        </form>
    </div>
</div>

<script>
let carrito = [];
let totalVentaActual = 0;

const selectorProducto     = document.getElementById('selectorProducto');
const inputBuscarProducto  = document.getElementById('inputBuscarProducto');
const cantidadProducto     = document.getElementById('cantidadProducto');
const cuerpoCarrito         = document.getElementById('cuerpoCarritoVenta');
const lblSubtotal           = document.getElementById('lblSubtotal');
const lblIva                = document.getElementById('lblIva');
const lblTotal              = document.getElementById('lblTotal');
const inputPagaCon          = document.getElementById('inputPagaCon');
const lblCambio             = document.getElementById('lblCambio');
const carritoJsonInput      = document.getElementById('carritoJson');
const btnConfirmarVenta     = document.getElementById('btnConfirmarVenta');

/* BÚSQUEDA DINÁMICA AL ESCRIBIR */
function ejecutarBusquedaProducto() {
    const filtro = inputBuscarProducto.value.toLowerCase().trim();
    const opciones = selectorProducto.options;
    let primerMatchEncontrado = false;

    for (let i = 1; i < opciones.length; i++) {
        const texto = opciones[i].text.toLowerCase();
        if (texto.includes(filtro)) {
            opciones[i].style.display = '';
            if (!primerMatchEncontrado && filtro !== '') {
                selectorProducto.selectedIndex = i;
                primerMatchEncontrado = true;
            }
        } else {
            opciones[i].style.display = 'none';
        }
    }

    if (filtro === '') {
        selectorProducto.selectedIndex = 0;
    }
}

inputBuscarProducto.addEventListener('keyup', ejecutarBusquedaProducto);

/* AGREGAR ITEM AL CARRITO */
document.getElementById('btnAgregarItem').addEventListener('click', function () {
    const opcion = selectorProducto.options[selectorProducto.selectedIndex];

    if (!opcion || !opcion.value) {
        alert('Selecciona un producto.');
        return;
    }

    const id       = opcion.value;
    const nombre   = opcion.dataset.nombre;
    const precio   = parseFloat(opcion.dataset.precio);
    const stock    = parseInt(opcion.dataset.stock);
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
        cuerpoCarrito.innerHTML = '<tr id="filaVacia"><td colspan="5" class="texto-vacio-tabla">Todavía no has agregado productos.</td></tr>';
        btnConfirmarVenta.disabled = true;
        lblSubtotal.textContent = '0.00';
        lblIva.textContent = '0.00';
        lblTotal.textContent = '0.00';
        lblCambio.textContent = '0.00';
        inputPagaCon.value = '';
        totalVentaActual = 0;
        return;
    }

    let html     = '';
    let subtotal = 0;

    carrito.forEach((item, index) => {
        const itemSubtotal = item.precio * item.cantidad;
        subtotal += itemSubtotal;

        html += `<tr>
            <td><strong>${item.nombre}</strong></td>
            <td>${item.cantidad}</td>
            <td>$${item.precio.toFixed(2)}</td>
            <td>$${itemSubtotal.toFixed(2)}</td>
            <td><a href="#" onclick="quitarItem(${index}); return false;" class="btn-quitar-item">Quitar</a></td>
        </tr>`;
    });

    const iva = subtotal * 0.15;
    totalVentaActual = subtotal + iva;

    cuerpoCarrito.innerHTML = html;
    lblSubtotal.textContent = subtotal.toFixed(2);
    lblIva.textContent = iva.toFixed(2);
    lblTotal.textContent = totalVentaActual.toFixed(2);

    calcularCambio();
    carritoJsonInput.value = JSON.stringify(carrito);
    btnConfirmarVenta.disabled = false;
}

function calcularCambio() {
    const pagaCon = parseFloat(inputPagaCon.value);
    if (!isNaN(pagaCon) && pagaCon >= totalVentaActual) {
        const cambio = pagaCon - totalVentaActual;
        lblCambio.textContent = cambio.toFixed(2);
    } else {
        lblCambio.textContent = '0.00';
    }
}

inputPagaCon.addEventListener('input', calcularCambio);

function quitarItem(index) {
    carrito.splice(index, 1);
    renderizarCarrito();
}

function limpiarVenta() {
    carrito = [];
    renderizarCarrito();
    document.getElementById('selectCliente').value = '';
    inputBuscarProducto.value = '';
    ejecutarBusquedaProducto();
}

document.getElementById('formVenta').addEventListener('submit', function (e) {
    if (carrito.length === 0) {
        e.preventDefault();
        alert('Agrega al menos un producto antes de confirmar la venta.');
        return;
    }

    const pagaCon = parseFloat(inputPagaCon.value);
    if (!isNaN(pagaCon) && pagaCon < totalVentaActual) {
        e.preventDefault();
        alert('El monto ingresado para pagar es menor al total de la venta.');
        return;
    }

    carritoJsonInput.value = JSON.stringify(carrito);
});

/* MODAL DE CLIENTES */
function abrirModalCliente() {
    document.getElementById('modalCliente').classList.add('modal-activo');
}

function cerrarModalCliente() {
    document.getElementById('modalCliente').classList.remove('modal-activo');
    document.getElementById('formNuevoClienteModal').reset();
}

document.getElementById('formNuevoClienteModal').addEventListener('submit', function(e) {
    e.preventDefault();
    const nombres   = document.getElementById('modalNombres').value.trim();
    const apellidos = document.getElementById('modalApellidos').value.trim();
    const telefono  = document.getElementById('modalTelefono').value.trim();
    const direccion = document.getElementById('modalDireccion').value.trim();
    const correo    = document.getElementById('modalCorreo').value.trim();

    const formData = new FormData();
    formData.append('nombres', nombres);
    formData.append('apellidos', apellidos);
    formData.append('telefono', telefono);
    formData.append('direccion', direccion);
    formData.append('correo', correo);

    fetch('guardar_cliente_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            const selectCliente = document.getElementById('selectCliente');
            const nuevaOpcion = document.createElement('option');
            nuevaOpcion.value = data.id;
            nuevaOpcion.textContent = `${nombres} ${apellidos}`;
            nuevaOpcion.selected = true;

            selectCliente.appendChild(nuevaOpcion);
            cerrarModalCliente();
            alert('Cliente registrado y seleccionado correctamente.');
        } else {
            alert('Error al registrar cliente: ' + data.mensaje);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar la solicitud.');
    });
});
/* ABRIR COMPROBANTE AUTOMÁTICAMENTE SI LA VENTA FUE EXITOSA */
window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const idVentaExito = urlParams.get('exito');

    if (idVentaExito) {
        // Abre la ventana/pestaña de impresión inmediatamente
        window.open(`imprimir_comprobante.php?id=${idVentaExito}`, '_blank');
    }
});
</script>

</body>
</html>