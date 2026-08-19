<?php
require_once "includes/conexion.php";
include "includes/header.php";
include "includes/navbar.php";

$mensaje_exito = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $correo_cliente = $_POST['correo'] ?? '';
    $nombre_comprobante = "";

    if ($metodo_pago === 'Transferencia') {
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === 0) {
            $directorio = "uploads/comprobantes/";
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
            $nombre_comprobante = time() . "_" . basename($_FILES['comprobante']['name']);
            move_uploaded_file($_FILES['comprobante']['tmp_name'], $directorio . $nombre_comprobante);
        } else {
            $error = "Por favor, adjunta el comprobante de la transferencia.";
        }
    }

    if ($error === "") {
        // Aquí va tu consulta SQL (INSERT INTO ventas...)

        if (!empty($correo_cliente)) {
            $asunto = "Comprobante de compra - Papelería Mundo Escolar";
            $mensaje = "Hola, gracias por tu compra. Hemos registrado tu pago por " . $metodo_pago . ".";
            
            $boundary = md5(time());
            $headers = "From: contacto@mundoescolar.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

            $body = "--{$boundary}\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n" . $mensaje . "\r\n";

            if (!empty($nombre_comprobante)) {
                $ruta = "uploads/comprobantes/" . $nombre_comprobante;
                if (file_exists($ruta)) {
                    $contenido = chunk_split(base64_encode(file_get_contents($ruta)));
                    $body .= "--{$boundary}\r\n";
                    $body .= "Content-Type: application/octet-stream; name=\"{$nombre_comprobante}\"\r\n";
                    $body .= "Content-Transfer-Encoding: base64\r\n";
                    $body .= "Content-Disposition: attachment; filename=\"{$nombre_comprobante}\"\r\n\r\n";
                    $body .= $contenido . "\r\n";
                }
            }
            $body .= "--{$boundary}--";
            @mail($correo_cliente, $asunto, $body, $headers);
        }
        $mensaje_exito = "¡La venta se ha guardado con éxito!";
    }
}
?>

<section class="finalizar-compra">
    <div class="titulo-seccion">
        <h2>Registrar Venta / Pago</h2>
    </div>

    <?php if ($mensaje_exito): ?>
        <div class="alerta-exito"><?php echo $mensaje_exito; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alerta-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="formulario-compra">
        <div class="grupo">
            <label>Correo del cliente</label>
            <input type="email" name="correo" required>
        </div>

        <div class="grupo">
            <label>Método de pago</label>
            <select name="metodo_pago" id="metodo_pago" onchange="cambiarMetodoPago()" required>
                <option value="">Seleccione...</option>
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia Bancaria</option>
                <option value="Tarjeta">Tarjeta de Crédito / Débito</option>
            </select>
        </div>

        <!-- Sección Transferencia -->
        <div id="info_transferencia" style="display: none;">
            <p>Realiza tu transferencia a:</p>
            <p>Banco Pichincha - Cta: 1234567890</p>
            <div class="grupo">
                <label>Adjuntar Comprobante</label>
                <input type="file" name="comprobante" accept="image/*,application/pdf">
            </div>
        </div>

        <!-- Sección Tarjeta -->
        <div id="info_tarjeta" style="display: none;">
            <p>Ingrese los datos de su tarjeta:</p>
            <div class="grupo">
                <label>Número de Tarjeta</label>
                <input type="text" placeholder="0000 0000 0000 0000">
            </div>
        </div>

        <button type="submit" class="btn-comprar">Guardar Venta</button>
    </form>
</section>

<script>
function cambiarMetodoPago() {
    var metodo = document.getElementById("metodo_pago").value;
    var divTransferencia = document.getElementById("info_transferencia");
    var divTarjeta = document.getElementById("info_tarjeta");
    
    divTransferencia.style.display = "none";
    divTarjeta.style.display = "none";
    
    if (metodo === "Transferencia") {
        divTransferencia.style.display = "block";
    } else if (metodo === "Tarjeta") {
        divTarjeta.style.display = "block";
    }
}
</script>

<?php include "includes/footer.php"; ?>