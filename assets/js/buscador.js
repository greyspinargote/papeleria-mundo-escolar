/* BUSCADOR DE PRODUCTOS */

document.addEventListener("DOMContentLoaded", function () {

    const buscador = document.getElementById("buscarProducto");

    if (!buscador) return;

    buscador.addEventListener("keyup", function () {

        let texto = this.value.toLowerCase();

        let productos = document.querySelectorAll(".producto");

        productos.forEach(function (producto) {

            let nombre = producto.querySelector("h3").textContent.toLowerCase();

            if (nombre.indexOf(texto) > -1) {

                producto.style.display = "block";

            } else {

                producto.style.display = "none";

            }

        });

    });

});