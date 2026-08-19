<?php

require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

// RESTRICCIÓN ESTRICTA
$rolActual = strtolower($_SESSION['usuario_rol'] ?? '');
if ($rolActual !== 'administrador' && $rolActual !== 'admin') {
    header("Location: productos.php");
    exit();
}

// Validar que se reciba un ID válido por GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: productos.php");
    exit();
}

$id = (int)$_GET['id'];

// Obtener datos del producto a editar
$resultado = obtenerProducto($conexion, $id);
$producto  = mysqli_fetch_assoc($resultado);

if (!$producto) {
    header("Location: productos.php");
    exit();
}

/* PROCESAR ACTUALIZACIÓN DEL PRODUCTO */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_producto'])) {

    $categoria_id = (int)$_POST['categoria_id'];
    $nombre       = trim($_POST['nombre']);
    $descripcion  = trim($_POST['descripcion']);
    $precio       = (float)$_POST['precio'];
    $stock        = (int)$_POST['stock'];
    $destacado    = isset($_POST['destacado']) ? 1 : 0;

    if ($nombre === "" || $categoria_id <= 0 || $precio <= 0) {
        $error = "Completa el nombre, la categoría y el precio correctamente.";
    } else {
        $nombreArchivo = $producto['imagen']; 

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            $extension  = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $permitidas)) {
                $error = "Formato de imagen no permitido.";
            } elseif ($_FILES['imagen']['size'] > 3 * 1024 * 1024) {
                $error = "La imagen es muy pesada (máx 3MB).";
            } else {
                $nuevoNombre = uniqid("prod_") . "." . $extension;
                $rutaDestino = "../assets/img/productos/" . $nuevoNombre;
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    $rutaAnterior = "../assets/img/productos/" . $producto['imagen'];
                    if (!empty($producto['imagen']) && file_exists($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }
                    $nombreArchivo = $nuevoNombre;
                }
            }
        }

        if (empty($error)) {
            $nombreEsc      = mysqli_real_escape_string($conexion, $nombre);
            $descripcionEsc = mysqli_real_escape_string($conexion, $descripcion);

            $sql = "UPDATE productos SET 
                    categoria_id = $categoria_id,
                    nombre       = '$nombreEsc',
                    descripcion  = '$descripcionEsc',
                    precio       = $precio,
                    stock        = $stock,
                    imagen       = '$nombreArchivo',
                    destacado    = $destacado
                    WHERE id = $id";

            if (mysqli_query($conexion, $sql)) {
                header("Location: productos.php?actualizado=1");
                exit();
            } else {
                $error = "Error al actualizar: " . mysqli_error($conexion);
            }
        }
    }
}

$categorias = obtenerCategorias($conexion);
$inicialUsuario = !empty($_SESSION['usuario_nombres']) ? strtoupper(substr($_SESSION['usuario_nombres'], 0, 1)) : 'G';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Producto - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="layout-panel">
    <?php include "includes_menu.php"; ?>
    <div class="contenido-panel">
        <div class="encabezado-panel">
            <div class="titulos-header">
                <h1>Editar Producto</h1>
                <p class="subtitulo-header">Modificando: <?php echo htmlspecialchars($producto['nombre']); ?></p>
            </div>
            <div class="tarjeta-usuario-header">
                <div class="avatar-inicial"><?php echo $inicialUsuario; ?></div>
                <div class="info-usuario-header">
                    <span class="nombre-user"><?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mensaje-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="tarjeta-panel">
            <form method="POST" enctype="multipart/form-data">
                <!-- Campos del formulario igual que tenías -->
                <div class="campo">
                    <label>Categoría</label>
                    <select name="categoria_id" required>
                        <?php while ($cat = mysqli_fetch_assoc($categorias)) { ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $producto['categoria_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Nombre del producto</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>
                <div class="campo">
                    <label>Precio ($)</label>
                    <input type="number" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
                </div>
                <div class="campo">
                    <label>Stock</label>
                    <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>
                </div>
                <div class="campo">
                    <label>Imagen actual</label>
                    <img src="../assets/img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" style="width:50px; display:block; margin-bottom:5px;">
                    <input type="file" name="imagen">
                </div>
                <div class="campo campo-checkbox">
                    <input type="checkbox" name="destacado" id="destacado" <?php echo $producto['destacado'] ? 'checked' : ''; ?>>
                    <label for="destacado">Mostrar en Destacados</label>
                </div>
                <button type="submit" name="actualizar_producto" class="btn-agregar-verde">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>