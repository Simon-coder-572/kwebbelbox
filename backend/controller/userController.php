<?php
require_once '../backend/config/db.php';

// ! Functie om gebruiker aan te maken
function createUser($gebruikersnaam, $wachtwoord, $email) {
    global $conn; // Zorg dat $conn een geldige databaseverbinding is

    $log = "../backend/log/log.txt"; // Zorg dat dit pad correct is

    // Check of de e-mail al bestaat
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        $errorBericht = "❌ " . date("Y-m-d H:i:s") . " - Er is een fout opgetreden in de database met error message (userController, createUser): " . $conn->error . "\n";
        file_put_contents($log, $errorBericht, FILE_APPEND);
        return false; // Return false bij fout
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return false; // E-mail bestaat al, geen fout loggen
    }

    // Hash het wachtwoord
    $hashedPassword = password_hash($wachtwoord, PASSWORD_DEFAULT);

    // Voeg de gebruiker toe aan de database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        $errorBericht = "❌ " . date("Y-m-d H:i:s") . " - Er is een fout opgetreden in de database met error message (userController, createUser): " . $conn->error . "\n";
        file_put_contents($log, $errorBericht, FILE_APPEND);
        return false; // Return false bij fout
    }

    $stmt->bind_param("sss", $gebruikersnaam, $email, $hashedPassword);

    if ($stmt->execute()) {
        $logBericht = "✅ " . date("Y-m-d H:i:s") . " - Gebruiker aangemaakt met gebruikersnaam: *$gebruikersnaam* en met e-mail: *$email*\n";
        file_put_contents($log, $logBericht, FILE_APPEND);
        return true; // Account succesvol aangemaakt
    } else {
        $errorBericht = "❌ " . date("Y-m-d H:i:s") . " - Er is een fout opgetreden in de database met error message (userController, createUser): " . $stmt->error . "\n";
        file_put_contents($log, $errorBericht, FILE_APPEND);
        return false; // Database-invoegfout
    }
}



// ! Functie om gebruiker te verwijderen
function deleteUser($userId) {
    global $conn;
    $log = "../backend/log/log.txt"; // Zorg ervoor dat het pad correct is

    // Bereid de DELETE query voor
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if (!$stmt) {
        $errorBericht = "❌ " . date("Y-m-d H:i:s") . " - Er is een fout opgetreden in de database met error message (userController, createUser): " . $conn->error . "\n";
        file_put_contents($log, $errorBericht, FILE_APPEND);
        return false; // Return false bij fout
    }
    
    $stmt->bind_param("i", $userId);

    // Voer de query uit
    if ($stmt->execute()) {
        $logBericht = "✅ " . date("Y-m-d H:i:s") . " - Gebruiker succesvol verwijderd: ID = $userId\n";
        file_put_contents($log, $logBericht, FILE_APPEND);
        return true; // Succesvol verwijderd
    } else {
        $errorMessage = "❌ " . date("Y-m-d H:i:s") . " - Fout bij het verwijderen van gebruiker (userController, createUser): ID = $userId, Error: " . $stmt->error . "\n";
        file_put_contents($log, $errorBericht ="Werk niet", FILE_APPEND);
        return false; // Fout bij verwijderen
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $logBestand = "../backend/log/log.txt"; // Zorg ervoor dat het pad correct is

        if (deleteUser($userId)) {
            // Log succes
            $logMessage = "✅ " . date("Y-m-d H:i:s") . " - Gebruiker met ID (userController, deleteUser): $userId heeft zijn/haar account succesvol verwijderd.\n";
            file_put_contents($logBestand, $logMessage, FILE_APPEND);

            // Sessie beëindigen en cookies verwijderen
            session_destroy();
            setcookie("username", "", time() - 3600, "/");

            // Redirect naar login of homepagina
            echo "<script>alert('Je account is succesvol verwijderd.'); window.location.href = 'login.php';</script>";
            exit;
        } else {
            // Log fout bij verwijderen van account
            $errorMessage = "❌ " . date("Y-m-d H:i:s") . " - Fout bij het verwijderen van het account voor gebruiker met ID (userController, deleteUser): $userId.\n";
            file_put_contents($logBestand, $errorMessage, FILE_APPEND);

            echo "<script>alert('Er is een fout opgetreden bij het verwijderen van je account.');</script>";
        }
    }
}

