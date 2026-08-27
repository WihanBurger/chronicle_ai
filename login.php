<?php
session_start();
require 'db.php';

$login_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT member_id, password_hash, role_id FROM members WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['member_id'] = $row['member_id'];
            $_SESSION['username'] = $username;
            $_SESSION['role_id'] = $row['role_id'];
            
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Incorrect passphrase.";
        }
    } else {
        $login_error = "Adventurer not found in the registry.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Chronicle AI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Lora:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <svg style="visibility: hidden; position: absolute;" width="0" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1">
        <defs>
            <filter id="torn-edge">
                <feTurbulence type="fractalNoise" baseFrequency="0.04" numOctaves="3" result="noise" />
                <feDisplacementMap in="SourceGraphic" in2="noise" scale="12" xChannelSelector="R" yChannelSelector="G" />
            </filter>
        </defs>
    </svg>

    <?php include 'navbar.php'; ?>

    <div class="login-container">
        <div class="scroll-wrapper <?php echo !empty($login_error) ? 'force-open' : ''; ?>" id="loginScroll" onclick="unrollScroll()">
            <div class="click-prompt" id="promptText">Click to Unroll</div>
            
            <div class="scroll-roller top-roller"></div>
            
            <div class="scroll-paper">
                <div class="scroll-content">
                    <h2 class="login-title font-display">Identify Yourself</h2>
                    
                    <?php if(!empty($login_error)): ?>
                        <div class="error-message"><?php echo $login_error; ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <input type="text" name="username" class="rpg-input" placeholder="Adventurer Name / Email" required>
                        <input type="password" name="password" class="rpg-input" placeholder="Secret Passphrase" required>
                        <button type="submit" class="btn btn-login font-display">ENTER THE REALM</button>
                    </form>
                    <p class="mt-4" style="font-size: 1rem; color: var(--leather);">
                        New to the guild? <a href="register.php" style="color: var(--gold); font-weight: bold;">Sign your pact here.</a>
                    </p>
                </div>
            </div>
            
            <div class="scroll-roller bottom-roller"></div>
        </div>
    </div>

    <script>
        function unrollScroll() {
            const scroll = document.getElementById('loginScroll');
            const prompt = document.getElementById('promptText');
            
            if (!scroll.classList.contains('open') && !scroll.classList.contains('force-open')) {
                scroll.classList.add('open');
                prompt.style.opacity = '0'; 
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>