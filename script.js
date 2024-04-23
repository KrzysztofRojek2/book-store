const cookieBox = document.querySelector(".cookies-wrapper"),
    buttons = document.querySelectorAll(".cookie-button");

const executeCodes = () => {
    if (document.cookie.includes("codinglab")) return;
    cookieBox.classList.add("show");

    buttons.forEach((button) => {
        button.addEventListener("click", () => {
            cookieBox.classList.remove("show");

           if (button.id == "acceptBtn") {
            document.cookie = "cookieBy= codinglab; max-age=" + 60 * 5;
        }
        });
    });
};

window.addEventListener("load",executeCodes);