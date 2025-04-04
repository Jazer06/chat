<?php
use yii\helpers\Html;
$this->registerCssFile('@web/css/chat.css');

?>
<h1>Чат</h1>

<p>Имя пользователя: <strong><?= Html::encode($username) ?></strong></p>

<div id="connection-status" class="status">
    Состояние: <span id="status-text">Проверка подключения...</span>
</div>

<div id="messages" class="messages"></div>
<input type="text" id="message-input" placeholder="Введите сообщение..." class="input-message" />
<button onclick="sendMessage()" class="send-button">Отправить</button>

<style >
body {
    font-family: Arial, sans-serif;
    background-color: #121212; 
    color: #ffffff; 
    padding: 20px; 
}

</style>
<script>
let conn;

function initChat() {
    conn = new WebSocket('ws://localhost:8085');
    
    conn.onopen = function() {
        updateStatus('Подключено', 'success');
    };
    
    conn.onerror = function(e) {
        updateStatus('Ошибка подключения', 'error');
    };
    
    conn.onclose = function(e) {
        updateStatus('Отключено', 'warning');
        setTimeout(initChat, 5000);
    };
    
    conn.onmessage = function(e) {
        const data = JSON.parse(e.data);
        addMessageToChat(data.username, data.message, false);
    };
    
    document.getElementById('message-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });
}

function addMessageToChat(username, message, isHistory) {
    const messagesDiv = document.getElementById('messages');
    const currentUsername = "<?= Html::encode($username) ?>";
    
    const messageElement = document.createElement('div');
    messageElement.classList.add('message');
    
    if (username === currentUsername) {
        messageElement.classList.add('me');
    } else {
        messageElement.classList.add('other');
    }
    
    const avatar = document.createElement('img');
    avatar.classList.add('avatar');
    avatar.style.width = '40px';
    avatar.style.height = '40px';
    avatar.style.objectFit = 'cover';
    avatar.style.borderRadius = '50%';
    avatar.style.marginRight = '10px';
    

    if (username === currentUsername) {
        avatar.src = '/images/amy.png'; 
    } else {
        avatar.src = '/images/tamy.png'; 
    }

    const content = document.createElement('div');
    content.classList.add('content');
    
    const name = document.createElement('div');
    name.classList.add('name'); 
    name.textContent = username;

    name.style.color = 'black'; 
    name.style.fontSize = '16px'; 
    
    const text = document.createElement('div');
    text.classList.add('text');
    text.textContent = message;
    
    const time = document.createElement('div');
    time.classList.add('time');
    time.textContent = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    content.appendChild(name);
    content.appendChild(text);
    content.appendChild(time);
    
    if (username !== currentUsername) {
        messageElement.appendChild(avatar);
    }
    messageElement.appendChild(content);
    if (username === currentUsername) {
        messageElement.appendChild(avatar);
    }
    
    messagesDiv.appendChild(messageElement);
    
    if (!isHistory) {
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
}

function updateStatus(text, color) {
    const statusElement = document.getElementById('status-text');
    statusElement.textContent = text;
    statusElement.parentElement.classList.remove('success', 'error', 'warning');
    statusElement.parentElement.classList.add(color);
}

function sendMessage() {
    if (!conn || conn.readyState !== WebSocket.OPEN) {
        alert('Нет подключения к серверу!');
        return;
    }
    
    const input = document.getElementById('message-input');
    const message = input.value.trim();
    
    if (message) {
        const data = { 
            username: "<?= Html::encode($username) ?>", 
            message 
        };
        conn.send(JSON.stringify(data));
        input.value = '';
     
    }
}

document.addEventListener('DOMContentLoaded', initChat);
</script>
