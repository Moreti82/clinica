function togglePassword() {
    const input = document.getElementById("senha");
    const icon = document.querySelector(".toggle-password i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

const erroLogin = document.getElementById("erroLogin");
if (erroLogin.textContent.trim() !== "") {
    erroLogin.style.display = "block"; // mostra o <p>
}


