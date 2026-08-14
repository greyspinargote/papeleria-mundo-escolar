<?php

require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

$mensaje = "";
$error   = "";

if (isset($_GET['actualizado'])) {
    $mensaje = "Producto actualizado correctamente.";
}

/* GUARDAR PRODUCTO NUEVO */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_producto'])) {

    $categoria_id = (int)$_POST['categoria_id'];
    $nombre       = trim($_POST['nombre']);
    $descripcion  = trim($_POST['descripcion']);
    $precio       = (float)$_POST['precio'];
    $stock        = (int)$_POST['stock'];
    $destacado    = isset($_POST['destacado']) ? 1 : 0;

    if ($nombre === "" || $categoria_id <= 0 || $precio <= 0) {

        $error = "Completa nombre, categoría y precio correctamente.";

    } elseif (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== 0) {

        $error = "Debes seleccionar una imagen para el producto.";

    } else {

        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $extension  = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $permitidas)) {

            $error = "Formato de imagen no permitido. Usa JPG, PNG o WEBP.";

        } elseif ($_FILES['imagen']['size'] > 3 * 1024 * 1024) {

            $error = "La imagen no puede pesar más de 3MB.";

        } else {

            $nombreArchivo = uniqid("prod_") . "." . $extension;
            $rutaDestino   = "../assets/img/productos/" . $nombreArchivo;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {

                $nombreEsc      = mysqli_real_escape_string($conexion, $nombre);
                $descripcionEsc = mysqli_real_escape_string($conexion, $descripcion);

                $sql = "INSERT INTO productos
                        (categoria_id, nombre, descripcion, precio, stock, imagen, destacado, estado)
                        VALUES
                        ($categoria_id, '$nombreEsc', '$descripcionEsc', $precio, $stock, '$nombreArchivo', $destacado, 1)";

                if (mysqli_query($conexion, $sql)) {

                    $mensaje = "Producto agregado correctamente.";

                } else {

                    $error = "Error al guardar en la base de datos: " . mysqli_error($conexion);

                }

            } else {

                $error = "No se pudo subir la imagen. Revisa permisos de la carpeta assets/img/productos.";

            }

        }

    }

}

/* ELIMINAR PRODUCTO */

if (isset($_GET['eliminar'])) {

    $id = (int)$_GET['eliminar'];

    $resultado = obtenerProducto($conexion, $id);
    $producto  = mysqli_fetch_assoc($resultado);

    if ($producto) {

        $rutaImagen = "../assets/img/productos/" . $producto['imagen'];

        if (!empty($producto['imagen']) && file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }

        mysqli_query($conexion, "DELETE FROM productos WHERE id = $id");

        $mensaje = "Producto eliminado.";

    }

}

$categorias = obtenerCategorias($conexion);
$productos  = obtenerProductos($conexion);

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Productos - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Productos</h1>

            <div class="usuario-actual">
                <i class="fa-solid fa-circle-user"></i>
                <?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?>
                <span class="badge-rol"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
            </div>

        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mensaje-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="tarjeta-panel">

            <h2>Agregar Producto</h2>

            <form method="POST" enctype="multipart/form-data">

                <div class="campo">
                    <label>Categoría</label>
                    <select name="categoria_id" required>
                        <option value="">-- Selecciona --</option>
                        <?php while ($cat = mysqli_fetch_assoc($categorias)) { ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="campo">
                    <label>Nombre del producto</label>
                    <input type="text" name="nombre" required>
                </div>

                <div class="campo">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3"></textarea>
                </div>

                <div class="campo">
                    <label>Precio ($)</label>
                    <input type="number" name="precio" step="0.01" min="0.01" required>
                </div>

                <div class="campo">
                    <label>Stock disponible</label>
                    <input type="number" name="stock" min="0" required>
                </div>

                <div class="campo">
                    <label>Imagen del producto</label>
                    <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>
                </div>

                <div class="campo" style="display:flex; align-items:center; gap:10px;">
                    <input type="checkbox" name="destacado" id="destacado" style="width:auto;">
                    <label for="destacado" style="margin:0;">Mostrar en Productos Destacados (inicio)</label>
                </div>

                <button type="submit" name="guardar_producto" class="btn-panel">
                    <i class="fa-solid fa-plus"></i> Agregar producto
                </button>

            </form>

        </div>

        <div class="tarjeta-panel">

            <h2>Productos existentes</h2>

            <table class="tabla-panel">

                <tr>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Destacado</th>
                    <th>Acciones</th>
                </tr>

                <?php while ($p = mysqli_fetch_assoc($productos)) { ?>

                    <tr>
                        <td>
                            <img src="../assets/img/productos/<?php echo htmlspecialchars($p['imagen']); ?>" alt="">
                        </td>
                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td><?php echo moneda($p['precio']); ?></td>
                        <td><?php echo $p['stock']; ?></td>
                        <td><?php echo $p['destacado'] ? 'Sí' : 'No'; ?></td>
                        <td>
                            <a href="editar_producto.php?id=<?php echo $p['id']; ?>" style="color:#0A4DA3; font-weight:600; margin-right:12px;">
                                Editar
                            </a>
                            <a href="productos.php?eliminar=<?php echo $p['id']; ?>"
                               style="color:#dc3545; font-weight:600;"
                               onclick="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.');">
                                Eliminar
                            </a>
                        </td>
                    </tr>

                <?php } ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>