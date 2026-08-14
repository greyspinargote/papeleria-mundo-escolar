<?php

require "proteger.php";
require_once "../includes/conexion.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$resultado = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id = $id");
$usuario   = mysqli_fetch_assoc($resultado);

if (!$usuario) {

    header("Location: usuarios.php");
    exit;

}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres      = trim($_POST['nombres']);
    $rol          = $_POST['rol'] === 'admin' ? 'admin' : 'vendedor';
    $passwordNuevo = $_POST['password'] ?? '';

    if ($nombres === "") {

        $error = "El nombre no puede estar vacío.";

    } else {

        $nombresEsc = mysqli_real_escape_string($conexion, $nombres);

        $sql = "UPDATE usuarios SET nombres = '$nombresEsc', rol = '$rol'";

        if ($passwordNuevo !== "") {

            if (strlen($passwordNuevo) < 6) {

                $error = "La nueva contraseña debe tener al menos 6 caracteres.";

            } else {

                $hash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
                $sql .= ", password = '$hash'";

            }

        }

        $sql .= " WHERE id = $id";

        if ($error === "") {

            mysqli_query($conexion, $sql);

            // Si edito mi propio nombre, actualizo también la sesión
            if ($id === (int)$_SESSION['usuario_id']) {
                $_SESSION['usuario_nombres'] = $nombres;
                $_SESSION['usuario_rol']     = $rol;
            }

            header("Location: usuarios.php?actualizado=1");
            exit;

        }

    }

    $resultado = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id = $id");
    $usuario   = mysqli_fetch_assoc($resultado);

}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Usuario - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <h1>Editar Usuario</h1>

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

            <form method="POST">

                <div class="campo">
                    <label>Nombres completos</label>
                    <input type="text" name="nombres" value="<?php echo htmlspecialchars($usuario['nombres']); ?>" required>
                </div>

                <div class="campo">
                    <label>Correo</label>
                    <input type="email" value="<?php echo htmlspecialchars($usuario['correo']); ?>" disabled
                        style="background:#f0f0f0;">
                    <small style="color:#999;">El correo no se puede modificar.</small>
                </div>

                <div class="campo">
                    <label>Rol</label>
                    <select name="rol" required>
                        <option value="vendedor" <?php echo $usuario['rol'] === 'vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                        <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Nueva contraseña (dejar vacío para no cambiarla)</label>
                    <input type="password" name="password" minlength="6" placeholder="••••••••">
                </div>

                <button type="submit" class="btn-panel">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>

                <a href="usuarios.php" class="btn-panel rojo" style="display:inline-block; text-decoration:none; margin-left:10px;">
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>
