const inputBox = document.getElementById('inputBox');
const sendBtn = document.getElementById('sendBtn');
const messages = document.getElementById('messages');

function appendMessage(sender, text) {
    const div = document.createElement('div');
    div.classList.add('message', sender);
    div.innerHTML = text;
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
}

async function sendMessage() {
    const message = inputBox.value.trim();
    if (!message) return;
    appendMessage('user', message);
    inputBox.value = '';

    try {
        const response = await fetch('chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(message)
        });
        const data = await response.json();
        appendMessage('bot', data.reply.replace(/\n/g, '<br>'));
    } catch (err) {
        appendMessage('bot', 'Error connecting to chatbot.');
    }
}

sendBtn.addEventListener('click', sendMessage);
inputBox.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') sendMessage();
});

document.getElementById('useLocationBtn').addEventListener('click', () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(position => {
      const lat = position.coords.latitude;
      const lon = position.coords.longitude;

      fetch('chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `lat=${lat}&lon=${lon}`
      })
      .then(res => res.json())
      .then(data => addMessage(data.reply, 'bot'))
      .catch(() => addMessage("Couldn't fetch weather for your location 😔", 'bot'));
    });
  } else {
    addMessage("Your browser doesn't support location detection 😕", 'bot');
  }
});

