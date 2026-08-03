<?php

/* FUNCIONES GENERALES */

/*  OBTENER CATEGORÍ */

function obtenerCategorias($conexion)
{

    $sql = "SELECT * FROM categorias
            WHERE estado = 1
            ORDER BY nombre ASC";

    $resultado = mysqli_query($conexion, $sql);

    return $resultado;

}

/* OBTENER PRODUCTOS */

function obtenerProductos($conexion)
{

    $sql = "SELECT *
            FROM productos
            WHERE estado = 1
            ORDER BY id DESC";

    $resultado = mysqli_query($conexion, $sql);

    return $resultado;

}

/* PRODUCTOS DESTACADOS */

function obtenerDestacados($conexion)
{

    $sql = "SELECT *
            FROM productos
            WHERE destacado = 1
            AND estado = 1
            ORDER BY id DESC
            LIMIT 8";

    return mysqli_query($conexion, $sql);

}

/* BUSCAR PRODUCTO POR ID */

function obtenerProducto($conexion,$id)
{

    $id=(int)$id;

    $sql="SELECT *
          FROM productos
          WHERE id=$id";

    return mysqli_query($conexion,$sql);

}

/* FORMATO MONEDA */

function moneda($precio)
{

    return "$ ".number_format($precio,2);

}

/* CONTAR PRODUCTOS */

function totalProductos($conexion)
{

    $sql="SELECT COUNT(*) total
          FROM productos
          WHERE estado=1";

    $resultado=mysqli_query($conexion,$sql);

    $fila=mysqli_fetch_assoc($resultado);

    return $fila['total'];

}

/* OBTENER PRODUCTOS POR CATEGORÍA */

function productosCategoria($conexion,$categoria)
{

    $categoria=(int)$categoria;

    $sql="SELECT *
          FROM productos
          WHERE categoria_id=$categoria
          AND estado=1";

    return mysqli_query($conexion,$sql);

}

?>