<!-- Chatbot -->
<div class="chatbot-container" id="chatbot-container">
    <div class="chatbot-header">
        <h3>Afya Hospital Assistant</h3>
        <button id="chatbot-close"><i class="fas fa-times"></i></button>
    </div>
    <div class="chatbot-messages" id="chatbot-messages">
        <div class="message bot-message">
            <div class="message-content">
                Hello! I'm your Afya Hospital virtual assistant. How can I help you today?
            </div>
            <div class="message-time">Just now</div>
        </div>
    </div>
    <div class="chatbot-input">
        <input type="text" id="chatbot-input-field" placeholder="Type your message...">
        <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<div class="chatbot-button" id="chatbot-button">
    <i class="fas fa-comment-dots"></i>
</div>

<style>
    /* Chatbot Styles */
    .chatbot-container {
        position: fixed;
        bottom: 5rem;
        right: 2rem;
        width: 350px;
        height: 450px;
        background-color: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        z-index: 1000;
        display: none;
    }

    .chatbot-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background-color: #0087ff;
        color: #ffffff;
    }

    .chatbot-header h3 {
        margin: 0;
        font-size: 1rem;
    }

    .chatbot-header button {
        background: none;
        border: none;
        color: #ffffff;
        cursor: pointer;
        font-size: 1rem;
    }

    .chatbot-messages {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;
    }

    .message {
        margin-bottom: 1rem;
        max-width: 80%;
    }

    .user-message {
        margin-left: auto;
    }

    .bot-message {
        margin-right: auto;
    }

    .message-content {
        padding: 0.75rem;
        border-radius: 0.5rem;
    }

    .user-message .message-content {
        background-color: #0087ff;
        color: #ffffff;
        border-top-right-radius: 0;
    }

    .bot-message .message-content {
        background-color: #e5e7eb;
        color: #333333;
        border-top-left-radius: 0;
    }

    .message-time {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
        text-align: right;
    }

    .chatbot-input {
        display: flex;
        padding: 0.75rem;
        border-top: 1px solid #e5e7eb;
    }

    .chatbot-input input {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        margin-right: 0.5rem;
    }

    .chatbot-input button {
        background-color: #0087ff;
        color: #ffffff;
        border: none;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        cursor: pointer;
    }

    .chatbot-button {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 3.5rem;
        height: 3.5rem;
        background-color: #0087ff;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        z-index: 999;
        font-size: 1.5rem;
    }

    @media (max-width: 768px) {
        .chatbot-container {
            width: 300px;
            height: 400px;
            bottom: 4.5rem;
            right: 1rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chatbot functionality
    const chatbotButton = document.getElementById('chatbot-button');
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const chatbotInput = document.getElementById('chatbot-input-field');
    const chatbotSend = document.getElementById('chatbot-send');
    
    chatbotButton.addEventListener('click', function() {
        chatbotContainer.style.display = 'flex';
        chatbotButton.style.display = 'none';
    });
    
    chatbotClose.addEventListener('click', function() {
        chatbotContainer.style.display = 'none';
        chatbotButton.style.display = 'flex';
    });
    
    function sendMessage() {
        const message = chatbotInput.value.trim();
        if (message === '') return;

        addMessage(message, 'user');
        chatbotInput.value = '';

        // Show typing indicator
        const typing = document.createElement('div');
        typing.className = 'message bot-message';
        typing.id = 'typing-indicator';
        typing.innerHTML = '<div class="message-content" style="color:#888;font-style:italic">Typing…</div>';
        chatbotMessages.appendChild(typing);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

        fetch('chatbot-process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        })
        .then(res => res.json())
        .then(data => {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) indicator.remove();
            if (data.status === 'success') {
                addMessage(data.response, 'bot');
            } else {
                addMessage("Sorry, I couldn't process that. Please try again or call 1-800-AFYA.", 'bot');
            }
        })
        .catch(() => {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) indicator.remove();
            addMessage("Sorry, something went wrong. Please call us at 1-800-AFYA for assistance.", 'bot');
        });
    }

    function addMessage(message, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.textContent = message;

        const timeDiv = document.createElement('div');
        timeDiv.className = 'message-time';
        const now = new Date();
        timeDiv.textContent = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');

        messageDiv.appendChild(contentDiv);
        messageDiv.appendChild(timeDiv);
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    chatbotSend.addEventListener('click', sendMessage);

    chatbotInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
});
</script>