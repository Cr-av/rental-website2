// Sprawdź, czy DOM został załadowany
document.addEventListener("DOMContentLoaded", function () {
    // Przykład: prosty komunikat powitalny w konsoli
    console.log("Witamy w systemie wypożyczalni!");

    // Przykład: walidacja formularza rejestracji
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

    // Przykład: podświetlenie elementów na liście przy najechaniu
    const items = document.querySelectorAll("ul li");
    items.forEach(function (item) {
        item.addEventListener("mouseover", function () {
            item.style.backgroundColor = "#eef";
        });
        item.addEventListener("mouseout", function () {
            item.style.backgroundColor = "";
        });
    });
});
