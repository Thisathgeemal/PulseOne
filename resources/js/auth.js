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
    const memberSection = document.getElementById("member-details-section");
    const paymentSection = document.getElementById("payment-details-section");
    if (memberSection && paymentSection) {
        memberSection.style.display = "none";
        paymentSection.style.display = "grid";
    }
};

window.goToMemberSection = function () {
    const memberSection = document.getElementById("member-details-section");
    const paymentSection = document.getElementById("payment-details-section");
    if (memberSection && paymentSection) {
        memberSection.style.display = "grid";
        paymentSection.style.display = "none";
    }
};
