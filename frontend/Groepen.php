<?php
// Start de sessie om gebruikersgegevens op te halen
session_start();

// Include databaseconfiguratie en controller
require_once '../backend/config/db.php';
require_once '../backend/controller/groupChatController.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Log eerst in voordat je bij jouw chats kan'); window.location.href = 'login.php';</script>";
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle sending a group message
    if (isset($_POST['send_group_message'])) {
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        $group_id = intval($_POST['group_id']);
        
        if (!empty($message)) {
            sendGroupMessage($group_id, $userId, $message);  // Send the group message
        }
    }
}

// Fetch group messages when the group is selected
if (isset($_GET['view_group_messages'])) {
    $group_id = intval($_GET['view_group_messages']);
    $group = $conn->query("SELECT groepnaam FROM groepen WHERE groep_id = $group_id")->fetch_assoc();
    $group_messages = getGroupMessages($group_id);  // Fetch group messages for the selected group
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="src/main2.css">
    <title>kwebbelbox</title>
</head>
<body>    
    <div class="navbar">
        <ul>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="Groepen.php" class="active">Chats</a></li>
            <li><a href="Instellingen.php"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" /><circle cx="12" cy="12" r="3" /></svg></a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </div>

    <br><br>

    <a href="GroepenAB.php" class="button-class">Groep aanmaken en beheren</a>

    <br><br>
    <!-- Laat zien de groepen die jij gemaakt heeft-->
        <h2>Zelf gemaakte groepen</h2>
<?php
$query = "SELECT * FROM groepen WHERE created_by = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
        $gid = $row['groep_id'];
?>
    <div class="table-container">
        <table>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($row['groepnaam']) ?></strong> - <?= htmlspecialchars($row['beschrijving']) ?>
                    <form method="get" style="display:inline;">
                        <input type="hidden" name="view_group_messages" value="<?= $gid ?>">
                        <button type="submit" class="btn btn-third">Berichten</button>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <br>
<?php
    endwhile;
else:
?>
    <p>Geen groepen gevonden.</p>
<?php endif; ?>

<!-- Laat zien de groepen die jij gemaakt heeft-->
<h2>Deelgenome groepen</h2>
<?php
$query = "SELECT g.groep_id, g.groepnaam, g.beschrijving 
          FROM groepen g
          JOIN user_groep ug ON g.groep_id = ug.group_id
          WHERE ug.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
    <div class="table-container">
        <table>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($row['groepnaam']) ?></strong> - <?= htmlspecialchars($row['beschrijving']) ?>
                    <form method="get" style="display:inline;">
                        <input type="hidden" name="view_group_messages" value="<?= $row['groep_id'] ?>">
                        <button type="submit" class="btn btn-third">Berichten</button>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <br>
<?php
    endwhile;
endif;
?>

<!--Groep berichten verzenden-->
<div class="management-container">
            <div class="management-section">
                <h3>Groep Bericht Verzenden</h3>
                <?php if (isset($_GET['view_group_messages'])): 
                $group_id = intval($_GET['view_group_messages']);
                $group = $conn->query("SELECT groepnaam FROM groepen WHERE groep_id = $group_id")->fetch_assoc();
                $group_messages = getGroupMessages($group_id);
                ?>
                <h4>Berichten in de groep: <?= htmlspecialchars($group['groepnaam']) ?></h4>
                
                <!-- Form to send a new group message -->
                 <form method="POST">
                    <input type="text" name="message" placeholder="Schrijf een bericht..." rows="3" required>
                    <input type="hidden" name="group_id" value="<?= $group_id ?>">
                    <button type="submit" name="send_group_message" class="btn-primary">Verstuur Bericht</button>
                </form>
                
                <!--Laat de berichting zien dat in de database zit-->
                <h5>Groep Berichten:</h5>
                <?php if (!empty($group_messages)): ?>
                    <ul>
                        <?php foreach ($group_messages as $message): ?>
                            <li> <?= htmlspecialchars($message['username']) ?>: <strong><?= htmlspecialchars($message['message']) ?> </strong><em>(<?= $message['created_at'] ?>)</em><strong></strong></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                            <p>Er zijn nog geen berichten in deze groep.</p>
                            <?php endif; ?>
                            <?php endif; ?>
            </div>
        </div>

        <script src="../backend/controller/zoekbalk.js"></script>
    </main>
<script src="Dark.js"></script>
</body>
</html>