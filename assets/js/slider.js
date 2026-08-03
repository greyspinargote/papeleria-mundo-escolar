/* slider.js */

document.addEventListener("DOMContentLoaded", function () {

    const slides = document.querySelectorAll(".slide");

    if (slides.length === 0) return;

    let indice = 0;

    // Mostrar el primer slide
    slides[indice].classList.add("active");

    function cambiarSlide() {

        slides[indice].classList.remove("active");

        indice++;

        if (indice >= slides.length) {
            indice = 0;
        }

        slides[indice].classList.add("active");

    }

    // Cambia cada 5 segundos
    setInterval(cambiarSlide, 5000);

});