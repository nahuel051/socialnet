const nav = document.querySelector("#navegador");
const abrir = document.querySelector("#abrir");
const cerrar = document.querySelector("#cerrar");
const cerrarLinks = document.querySelectorAll(".cerrar-link");

abrir.addEventListener("click", () => {
    nav.classList.add("visible");
});

cerrar.addEventListener("click", () => {
    nav.classList.remove("visible");
});

cerrarLinks.forEach(link => {
    link.addEventListener("click", () => {
        nav.classList.remove("visible");
    });
});
