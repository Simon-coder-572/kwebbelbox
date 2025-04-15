<?php
require_once '../backend/auth.php';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/userLogin.css">
    <link rel="stylesheet" href="src/main2.css">
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
            <li><a href="homepage.php">Home</a></li>
            <li><a href="Groepen.php">Chats</a></li>
            <li><a href="Instellingen.php"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" /><circle cx="12" cy="12" r="3" /></svg></a></li>
            <li><a href="login.php" class="active">Login</a></li>
        </ul>
    </div>
    <div class="login-container">
        <form action="login.php" method="POST">
            <center>
                <h2>
                    <?php 
                        echo isset($_SESSION['user_name']) 
                        ? "Welkom, " . htmlspecialchars($_SESSION['user_name']) . "! <br> Je bent succesvol ingelogd." 
                        : "Log hier in:";
                    ?>
                </h2>
            </center>

            <?php if (!isset($_SESSION['user_name'])): ?> <!-- als er geeb gebruiker is ingelogd is ingelogd -->
                <label for="username">Gebruikersnaam:</label>
                <input type="text" id="username" name="username" required><br><br>

                <label for="password">Wachtwoord:</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required>
                    <span id="togglePassword" style="cursor: pointer; position: absolute; right: 10px; top: 5px;" onclick="togglePassword()">👀</span>
                </div>
                <br>
                <input type="submit" name="login" value="Log in">
                <p>Heb je nog geen account? <a href="signup.php">Maak hier een account</a></p>
            <?php else: ?> <!-- als er al een gebruiker is ingelogd -->
                <input type="submit" name="logout" value="Log uit">
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <p style='color: red;'><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
    </div>
    <script src="Dark.js"></script>
</body>
</html>