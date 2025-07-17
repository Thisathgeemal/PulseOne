window.togglePasswordVisibility = function () {
    const input = document.getElementById("password");
    const icon = document.getElementById("password-toggle-icon");
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
};

window.selectCardType = function (type) {
    const select = document.getElementById("card_type");
    if (!select) return;
    select.value = type;
    select.dispatchEvent(new Event("change"));
};

window.goToPaymentSection = function () {
    const createForm = document.getElementById("create-form");
    const paymentForm = document.getElementById("payment-form");

    createForm.classList.remove("max-w-md");
    paymentForm.classList.remove("hidden");

    const container = createForm.parentElement;
    container.classList.add("md:space-x-10");
    container.classList.remove("justify-center");
    container.classList.add("justify-between");
};
