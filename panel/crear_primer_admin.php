<?php

/* SCRIPT DE UN SOLO USO: crea el primer usuario administrador */
/* BORRA ESTE ARCHIVO después de usarlo, por seguridad */

require_once "../includes/conexion.php";

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres  = trim($_POST['nombres']);
    $correo   = trim($_POST['correo']);
    $password = $_POST['password'];

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $nombresEsc = mysqli_real_escape_string($conexion, $nombres);
    $correoEsc  = mysqli_real_escape_string($conexion, $correo);

    $sql = "INSERT INTO usuarios (nombres, correo, password, rol, estado)
            VALUES ('$nombresEsc', '$correoEsc', '$hash', 'admin', 1)";

    if (mysqli_query($conexion, $sql)) {
        $mensaje = "✅ Administrador creado correctamente. Ya puedes borrar este archivo.";
    } else {
        $mensaje = "❌ Error: " . mysqli_error($conexion);
    }

}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear primer administrador</title>
<style>
    body{font-family:sans-serif; max-width:400px; margin:80px auto; padding:20px;}
    input{width:100%; padding:10px; margin-bottom:12px; box-sizing:border-box;}
    button{width:100%; padding:12px; background:#0A4DA3; color:white; border:none; border-radius:6px; cursor:pointer;}
    .msg{background:#d4edda; padding:12px; margin-bottom:15px; border-radius:6px;}
</style>
</head>
<body>

<h2>Crear primer administrador</h2>

<?php if ($mensaje): ?>
    <div class="msg"><?php echo $mensaje; ?></div>
<?php endif; ?>

<form method="POST">
    <label>Nombres</label>
    <input type="text" name="nombres" required>

    <label>Correo</label>
    <input type="email" name="correo" required>

    <label>Contraseña</label>
    <input type="password" name="password" required minlength="6">

    <button type="submit">Crear administrador</button>
</form>

</body>
</html>