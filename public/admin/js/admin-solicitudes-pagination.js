// Script para paginación AJAX de solicitudes admin (sin Vite ni Mix)
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('tabla-solicitudes-container');
    if (!container) return;
    container.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' && e.target.closest('.admin-solicitudes-pagination')) {
            e.preventDefault();
            const url = e.target.getAttribute('href');
            if (!url) return;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;
                });
        }
    });
});
