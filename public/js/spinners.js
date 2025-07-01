// ========================================
// SISTEMA DE SPINNERS Y CONFIRMACIONES
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // FUNCIONES AUXILIARES
    // ========================================
    
    /**
     * Mostrar spinner en un botón
     */
    function showSpinner(button) {
        const btnText = button.querySelector('.btn-text');
        const btnSpinner = button.querySelector('.btn-spinner');
        
        if (btnText && btnSpinner) {
            btnText.style.display = 'none';
            btnSpinner.style.display = 'inline-flex';
            button.disabled = true;
        }
    }
    
    /**
     * Mostrar spinner con texto personalizado
     */
    function showSpinnerWithText(button, text) {
        const originalText = button.innerHTML;
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${text}`;
        button.disabled = true;
        button.dataset.originalText = originalText;
    }
    
    /**
     * Restaurar botón a su estado original
     */
    function restoreButton(button) {
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText;
            delete button.dataset.originalText;
        }
        button.disabled = false;
    }
    
    /**
     * Función robusta para confirmación de eliminación
     */
    function handleDeleteConfirmation(form, confirmMessage = '¿Estás seguro de que quieres eliminar este elemento? Esta acción no se puede deshacer.') {
        const confirmacion = confirm(confirmMessage);
        
        if (!confirmacion) {
            return false; // Cancelar la acción
        }
        
        // Si se confirma, mostrar spinner
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            if (submitBtn.querySelector('.btn-spinner')) {
                showSpinner(submitBtn);
            } else {
                showSpinnerWithText(submitBtn, 'Eliminando...');
            }
        }
        
        return true; // Continuar con la eliminación
    }

    // ========================================
    // SPINNERS PARA FORMULARIOS PRINCIPALES
    // ========================================
    
    // Spinners para formularios de creación/edición
    const mainForms = document.querySelectorAll('form[id*="Crear"], form[id*="Editar"], form[id*="Update"]');
    mainForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && submitBtn.querySelector('.btn-spinner')) {
                showSpinner(submitBtn);
            }
        });
    });

    // ========================================
    // SPINNERS PARA SUBIDA DE ARCHIVOS
    // ========================================
    
    // Spinners para subida de temarios
    const temarioForms = document.querySelectorAll('form[action*="upload"]:not([action*="portada"])');
    temarioForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && submitBtn.querySelector('.btn-spinner')) {
                showSpinner(submitBtn);
            } else if (submitBtn) {
                showSpinnerWithText(submitBtn, 'Subiendo...');
            }
        });
    });

    // Spinners para subida de portadas
    const portadaForms = document.querySelectorAll('form[action*="upload-portada"]');
    portadaForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && submitBtn.querySelector('.btn-spinner')) {
                showSpinner(submitBtn);
            } else if (submitBtn) {
                showSpinnerWithText(submitBtn, 'Subiendo...');
            }
        });
    });

    // ========================================
    // CONFIRMACIONES DE ELIMINACIÓN MEJORADAS
    // ========================================
    
    // Eliminación de temarios con confirmación robusta
    const deleteTemarioForms = document.querySelectorAll('form[action*="delete-temario"]');
    deleteTemarioForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const confirmed = handleDeleteConfirmation(
                this, 
                '¿Estás seguro de que quieres eliminar el temario? Esta acción no se puede deshacer.'
            );
            
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Eliminación de portadas con confirmación robusta
    const deletePortadaForms = document.querySelectorAll('form[action*="delete-portada"]');
    deletePortadaForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const confirmed = handleDeleteConfirmation(
                this, 
                '¿Estás seguro de que quieres eliminar la portada? Esta acción no se puede deshacer.'
            );
            
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Eliminación de otros elementos con confirmación robusta
    const otherDeleteForms = document.querySelectorAll('form[action*="delete"]:not([action*="delete-temario"]):not([action*="delete-portada"])');
    otherDeleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const confirmed = handleDeleteConfirmation(this);
            
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        });
    });

    // ========================================
    // SPINNERS PARA TOGGLE/ESTADO
    // ========================================
    
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

    // ========================================
    // MANEJO DE ERRORES Y RESTAURACIÓN
    // ========================================
    
    // Restaurar botones en caso de error de red
    window.addEventListener('beforeunload', function() {
        const buttons = document.querySelectorAll('button[disabled]');
        buttons.forEach(button => {
            if (button.dataset.originalText) {
                restoreButton(button);
            }
        });
    });

    // Restaurar botones después de un tiempo si no hay respuesta
    setTimeout(function() {
        const disabledButtons = document.querySelectorAll('button[disabled]');
        disabledButtons.forEach(button => {
            if (button.dataset.originalText && button.closest('form')) {
                // Solo restaurar si el formulario no se envió exitosamente
                const form = button.closest('form');
                if (!form.classList.contains('submitted')) {
                    restoreButton(button);
                }
            }
        });
    }, 10000); // 10 segundos

    // ========================================
    // FUNCIONES GLOBALES PARA USO EXTERNO
    // ========================================
    
    // Hacer las funciones disponibles globalmente
    window.SpinnerUtils = {
        showSpinner: showSpinner,
        showSpinnerWithText: showSpinnerWithText,
        restoreButton: restoreButton,
        handleDeleteConfirmation: handleDeleteConfirmation
    };
}); 