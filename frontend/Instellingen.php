<?php
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Log eerst in voordat je bij jouw instellingen kan'); window.location.href = 'login.php';</script>";
    exit;
}


$userId = $_SESSION['user_id'];

// Inclusie van de databaseconfiguratie en backendfuncties
require_once '../backend/config/db.php';
require_once '../backend/controller/userController.php'; // Hier staat ook updateUser() en nu updateUserAndLogout()

// Probeer de update en vang eventuele foutmelding op
$updateMessage = updateUserAndLogout($userId);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Instellingen</title>
    <!--<link rel="stylesheet" href="src/navbar.css">-->
    <link rel="stylesheet" href="src/main2.css">
</head>
<body>
    <div class="navbar">
        <ul>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="Groepen.php">Chats</a></li>
            <li><a href="Instellingen.php" class="active"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" /><circle cx="12" cy="12" r="3" /></svg></a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </div>
    <h1>Instellingen</h1>
    <div class="container">
        <?php if (isset($updateMessage)): ?>
            <div class="message error">
                <?php echo $updateMessage; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Nieuwe Gebruikersnaam</label>
                <input type="text" id="username" name="username" placeholder="Laat leeg om ongewijzigd te blijven">
            </div>
            <div class="form-group">
                <label for="email">Nieuw E-mailadres</label>
                <input type="email" id="email" name="email" placeholder="Laat leeg om ongewijzigd te blijven">
            </div>
            <div class="form-group">
                <label for="password">Nieuw Wachtwoord</label>
                <input type="password" id="password" name="password" placeholder="Laat leeg om ongewijzigd te blijven">
            </div>
            <button type="submit">Wijzigingen Opslaan</button>
        </form>
    </div>
    <br>
    <div>
        <!-- Formulier voor account verwijderen -->
        <form method="post" onsubmit="return confirmDelete();">
            <input type="hidden" name="delete_user" value="1">
            <button type="submit" name="delete" class="delete-btn">Verwijder Account</button>
        </form>
    <br>
        <button onclick="toggleDarkMode()">Toggle Dark/Light Mode</button>

        <script>
        function confirmDelete() {
            return confirm("Weet je zeker dat je je account wilt verwijderen? Dit kan niet ongedaan worden gemaakt.");
        }
        </script>
    </div>
    <script src="Dark.js"></script>
</body>
</html>