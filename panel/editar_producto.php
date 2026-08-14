<?php

require "proteger.php";
require_once "../includes/conexion.php";
require_once "../includes/funciones.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$resultado = obtenerProducto($conexion, $id);
$producto  = mysqli_fetch_assoc($resultado);

if (!$producto) {

    header("Location: productos.php");
    exit;

}

$error = "";

/* GUARDAR CAMBIOS */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $categoria_id = (int)$_POST['categoria_id'];
    $nombre       = trim($_POST['nombre']);
    $descripcion  = trim($_POST['descripcion']);
    $precio       = (float)$_POST['precio'];
    $stock        = (int)$_POST['stock'];
    $destacado    = isset($_POST['destacado']) ? 1 : 0;

    if ($nombre === "" || $categoria_id <= 0 || $precio <= 0) {

        $error = "Completa nombre, categoría y precio correctamente.";

    } else {

        $nombreImagen = $producto['imagen'];

        /* Si subió una imagen nueva, reemplazamos la anterior */

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0 && $_FILES['imagen']['size'] > 0) {

            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            $extension  = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $permitidas)) {

                $error = "Formato de imagen no permitido. Usa JPG, PNG o WEBP.";

            } elseif ($_FILES['imagen']['size'] > 3 * 1024 * 1024) {

                $error = "La imagen no puede pesar más de 3MB.";

            } else {

                $nombreArchivoNuevo = uniqid("prod_") . "." . $extension;
                $rutaDestino        = "../assets/img/productos/" . $nombreArchivoNuevo;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {

                    $rutaAnterior = "../assets/img/productos/" . $producto['imagen'];

                    if (!empty($producto['imagen']) && file_exists($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }

                    $nombreImagen = $nombreArchivoNuevo;

                } else {

                    $error = "No se pudo subir la nueva imagen.";

                }

            }

        }

        if ($error === "") {

            $nombreEsc      = mysqli_real_escape_string($conexion, $nombre);
            $descripcionEsc = mysqli_real_escape_string($conexion, $descripcion);
            $imagenEsc      = mysqli_real_escape_string($conexion, $nombreImagen);

            $sql = "UPDATE productos SET
                        categoria_id = $categoria_id,
                        nombre = '$nombreEsc',
                        descripcion = '$descripcionEsc',
                        precio = $precio,
                        stock = $stock,
                        imagen = '$imagenEsc',
                        destacado = $destacado
                    WHERE id = $id";

            if (mysqli_query($conexion, $sql)) {

                header("Location: productos.php?actualizado=1");
                exit;

            } else {

                $error = "Error al actualizar: " . mysqli_error($conexion);

            }

        }

    }

    // Refrescar datos del producto para mostrar en el formulario si hubo error
    $resultado = obtenerProducto($conexion, $id);
    $producto  = mysqli_fetch_assoc($resultado);

}

$categorias = obtenerCategorias($conexion);

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

            <h1>Editar Producto</h1>

            <div class="usuario-actual">
                <i class="fa-solid fa-circle-user"></i>
                <?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?>
                <span class="badge-rol"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
            </div>

        </div>

        <?php if ($error): ?>
            <div class="mensaje-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="tarjeta-panel">

            <img src="../assets/img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>"
                 alt="" style="width:120px; height:120px; object-fit:cover; border-radius:10px; margin-bottom:20px;">

            <form method="POST" enctype="multipart/form-data">

                <div class="campo">
                    <label>Categoría</label>
                    <select name="categoria_id" required>
                        <?php while ($cat = mysqli_fetch_assoc($categorias)) { ?>
                            <option value="<?php echo $cat['id']; ?>"
                                <?php echo $cat['id'] == $producto['categoria_id'] ? 'selected' : ''; ?>>
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
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                </div>

                <div class="campo">
                    <label>Precio ($)</label>
                    <input type="number" name="precio" step="0.01" min="0.01" value="<?php echo $producto['precio']; ?>" required>
                </div>

                <div class="campo">
                    <label>Stock disponible</label>
                    <input type="number" name="stock" min="0" value="<?php echo $producto['stock']; ?>" required>
                </div>

                <div class="campo">
                    <label>Reemplazar imagen (opcional)</label>
                    <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp">
                </div>

                <div class="campo" style="display:flex; align-items:center; gap:10px;">
                    <input type="checkbox" name="destacado" id="destacado" style="width:auto;"
                        <?php echo $producto['destacado'] ? 'checked' : ''; ?>>
                    <label for="destacado" style="margin:0;">Mostrar en Productos Destacados (inicio)</label>
                </div>

                <button type="submit" class="btn-panel">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>

                <a href="productos.php" class="btn-panel rojo" style="display:inline-block; text-decoration:none; margin-left:10px;">
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>