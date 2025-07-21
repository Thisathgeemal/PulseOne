// Initialize after DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    handleSelectAll("select-all", "selector[]");
});

//  handle "Select All" functionality
window.handleSelectAll = function (masterCheckboxId, selectorName) {
    const masterCheckbox = document.getElementById(masterCheckboxId);
    const checkboxes = document.querySelectorAll(
        `input[name="${selectorName}"]`
    );

    if (masterCheckbox) {
        masterCheckbox.addEventListener("change", function () {
            checkboxes.forEach((cb) => (cb.checked = this.checked));
        });
    }
};
