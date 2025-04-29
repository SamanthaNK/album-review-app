// assets/js/script.js

// Auto-fade alert after 3 seconds
document.addEventListener("DOMContentLoaded", function () {
    const alertBox = document.querySelector('.alert');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = "0";
        }, 3000);
    }
});

document.addEventListener("DOMContentLoaded", function () {
    // Auto-fade alert
    const alertBox = document.querySelector('.alert');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = "0";
        }, 3000);
    }

    // Live image preview
    const coverInput = document.getElementById("coverImageUrl");
    const imagePreview = document.getElementById("imagePreview");

    if (coverInput && imagePreview) {
        coverInput.addEventListener("input", function () {
            const url = coverInput.value.trim();
            if (url.match(/\.(jpeg|jpg|gif|png|webp)$/i)) {
                imagePreview.src = url;
                imagePreview.style.display = "block";
            } else {
                imagePreview.src = "";
                imagePreview.style.display = "none";
            }
        });
    }
});
