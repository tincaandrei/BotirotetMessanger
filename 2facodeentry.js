const inputs = document.querySelectorAll(".code-input");

inputs.forEach((input, index) => {
    input.addEventListener("input", (event) => {
        const value = event.target.value;
        
        // Verificăm dacă valoarea introdusă este o cifră
        if (!/^[0-9]$/.test(value)) {
            event.target.setCustomValidity("Please enter a number between 0 and 9");
            event.target.reportValidity();
            event.target.value = ""; // Ștergem valoarea dacă nu este o cifră
        } else {
            event.target.setCustomValidity(""); // Resetăm mesajul de eroare pentru valoare validă
            if (index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        }
    });

    // Permitem navigarea cu backspace
    input.addEventListener("keydown", (event) => {
        if (event.key === "Backspace" && index > 0 && !input.value) {
            inputs[index - 1].focus();
        }
    });
});
