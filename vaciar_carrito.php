<?php

session_start();

// Vaciar completamente el carrito
unset($_SESSION['carrito']);

// Regresar al carrito
header("Location: carrito.php");
exit();

?>