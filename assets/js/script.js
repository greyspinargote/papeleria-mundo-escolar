
document.addEventListener("DOMContentLoaded", function () {

    // BOTÓN VOLVER ARRIBA //

    const btnTop = document.createElement("button");
    btnTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
    btnTop.id = "btnTop";
    document.body.appendChild(btnTop);

    btnTop.style.position = "fixed";
    btnTop.style.bottom = "30px";
    btnTop.style.right = "30px";
    btnTop.style.width = "50px";
    btnTop.style.height = "50px";
    btnTop.style.border = "none";
    btnTop.style.borderRadius = "50%";
    btnTop.style.background = "#0A4DA3";
    btnTop.style.color = "#fff";
    btnTop.style.fontSize = "20px";
    btnTop.style.cursor = "pointer";
    btnTop.style.display = "none";
    btnTop.style.boxShadow = "0 5px 15px rgba(0,0,0,.3)";
    btnTop.style.zIndex = "9999";

    window.addEventListener("scroll", function () {

        if (window.scrollY > 300) {
            btnTop.style.display = "block";
        } else {
            btnTop.style.display = "none";
        }

    });

    btnTop.addEventListener("click", function () {

        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });

    });

    // ANIMACIÓN DE TARJETAS //

    const tarjetas = document.querySelectorAll(".producto, .categoria, .beneficio");

    tarjetas.forEach(function (tarjeta) {

        tarjeta.addEventListener("mouseenter", function () {

            this.style.transition = ".3s";
            this.style.transform = "translateY(-10px)";

        });

        tarjeta.addEventListener("mouseleave", function () {

            this.style.transform = "translateY(0px)";

        });

    });

});