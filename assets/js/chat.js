// Hent elementer fra DOM
const inputBox = document.getElementById('inputBox'); // Tekstfelt for brukerinput
const sendBtn = document.getElementById('sendBtn'); // Send-knapp
const messages = document.getElementById('messages'); // Container for meldinger

/**
 * Legg til melding i chatten
 * @param {string} sender - 'user' eller 'bot'
 * @param {string} text - Meldingstekst
 */
function appendMessage(sender, text) {
    const div = document.createElement('div');
    div.classList.add('message', sender);
    div.innerHTML = text;
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight; // Scroll ned til siste melding
}

/**
 * Send brukerens melding til PHP og vis svar
 */
async function sendMessage() {
    const message = inputBox.value.trim();
    if (!message) return; // Ingen melding, gjør ingenting

    appendMessage('user', message); // Vis brukerens melding
    inputBox.value = ''; // Tøm tekstfelt

    try {
        const response = await fetch('chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(message) // Send melding
        });
        const data = await response.json();
        appendMessage('bot', data.reply.replace(/\n/g, '<br>')); // Vis bot-svar
    } catch (err) {
        appendMessage('bot', 'Error connecting to chatbot.');
    }
}

// Send melding ved klikk på knapp
sendBtn.addEventListener('click', sendMessage); 

// Send melding ved Enter-tast
inputBox.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') sendMessage();
});