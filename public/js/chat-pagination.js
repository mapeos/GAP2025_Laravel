document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.chat-pagination').forEach(function (container) {
        container.addEventListener('click', function (e) {
            if (e.target.tagName === 'A' && e.target.closest('.pagination')) {
                e.preventDefault();
                const url = e.target.getAttribute('href');
                if (!url) return;
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
                        // Extraer solo el fragmento de la lista de usuarios
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newList = doc.querySelector(`#${container.id}`);
                        if (newList) {
                            container.innerHTML = newList.innerHTML;
                        }
                    });
            }
        });
    });
});
