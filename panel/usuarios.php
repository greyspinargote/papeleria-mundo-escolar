<?php

require "proteger.php";
require_once "../includes/conexion.php";

$mensaje = "";
$error   = "";

if (isset($_GET['actualizado'])) {
    $mensaje = "Usuario actualizado correctamente.";
}

/* CREAR USUARIO NUEVO */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {

    $nombres  = trim($_POST['nombres']);
    $correo   = trim($_POST['correo']);
    $password = $_POST['password'];
    $rol      = $_POST['rol'] === 'admin' ? 'admin' : 'vendedor';

    if ($nombres === "" || $correo === "" || strlen($password) < 6) {

        $error = "Completa todos los campos. La contraseña debe tener al menos 6 caracteres.";

    } else {

        $correoEsc = mysqli_real_escape_string($conexion, $correo);

        $verificar = mysqli_query($conexion, "SELECT id FROM usuarios WHERE correo = '$correoEsc'");

        if (mysqli_num_rows($verificar) > 0) {

            $error = "Ya existe un usuario con ese correo.";

        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $nombresEsc = mysqli_real_escape_string($conexion, $nombres);

            mysqli_query($conexion, "INSERT INTO usuarios (nombres, correo, password, rol, estado)
                VALUES ('$nombresEsc', '$correoEsc', '$hash', '$rol', 1)");

            $mensaje = "Usuario creado correctamente.";

        }

    }

}

/* ACTIVAR / DESACTIVAR USUARIO */

if (isset($_GET['toggle'])) {

    $id = (int)$_GET['toggle'];

    if ($id === (int)$_SESSION['usuario_id']) {

        $error = "No puedes desactivar tu propia cuenta.";

    } else {

        mysqli_query($conexion, "UPDATE usuarios SET estado = 1 - estado WHERE id = $id");
        $mensaje = "Estado del usuario actualizado.";

    }

}

$usuarios = mysqli_query($conexion, "SELECT * FROM usuarios ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Usuarios - Panel Mundo Escolar</title>
<link rel="stylesheet" href="panel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout-panel">

    <?php include "includes_menu.php"; ?>

    <div class="contenido-panel">

        <div class="encabezado-panel">

            <div class="header-info">
                <h1>Usuarios</h1>
                <p class="subtitulo-panel">Gestión y control de usuarios del sistema 👋</p>
            </div>

            <div class="usuario-tarjeta">
                <div class="avatar-inicial">
                    <?php 
                        $inicial = !empty($_SESSION['usuario_nombres']) ? strtoupper(substr(trim($_SESSION['usuario_nombres']), 0, 1)) : 'U';
                        echo htmlspecialchars($inicial);
                    ?>
                </div>
                <div class="datos-usuario">
                    <span class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?></span>
                    <span class="rol-usuario"><?php echo htmlspecialchars(ucfirst($_SESSION['usuario_rol'])); ?></span>
                </div>
            </div>

        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mensaje-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="tarjeta-panel">

            <h2>Crear nuevo usuario</h2>

            <form method="POST">

                <div class="campo">
                    <label>Nombres completos</label>
                    <input type="text" name="nombres" required>
                </div>

                <div class="campo">
                    <label>Correo</label>
                    <input type="email" name="correo" required>
                </div>

                <div class="campo">
                    <label>Contraseña</label>
                    <input type="password" name="password" required minlength="6">
                </div>

                <div class="campo">
                    <label>Rol</label>
                    <select name="rol" required>
                        <option value="vendedor">Vendedor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>

                <button type="submit" name="crear_usuario" class="btn-panel">
                    <i class="fa-solid fa-user-plus"></i> Crear usuario
                </button>

            </form>

        </div>

        <div class="tarjeta-panel">

            <h2>Usuarios del sistema</h2>

            <table class="tabla-panel">

                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

                <?php while ($u = mysqli_fetch_assoc($usuarios)) { ?>

                    <tr>
                        <td><?php echo htmlspecialchars($u['nombres']); ?></td>
                        <td><?php echo htmlspecialchars($u['correo']); ?></td>
                        <td>
                            <span class="badge-rol"><?php echo htmlspecialchars($u['rol']); ?></span>
                        </td>
                        <td>
                            <?php if ($u['estado']): ?>
                                <span class="badge-estado state-activo">Activo</span>
                            <?php else: ?>
                                <span class="badge-estado state-inactivo">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" class="link-detalle">
                                Editar
                            </a>

                            <?php if ($u['id'] != $_SESSION['usuario_id']): ?>

                                <a href="usuarios.php?toggle=<?php echo $u['id']; ?>"
                                   class="<?php echo $u['estado'] ? 'link-desactivar' : 'link-activar'; ?>"
                                   onclick="return confirm('¿<?php echo $u['estado'] ? 'Desactivar' : 'Activar'; ?> a este usuario?');">
                                    <?php echo $u['estado'] ? 'Desactivar' : 'Activar'; ?>
                                </a>

                            <?php else: ?>

                                <span class="texto-usuario-actual">(Tú)</span>

                            <?php endif; ?>
                        </td>
                    </tr>

                <?php } ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>