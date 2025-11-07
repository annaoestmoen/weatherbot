<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weatherbot</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div id="chatbox">
        <h2>Weatherbot</h2>

        <!-- Meldingsvindu -->
        <div id="messages"></div>

        <!-- Tekstfelt for brukerinput -->
        <input type="text" id="inputBox" placeholder="Ask about the weather in any city!" />

        <!-- Send-knapp -->
        <button id="sendBtn">Send</button>
    </div>

    <!-- Admin-login-link -->
    <p style="text-align:center; margin-top: 10px;">
      <a href="admin/login.php" style="font-size: 0.9em;">Admin login</a>
    </p>

    <!-- Link til JavaScript -->
    <script src="assets/js/chat.js"></script>
</body>
</html>



