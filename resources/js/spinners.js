/**
 * Spinners de carga para formularios
 * Este archivo centraliza la funcionalidad de spinners para mejorar la UX
 */

document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar spinner en un botón
    function showSpinner(button) {
        const btnText = button.querySelector('.btn-text');
        const btnSpinner = button.querySelector('.btn-spinner');
        
        if (btnText && btnSpinner) {
            btnText.style.display = 'none';
            btnSpinner.style.display = 'inline-flex';
            button.disabled = true;
        }
    }

    // Función para mostrar spinner con texto personalizado
    function showSpinnerWithText(button, text) {
        const originalText = button.innerHTML;
        button.dataset.originalText = originalText;
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${text}`;
        button.disabled = true;
    }

    // Spinners para formularios principales
    const mainForms = document.querySelectorAll('form[action*="store"], form[action*="update"]');
    mainForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && submitBtn.querySelector('.btn-spinner')) {
                showSpinner(submitBtn);
            }
        });
    });

    // Spinners para subida de archivos (temarios y portadas)
    const uploadForms = document.querySelectorAll('form[action*="upload"]');
    uploadForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && submitBtn.querySelector('.btn-spinner')) {
                showSpinner(submitBtn);
            }
        });
    });

    // Spinners para eliminación de archivos
    const deleteForms = document.querySelectorAll('form[action*="delete-temario"], form[action*="delete-portada"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                if (submitBtn.querySelector('.btn-spinner')) {
                    showSpinner(submitBtn);
                } else {
                    showSpinnerWithText(submitBtn, 'Eliminando...');
                }
            }
        });
    });

    // Spinners para otros formularios de eliminación
    const otherDeleteForms = document.querySelectorAll('form[action*="delete"]:not([action*="delete-temario"]):not([action*="delete-portada"])');
    otherDeleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.querySelector('.btn-spinner')) {
                showSpinnerWithText(submitBtn, 'Eliminando...');
            }
        });
    });

    // Spinners para formularios de toggle/estado
    const toggleForms = document.querySelectorAll('form[action*="toggle"]');
    toggleForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.querySelector('.btn-spinner')) {
                showSpinnerWithText(submitBtn, 'Actualizando...');
            }
        });
    });
}); 