// ! Functie gebruikergegevens aan te passen
function updateUserAndLogout($userId) {
    // Zorg ervoor dat deze functie alleen wordt uitgevoerd bij een POST-request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Haal de waarden op uit het formulier; lege velden blijven ongewijzigd
        $username = (!empty($_POST['username'])) ? trim($_POST['username']) : null;
        $email    = (!empty($_POST['email'])) ? trim($_POST['email']) : null;
        $password = (!empty($_POST['password'])) ? trim($_POST['password']) : null;

        global $conn;
        $logBestand = "../backend/log/log.txt"; // Zorg ervoor dat het pad correct is

        // Bouw de velden, parameters en types op voor de UPDATE-query
        $fields = [];
        $params = [];
        $types = "";

        if ($username !== null) {
            $fields[] = "username = ?";
            $params[] = $username;
            $types .= "s";
        }

        if ($email !== null) {
            $fields[] = "email = ?";
            $params[] = $email;
            $types .= "s";
        }

        if ($password !== null) {
            // Hash het nieuwe wachtwoord
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $fields[] = "password = ?";
            $params[] = $hashedPassword;
            $types .= "s";
        }

        // Als er niets is om te updaten, dan stoppen we de functie
        if (empty($fields)) {
            return "Er is niets ingevuld om te updaten.";
        }

        // Voeg de userId toe aan de parameters en type (integer)
        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        $params[] = $userId;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            // Log fout bij voorbereiden van de query
            $errorMessage = "❌ " . date("Y-m-d H:i:s") . " - Fout bij voorbereiden van de query voor updateUserAndLogout (userController, updateUserAndLogout): " . $conn->error . "\n";
            file_put_contents($logBestand, $errorMessage, FILE_APPEND);
            return "Er is iets misgegaan. Probeer het opnieuw of neem contact op met de beheerder.";
        }

        // Dynamisch de parameters binden
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            // Update gelukt: log de gebruiker uit

            // Verwijder alle sessievariabelen
            $_SESSION = array();

            // Als er een sessiecookie bestaat, verwijder deze dan
            if (ini_get("session.use_cookies")) {
                $paramsCookie = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $paramsCookie["path"], $paramsCookie["domain"],
                    $paramsCookie["secure"], $paramsCookie["httponly"]
                );
            }

            // Vernietig de sessie
            session_destroy();

            // Indien er cookies voor de gebruikersnaam worden gebruikt, verwijder die ook
            if (isset($_COOKIE['username'])) {
                setcookie("username", "", time() - 3600, "/");
            }

            // Log succes
            $logMessage = "✅ " . date("Y-m-d H:i:s") . " - Gegevens succesvol bijgewerkt voor gebruiker met ID (userController, updateUserAndLogout): $userId\n";
            file_put_contents($logBestand, $logMessage, FILE_APPEND);

            // Redirect naar login.php met een melding
            echo "<script>alert('Je gegevens zijn succesvol aangepast. Log opnieuw in met je nieuwe gegevens.'); window.location.href = 'login.php';</script>";
            exit;
        } else {
            // Log fout bij uitvoeren van de query
            $errorMessage = "❌ " . date("Y-m-d H:i:s") . " - Fout bij het bijwerken van de gegevens voor gebruiker met ID (userController, updateUserAndLogout): $userId, Error: " . $stmt->error . "\n";
            file_put_contents($logBestand, $errorMessage, FILE_APPEND);

            return "Er is iets misgegaan. Probeer het opnieuw of neem contact op met de beheerder.";
        }
    }
    return null;
}

function getAllUsers() {
    global $conn;
    try {
        $result = $conn->query("SELECT id, username FROM users ORDER BY username");
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception $e) {
        logError("Gebruikers ophalen mislukt: " . $e->getMessage());
        return false;
    }
}