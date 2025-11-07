// Hent elementer fra DOM
const inputBox = document.getElementById('inputBox'); // Tekstfelt
const sendBtn = document.getElementById('sendBtn');   // Send-knapp
const messages = document.getElementById('messages'); // Meldingsvindu

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
    messages.scrollTop = messages.scrollHeight; // scroll til siste melding
}

/**
 * Send melding til PHP og vis bot-svar
 * @param {string} message
 * @param {number} lat - valgfritt, geografisk breddegrad
 * @param {number} lon - valgfritt, geografisk lengdegrad
 */
async function sendMessage(message = null, lat = null, lon = null) {
    const msg = message || inputBox.value.trim();
    if (!msg) return;

    if (!message) inputBox.value = ''; // tøm tekstfelt hvis bruker skrev selv
    appendMessage('user', msg);       // vis bruker-melding

    try {
        // Bygg POST-data
        let body = 'message=' + encodeURIComponent(msg);
        if (lat !== null && lon !== null) {
            body += `&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`;
        }

        // Send til chat.php
        const response = await fetch('user/chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        });

        const data = await response.json();
        appendMessage('bot', data.reply.replace(/\n/g, '<br>'));

    } catch (err) {
        appendMessage('bot', 'Error connecting to chatbot.');
        console.error(err);
    }
}

// Send melding ved klikk på knapp
sendBtn.addEventListener('click', () => sendMessage());

// Send melding ved Enter-tast
inputBox.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') sendMessage();
});

/**
 * Funksjon for å bruke brukerens posisjon (valgfritt)
 */
async function sendLocation() {
    if (!navigator.geolocation) {
        appendMessage('bot', 'Geolocation is not supported by your browser.');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            sendMessage('My location', lat, lon);
        },
        (err) => {
            appendMessage('bot', 'Could not get your location.');
            console.error(err);
        }
    );
}

// Hvis du vil legge til en knapp for "Bruk min posisjon", kan du koble den slik:
// document.getElementById('locationBtn').addEventListener('click', sendLocation);
