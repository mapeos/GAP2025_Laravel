// Lógica JS para chat tipo WhatsApp Web
// Carga y muestra la conversación seleccionada en el panel principal del index
import axios from 'axios';

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
        let html = `<div class='w-100' style='max-width:600px;margin:0 auto;'>`;
        html += `<h4 class='mb-3'>Conversación con ${user.name}</h4>`;
        html += `<div class='chat-mensajes' style='max-height:350px;overflow-y:auto;margin-bottom:1.5rem;background:#f8fafc;border-radius:12px;padding:1.2rem 1rem;box-shadow:0 1px 4px 0 rgba(0,0,0,0.03);'>`;
        if (mensajes.length === 0) {
            html += `<p class='text-muted'>No hay mensajes aún.</p>`;
        } else {
            mensajes.forEach(m => {
                const tu = m.senderId == authId;
                html += `<div class='chat-mensaje${tu ? ' tu-mensaje' : ''}' style='margin-bottom:1.1rem;display:flex;align-items:flex-start;gap:0.7rem;'>`;
                html += `<span class='avatar' style='width:36px;height:36px;border-radius:50%;background:#e9ecef;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#6c757d;flex-shrink:0;'><i class='ri-user-3-line'></i></span>`;
                html += `<div class='contenido' style='background:${tu ? '#e6f7e6' : '#fff'};border-radius:10px;padding:0.7rem 1rem;box-shadow:0 1px 2px 0 rgba(0,0,0,0.03);min-width:80px;${tu ? 'border:1px solid #b6e6b6;' : ''}'>`;
                html += `<span class='nombre' style='font-weight:600;font-size:0.98em;'>${tu ? 'Tú' : user.name}</span><br>`;
                html += `<span>${m.content}</span><br>`;
                html += `<small style='color:#6c757d;font-size:0.85em;'>${m.createdAt_fmt ?? ''}</small>`;
                html += `</div></div>`;
            });
        }
        html += `</div>`;
        html += `<form class='chat-send-form' autocomplete='off'>`;
        html += `<div class='input-group chat-input-group'><input type='text' name='mensaje' class='form-control' placeholder='Escribe un mensaje...' required maxlength='2000'><button class='btn btn-success' type='submit'>Enviar</button></div>`;
        html += `</form></div>`;
        mainPanel.innerHTML = html;
        scrollToBottom(mainPanel.querySelector('.chat-mensajes'));
        // Envío de mensaje
        mainPanel.querySelector('.chat-send-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const input = this.querySelector('input[name="mensaje"]');
            const mensaje = input.value.trim();
            if (!mensaje) return;
            this.querySelector('button[type="submit"]').disabled = true;
            axios.post(`/chat/${user.id}`, { mensaje })
                .then(resp => {
                    if (resp.data && resp.data.mensajes) {
                        renderChat(user, resp.data.mensajes, authId);
                    } else {
                        input.value = '';
                        this.querySelector('button[type="submit"]').disabled = false;
                    }
                })
                .catch(() => {
                    this.querySelector('button[type="submit"]').disabled = false;
                });
        });
    }

    chatItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.dataset.userId;
            if (userId == currentUserId) return;
            currentUserId = userId;
            chatItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            renderLoading();
            axios.get(`/chat/${userId}?ajax=1`).then(resp => {
                if (resp.data && resp.data.user && resp.data.mensajes) {
                    renderChat(resp.data.user, resp.data.mensajes, resp.data.authId);
                } else {
                    mainPanel.innerHTML = `<div class='text-center text-muted py-5'>No se pudo cargar la conversación.</div>`;
                }
            });
        });
    });
});
