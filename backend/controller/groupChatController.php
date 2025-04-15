<?php
require_once '../backend/config/db.php';

function logError($message) {
    $logFile = "../backend/log/log.txt";
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

function createGroup($groepnaam, $beschrijving, $created_by) {
    global $conn;
    try {
        $stmt = $conn->prepare("INSERT INTO groepen (groepnaam, beschrijving, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $groepnaam, $beschrijving, $created_by);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        logError("Groep aanmaken mislukt: " . $e->getMessage());
        return false;
    }
}

function deleteGroup($groep_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("DELETE FROM groepen WHERE groep_id = ?");
        $stmt->bind_param("i", $groep_id);
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        logError("Groep verwijderen mislukt: " . $e->getMessage());
        return false;
    }
}

function updateGroup($groep_id, $groepnaam, $beschrijving) {
    global $conn;
    try {
        $stmt = $conn->prepare("UPDATE groepen SET groepnaam = ?, beschrijving = ? WHERE groep_id = ?");
        $stmt->bind_param("ssi", $groepnaam, $beschrijving, $groep_id);
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        logError("Groep bijwerken mislukt: " . $e->getMessage());
        return false;
    }
}

function addUserToGroup($user_id, $group_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("INSERT INTO user_groep (user_id, group_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $group_id);
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        logError("Gebruiker toevoegen mislukt: " . $e->getMessage());
        return false;
    }
}

function removeUserFromGroup($user_id, $group_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("DELETE FROM user_groep WHERE user_id = ? AND group_id = ?");
        $stmt->bind_param("ii", $user_id, $group_id);
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        logError("Gebruiker verwijderen mislukt: " . $e->getMessage());
        return false;
    }
}

function getGroupMembers($group_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT u.id, u.username 
            FROM user_groep ug
            JOIN users u ON ug.user_id = u.id
            WHERE ug.group_id = ?
        ");
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception $e) {
        logError("Leden ophalen mislukt: " . $e->getMessage());
        return false;
    }
}

// Function to retrieve group messages for a specific group
function getGroupMessages($groupId) {
    global $conn;
    $query = "SELECT m.*, u.username 
              FROM group_messages m 
              JOIN users u ON m.user_id = u.id 
              WHERE m.group_id = ? 
              ORDER BY m.created_at ASC";  // Make sure `created_at` is the correct column name
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $groupId);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    return $messages;
}


// Function to send a new group message
function sendGroupMessage($groupId, $userId, $message) {
    global $conn;
    $query = "INSERT INTO group_messages (group_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $groupId, $userId, $message);
    $stmt->execute();
}


?>