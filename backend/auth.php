<?php
session_start();
require_once 'config/db.php'; // runt database config 1 keer



// !  Inloggen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $gebruikersnaam = $_POST['username'];
    $wachtwoord = $_POST['password'];

    // SQL-injectie voorkomen met prepared statements
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $gebruikersnaam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Controleer of de gebruiker bestaat
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Controleer of het wachtwoord correct is
        if (password_verify($wachtwoord, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['username'];
        } else {
            $error_message = "Incorrect wachtwoord!"; // geen goed wachtwoord
        }
    } else {
        $error_message = "Geen account gevonden met deze gebruikersnaam!"; // geen account gevonden met deze gebruikersnaam
    }
    $stmt->close();
}



// ! Uitloggen
if (isset($_POST['logout'])) {
    session_destroy(); // logout de sessie
    header("Location: login.php"); // brengt je terug naar login.php
    exit();
}
?>