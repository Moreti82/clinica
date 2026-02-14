const senhaInput = document.getElementById("senha");
const toggleSenha = document.getElementById("toggleSenha");

toggleSenha.addEventListener("click", function () {
    if (senhaInput.type === "password") {
        senhaInput.type = "text";
        this.classList.remove("fa-eye");
        this.classList.add("fa-eye-slash");
    } else {
        senhaInput.type = "password";
        this.classList.remove("fa-eye-slash");
        this.classList.add("fa-eye");
    }
});

const confsenhaInput = document.getElementById("conf-senha");
const toggleConfSenha = document.getElementById("toggleConfSenha");

toggleConfSenha.addEventListener("click", function () {
    if (confsenhaInput.type === "password") {
        confsenhaInput.type = "text";
        this.classList.remove("fa-eye");
        this.classList.add("fa-eye-slash");
    } else {
        confsenhaInput.type = "password";
        this.classList.remove("fa-eye-slash");
        this.classList.add("fa-eye");
    }
});

const labelConfirmar = document.getElementById("confirmar-senha");
const botao = document.getElementById("salvar");

function validarSenha() {
    if (confsenhaInput.value === "") {
        labelConfirmar.textContent = "Confirmar Senha";
        labelConfirmar.style.color = "blue";
        botao.disabled = true; // mantém desabilitado
        return;
    }

    if (senhaInput.value !== confsenhaInput.value) {
        labelConfirmar.textContent = "As senhas não conferem";
        labelConfirmar.style.color = "red";
        botao.disabled = true; // desabilita
    } else {
        labelConfirmar.textContent = "Senhas conferem ✔";
        labelConfirmar.style.color = "green";
        botao.disabled = false; // habilita
    }
}

    senhaInput.addEventListener("input", validarSenha);
    confsenhaInput.addEventListener("input", validarSenha);