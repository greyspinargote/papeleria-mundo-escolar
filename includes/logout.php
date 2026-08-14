<?php

require_once "includes/conexion.php";

unset($_SESSION['cliente_id']);
unset($_SESSION['cliente_nombre']);

header("Location: index.php");
exit;