document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.user-search-input').forEach(function (input) {
        let timeout = null;
        const rol = input.dataset.rol;
        const userList = document.getElementById(`user-list-${rol}`);
        function fetchUsers(page = 1) {
            const search = input.value;
            const url = `/chat/search/users?rol=${rol}&search=${encodeURIComponent(search)}&page=${page}`;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    userList.innerHTML = html;
                });
        }
        input.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                fetchUsers();
            }, 250);
        });
        // Delegar paginación AJAX
        userList.addEventListener('click', function (e) {
            if (e.target.classList.contains('pagination-ajax-link')) {
                e.preventDefault();
                const page = e.target.dataset.page;
                fetchUsers(page);
            }
        });
    });
});
