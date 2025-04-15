<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Zorgt ervoor dat MySQL fouten worden gegooid als exceptions

$serveradress = getenv("CLOUDRON_MYSQL_HOST");
$gebruikersnaam = getenv("CLOUDRON_MYSQL_USERNAME");
$wachtwoord = getenv("CLOUDRON_MYSQL_PASSWORD");
$dbnaam = getenv("CLOUDRON_MYSQL_DATABASE");

try {
    $conn = new mysqli($serveradress, $gebruikersnaam, $wachtwoord, $dbnaam);
    $conn->set_charset("utf8mb4"); // Zorgt voor correcte tekencodering
} catch (mysqli_sql_exception $e) {
    // Log foutmelding
    $errorMessage = "❌" . date("Y-m-d H:i:s") . " - Er is een fout opgereden in de database met error message (db, connect): " . $e->getMessage() . "\n";
    file_put_contents("../backend/log/log.txt", $errorMessage, FILE_APPEND);

    // Toon een generieke foutmelding aan de gebruiker
    die("Er is een probleem opgetreden met de website. Neem contact op met de beheerder van deze website.");
}
?>
