// Lógica JS para chat tipo WhatsApp Web
// Carga y muestra la conversación seleccionada en el panel principal del index

function scrollToBottom(container) {
    container.scrollTop = container.scrollHeight;
}

document.addEventListener('DOMContentLoaded', function() {
    const chatItems = document.querySelectorAll('.wa-chat-item[data-user-id]');
    const mainPanel = document.querySelector('.wa-main');
    let currentUserId = null;

    function renderLoading() {
        mainPanel.innerHTML = `<div class='text-center text-muted py-5'>Cargando conversación...</div>`;
    }

    function renderChat(user, mensajes, authId) {
        // Ordenar mensajes del más antiguo al más reciente (por si no vienen ya ordenados)
        mensajes = mensajes.sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt));
        let html = `<div class='w-100 h-100 d-flex flex-column' style='height:100%;'>`;
        html += `<div class='flex-shrink-0'><h4 class='mb-3'>Conversación con ${user.name}</h4></div>`;
        html += `<div class='chat-mensajes flex-grow-1' style='flex:1 1 0;min-height:0;max-height:100%;overflow-y:auto;margin-bottom:1.5rem;background:#f8fafc;border-radius:12px;padding:1.2rem 1rem;box-shadow:0 1px 4px 0 rgba(0,0,0,0.03);'>`;
        if (mensajes.length === 0) {
            html += `<p class='text-muted'>No hay mensajes aún.</p>`;
        } else {
            mensajes.forEach(m => {
                const tu = m.senderId == authId;
                let avatarHtml = '';
                if (m.foto_perfil) {
                    avatarHtml = `<span class='avatar' style='width:36px;height:36px;border-radius:50%;background:#e9ecef;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#6c757d;flex-shrink:0;'><img src='/storage/${m.foto_perfil}' alt='avatar' style='width:100%;height:100%;object-fit:cover;border-radius:50%;'></span>`;
                } else {
                    avatarHtml = `<span class='avatar' style='width:36px;height:36px;border-radius:50%;background:#e9ecef;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#6c757d;flex-shrink:0;'><i class='ri-user-3-line'></i></span>`;
                }
                html += `<div class='chat-mensaje${tu ? ' tu-mensaje' : ''}' style='margin-bottom:1.1rem;display:flex;align-items:flex-start;gap:0.7rem;${tu ? 'flex-direction:row-reverse;justify-content:flex-end;' : 'justify-content:flex-start;'}'>`;
                html += avatarHtml;
                html += `<div class='contenido' style='background:${tu ? '#e6f7e6' : '#fff'};border-radius:10px;padding:0.7rem 1rem;box-shadow:0 1px 2px 0 rgba(0,0,0,0.03);min-width:80px;${tu ? 'border:1px solid #b6e6b6;margin-left:auto;' : ''}${!tu ? 'margin-right:auto;' : ''}'>`;
                html += `<span class='nombre' style='font-weight:600;font-size:0.98em;'>${tu ? 'Tú' : user.name}</span><br>`;
                html += `<span>${m.content}</span><br>`;
                html += `<small style='color:#6c757d;font-size:0.85em;'>${m.createdAt_fmt ?? ''}</small>`;
                html += `</div>`;
                html += `</div>`;
            });
        }
        html += `</div>`;
        html += `<form class='chat-send-form mt-auto' autocomplete='off'>`;
        html += `<div class='input-group chat-input-group'><input type='text' name='mensaje' class='form-control' placeholder='Escribe un mensaje...' required maxlength='2000'><button class='btn btn-success' type='submit'>Enviar</button></div>`;
        html += `</form></div>`;
        mainPanel.innerHTML = html;
        scrollToBottom(mainPanel.querySelector('.chat-mensajes'));
    }

    // Delegación para evitar recarga incluso si el DOM cambia
    document.body.addEventListener('submit', function(e) {
        if (e.target && e.target.classList.contains('chat-send-form')) {
            e.preventDefault();
            const form = e.target;
            const input = form.querySelector('input[name="mensaje"]');
            const mensaje = input.value.trim();
            if (!mensaje) return;
            form.querySelector('button[type="submit"]').disabled = true;
            const userId = form.closest('.wa-main').dataset.userId || currentUserId;
            window.axios.post(`/chat/${userId}`, { mensaje })
                .then(resp => {
                    if (resp.data && resp.data.mensajes) {
                        renderChat(resp.data.user, resp.data.mensajes, resp.data.authId);
                    }
                })
                .finally(() => {
                    form.querySelector('button[type="submit"]').disabled = false;
                    input.value = '';
                });
        }
    });

    chatItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.dataset.userId;
            if (userId == currentUserId) return;
            currentUserId = userId;
            chatItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            renderLoading();
            window.axios.get(`/chat/${userId}?ajax=1`).then(resp => {
                if (resp.data && resp.data.user && resp.data.mensajes) {
                    renderChat(resp.data.user, resp.data.mensajes, resp.data.authId);
                } else {
                    mainPanel.innerHTML = `<div class='text-center text-muted py-5'>No se pudo cargar la conversación.</div>`;
                }
            });
        });
    });

    // Al cargar la página, si hay ?user_id=... en la URL, simular clic en ese usuario
    const params = new URLSearchParams(window.location.search);
    const preselectId = params.get('user_id');
    if (preselectId) {
        const preItem = document.querySelector(`.wa-chat-item[data-user-id='${preselectId}']`);
        if (preItem) {
            preItem.click();
            // Limpiar el parámetro de la URL para evitar recarga accidental
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('user_id');
                window.history.replaceState({}, '', url.pathname);
            }
        }
    }
});
