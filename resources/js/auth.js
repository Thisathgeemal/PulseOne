window.togglePasswordVisibility = function () {
    // Toggle password visibility based on input type
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
    // Select a specific card type in the dropdown and trigger change event
    const select = document.getElementById("card_type");
    if (!select) return;
    select.value = type;
    select.dispatchEvent(new Event("change"));
};

window.goToPaymentSection = function () {
    // Show the payment form and adjust layout for payment section
    const createForm = document.getElementById("create-form");
    const paymentForm = document.getElementById("payment-form");

    createForm.classList.remove("max-w-md");
    paymentForm.classList.remove("hidden");

    const container = createForm.parentElement;
    container.classList.add("md:space-x-10");
    container.classList.remove("justify-center");
    container.classList.add("justify-between");
};

window.setPriceFromSelect = function (selectId, targetInputId) {
    // Set price in an input field based on selected option from dropdown
    const select = document.getElementById(selectId);
    if (!select) return;

    const selectedOption = select.options[select.selectedIndex];
    const price = selectedOption.getAttribute("data-price") || "0.00";
    const targetInput = document.getElementById(targetInputId);

    if (targetInput) {
        targetInput.value = price;
    }
};
