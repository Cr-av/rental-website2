//Prosty JS (Jak widać naprawdę minimalny)
document.addEventListener("DOMContentLoaded", function () {
    console.log("test - dziala");
    
    // "walidacja hasla" (w rejestracji)
    const form = document.querySelector("form");
    if (form && form.querySelector("input[name='haslo']")) {
        form.addEventListener("submit", function (e) {
            const haslo = form.querySelector("input[name='haslo']").value;
            if (haslo.length < 6) {
                alert("Hasło musi mieć co najmniej 6 znaków.");
                e.preventDefault();
            }
        });
    }

    // efekt przy najechaniu na liste (dashboard)
    const items = document.querySelectorAll("ul li");
    items.forEach(function (item) {
        item.addEventListener("mouseover", function () {
            item.style.backgroundColor = "#503579";
            item.style.scale = 1.01;
        });
        item.addEventListener("mouseout", function () {
            item.style.backgroundColor = "";
            item.style.scale = 1;
        });
    });
});
