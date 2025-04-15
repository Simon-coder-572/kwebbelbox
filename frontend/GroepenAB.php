<?php
session_start();
require_once '../backend/config/db.php';
require_once '../backend/controller/groupChatController.php';
require_once '../backend/controller/userController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$userId = $_SESSION['user_id'];

// Verwerk alle formulieren
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {
        // Groep aanmaken
        $groepNaam = mysqli_real_escape_string($conn, $_POST['groep_naam']);
        $beschrijving = mysqli_real_escape_string($conn, $_POST['beschrijving']);
        
        if (!empty($groepNaam)) {
            createGroup($groepNaam, $beschrijving ?? '', $userId);
        }
    } 
    elseif (isset($_POST['update_group'])) {
        // Groep bijwerken
        $groep_id = intval($_POST['groep_id']);
        $groepNaam = mysqli_real_escape_string($conn, $_POST['groep_naam']);
        $beschrijving = mysqli_real_escape_string($conn, $_POST['beschrijving']);
        updateGroup($groep_id, $groepNaam, $beschrijving);
    }
    elseif (isset($_POST['add_user_to_group'])) {
        // Gebruiker aan groep toevoegen
        $user_id = intval($_POST['user_id']);
        $group_id = intval($_POST['group_id']);
        addUserToGroup($user_id, $group_id);
    }
}

// Verwerk GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['deleteGroup'])) {
        $groep_id = intval($_GET['deleteGroup']);
        deleteGroup($groep_id);
    }
    elseif (isset($_GET['remove_user'])) {
        $user_id = intval($_GET['remove_user']);
        $group_id = intval($_GET['group_id']);
        removeUserFromGroup($user_id, $group_id);
    }
}

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
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Groepen Beheren</title>
    <link rel="stylesheet" href="src/main2.css">
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
<br>
    <a href="Groepen.php" class="button-class">Terug naar groepen</a>
        <h1>Groepen Beheren</h1>
        <section>
            <h2>Groep Aanmaken</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="groep_naam">Groepsnaam:</label>
                    <input type="text" id="groep_naam" name="groep_naam" required>
                </div>
                <div class="form-group">
                    <label for="beschrijving">Beschrijving:</label>
                    <input type="text" id="beschrijving" name="beschrijving" rows="3"></input>
                </div>
                <button type="submit" name="submit">Groep Aanmaken</button>
            </form>
        </section>
<br>
        <section>
            <h2>Mijn Groepen</h2><!--Laat groepen zien dat jij zelf heb gemaakt-->
            <?php
            $groups = $conn->query("SELECT * FROM groepen WHERE created_by = $userId");
            if ($groups->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Groepsnaam</th>
                            <th>Beschrijving</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($group = $groups->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td><input type="text" name="groep_naam" value="<?= htmlspecialchars($group['groepnaam']) ?>" required></td>
                                <td><input type="text" name="beschrijving" value="<?= htmlspecialchars($group['beschrijving']) ?>"</td>
                                <td><!--Groepen hier en buttons om dingen aan te passen-->
                                    <input type="hidden" name="groep_id" value="<?= $group['groep_id'] ?>">
                                    <button type="submit" name="update_group" class="btn-primary">Bijwerken</button>
                                    <a href="?deleteGroup=<?= $group['groep_id'] ?>" class="btn btn-danger" 
                                       onclick="return confirm('Weet u zeker dat u deze groep wilt verwijderen?')">Verwijderen</a>
                                    <a href="?view_members=<?= $group['groep_id'] ?>#leden" class="btn btn-secondary">Leden</a>
                                    <a href="?view_group_messages=<?= $group['groep_id'] ?>#messages" class="btn btn-third">Berichten</a>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>U heeft nog geen groepen aangemaakt.</p>
            <?php endif; ?>
        </section>
            <!--Hier kan leden toevoegen aan een groep-->
        <div class="management-container">
            <div class="management-section">
                <h3>Gebruiker toevoegen</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="user_id">Gebruiker:</label>
                        <select name="user_id" id="user_id" required>
                            <option value="">Selecteer gebruiker</option>
                            <?php foreach (getAllUsers() as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="group_id">Groep:</label>
                        <select name="group_id" id="group_id" required>
                            <option value="">Selecteer groep</option>
                            <?php 
                            $allGroups = $conn->query("SELECT groep_id, groepnaam FROM groepen WHERE created_by = $userId");
                            while ($group = $allGroups->fetch_assoc()): ?>
                                <option value="<?= $group['groep_id'] ?>"><?= htmlspecialchars($group['groepnaam']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_user_to_group">Toevoegen</button>
                </form>
            </div>
                <!--Table met leden in een groep hier kan je leden verwijderen van de groep -->
            <div class="management-section" id="leden">
                <h3>Ledenbeheer</h3>
                <?php if (isset($_GET['view_members'])): 
                    $group_id = intval($_GET['view_members']);
                    $group = $conn->query("SELECT groepnaam FROM groepen WHERE groep_id = $group_id")->fetch_assoc();
                    $members = getGroupMembers($group_id);
                ?>
                    <h4><?= htmlspecialchars($group['groepnaam']) ?></h4>
                    <?php if (!empty($members)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Gebruiker</th>
                                    <th>Actie</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?= htmlspecialchars($member['username']) ?></td>
                                    <td>
                                        <a href="?remove_user=<?= $member['id'] ?>&group_id=<?= $group_id ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('Weet u zeker dat u deze gebruiker wilt verwijderen?')">
                                            Verwijderen
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Geen leden in deze groep.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Selecteer een groep om leden te bekijken.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="management-container">
            <div class="management-section" id="messages">
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
                
                <h5>Groep Berichten:</h5>
                <?php if (!empty($group_messages)): ?>
                    <ul>
                        <?php foreach ($group_messages as $message): ?>
                            <li><strong><?= htmlspecialchars($message['username']) ?>:</strong> <?= htmlspecialchars($message['message']) ?> <em>(<?= $message['created_at'] ?>)</em></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                            <p>Er zijn nog geen berichten in deze groep.</p>
                            <?php endif; ?>
                            <?php endif; ?>
            </div>
        </div>
    <script src="Dark.js"></script>
</body>
</html>