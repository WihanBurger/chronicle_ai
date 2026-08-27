<?php
session_start();
require 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$error_message = '';
$current_member_id = $_SESSION['member_id'];

$realms_query = "SELECT realm_id, realm_name, description FROM realms WHERE status = 'active'";
$realms_result = $conn->query($realms_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $character_name = trim($_POST['character_name']);
    $class_name = trim($_POST['class_name']);
    $realm_id = $_POST['realm_id'];

    if (empty($character_name) || empty($class_name) || empty($realm_id)) {
        $error_message = "All fields are required to begin a journey.";
    } else {
        $stmt = $conn->prepare("INSERT INTO journeys (member_id, realm_id, character_name, class_name, level, progress, status) VALUES (?, ?, ?, ?, 1, 0, 'active')");
        $stmt->bind_param("iiss", $current_member_id, $realm_id, $character_name, $class_name);
        
        if ($stmt->execute()) {
            header("Location: my_journeys.php");
            exit();
        } else {
            $error_message = "A magical disturbance prevented your journey from starting.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start New Campaign | Chronicle AI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Lora:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="create_journey.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="creation-container">
        <div class="character-sheet">
            <h2 class="sheet-title font-display">Forge a New Destiny</h2>
            
            <?php if(!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="create_journey.php" method="POST">
                <div class="form-group">
                    <label>Adventurer Name</label>
                    <input type="text" name="character_name" class="rpg-input" placeholder="e.g., Kaelen the Shadow" required>
                </div>

                <div class="form-group">
                    <label>Class / Vocation</label>
                    <input type="text" name="class_name" class="rpg-input" placeholder="e.g., Rogue, Cyber-Mage, Warrior" required>
                </div>

                <div class="form-group">
                    <label>Select Realm</label>
                    <select name="realm_id" class="rpg-select" id="realmSelect" required onchange="showRealmDesc()">
                        <option value="" disabled selected>-- Choose your destination --</option>
                        
                        <?php 
                        if ($realms_result->num_rows > 0) {
                            while($realm = $realms_result->fetch_assoc()) {
                                echo "<option value='" . $realm['realm_id'] . "' data-desc='" . htmlspecialchars($realm['description']) . "'>" . htmlspecialchars($realm['realm_name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                    <div id="realmDescription" class="realm-desc"></div>
                </div>

                <button type="submit" class="btn btn-forge font-display">BEGIN CAMPAIGN</button>
            </form>
        </div>
    </div>

    <script>
        function showRealmDesc() {
            var select = document.getElementById("realmSelect");
            var descBox = document.getElementById("realmDescription");
            var selectedOption = select.options[select.selectedIndex];
            var description = selectedOption.getAttribute("data-desc");
            
            if (description) {
                descBox.innerHTML = description;
                descBox.style.display = "block";
            } else {
                descBox.style.display = "none";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>