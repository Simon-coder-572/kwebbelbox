<?php
require_once '../backend/controller/userController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    if (createUser($username, $password, $email)) {
        echo "<script>alert('Account succesvol aangemaakt!'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Er is iets misgegaan of het e-mailadres is al in gebruik.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/main2.css">
    <title>Registratie</title>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var toggleIcon = document.getElementById("togglePassword");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.innerText = "🙈"; // Oogje dicht
            } else {
                passwordField.type = "password";
                toggleIcon.innerText = "👀"; // Oogje open
            }
        }
    </script>
</head>
<body>
    <div class="navbar">
        <ul>
            <li><a href="Homepage.php">Home</a></li>
            <li><a href="Groepen.php">Chats</a></li>
            <li><a href="Instellingen.php">  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" /><circle cx="12" cy="12" r="3" /></svg></a></li>
            <li><a href="login.php" class="active">Login</a></li>
        </ul>
    </div>

    <div class="login-container">
        <!-- dit is gekoppeld aan het php hierboven -->
        <form action="signup.php" method="POST">
            <h2>Maak een hier je account aan</h2>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Wachtwoord:</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required>
                    <span id="togglePassword" style="cursor: pointer; position: absolute; right: 10px; top: 5px;" onclick="togglePassword()">👀</span>
                </div><br>

            <input type="submit" value="Registreren">

        <!-- Toon het bericht als er een is -->
        <?php if (!empty($signupMessage)) { echo "<p>$signupMessage</p>"; } ?>
        </form>
    </div>
    <script src="Dark.js"></script>
</body>
</html>
