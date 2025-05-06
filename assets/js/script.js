// assets/js/script.js

document.addEventListener("DOMContentLoaded", function () {
    // Auto-fade alert after 3 seconds
    const alertBox = document.querySelector('.alert');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = "0";
            setTimeout(() => {
                alertBox.remove();
            }, 500);
        }, 3000);
    }

    // Random Album button functionality with loading state
    const randomAlbumBtn = document.querySelector('a[href="recommendations.php"]');
    if (randomAlbumBtn) {
        randomAlbumBtn.addEventListener('click', function (e) {
            e.preventDefault();
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
            this.disabled = true;
        });
    }

    // Add nice fade-in effect for page elements
    document.querySelectorAll('section').forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

        setTimeout(() => {
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, 100 * (index + 1));
    });

    // Live image preview for album cover input
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

    // Confirm delete on album reviews
    const deleteButtons = document.querySelectorAll('form[action="delete_review.php"]');
    deleteButtons.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to delete this review?')) {
                e.preventDefault();
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('albumForm');
    
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
            
            // Additional validation for genre selection
            const genres = document.getElementById('genres');
            if (genres.selectedOptions.length === 0) {
                genres.setCustomValidity('Please select at least one genre');
            } else {
                genres.setCustomValidity('');
            }
            
            // Additional validation for image URL
            const coverUrl = document.getElementById('coverImageUrl');
            if (coverUrl.value && !coverUrl.value.match(/\.(jpeg|jpg|gif|png|webp)$/i)) {
                coverUrl.setCustomValidity('Please enter a valid image URL');
            } else {
                coverUrl.setCustomValidity('');
            }
        });
        
        // Live validation for genres
        const genres = document.getElementById('genres');
        genres.addEventListener('change', function() {
            if (this.selectedOptions.length === 0) {
                this.setCustomValidity('Please select at least one genre');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Live validation for image URL
        const coverUrl = document.getElementById('coverImageUrl');
        coverUrl.addEventListener('input', function() {
            if (this.value && !this.value.match(/\.(jpeg|jpg|gif|png|webp)$/i)) {
                this.setCustomValidity('Please enter a valid image URL');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});