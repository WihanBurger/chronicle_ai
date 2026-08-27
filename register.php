<?php
session_start();
require 'db.php';

$register_error = '';
$register_success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $register_error = "All fields are required to sign the pact.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO members (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);

        try {
            if ($stmt->execute()) {
                $register_success = "Pact signed successfully! You may now log in.";
            } else {
                $register_error = "Failed to register. Please try again.";
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $register_error = "That Adventurer Name or Email is already claimed.";
            } else {
                $register_error = "A mystical error occurred: " . $e->getMessage();
            }
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
    <title>Register | Chronicle AI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Lora:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="register.css">
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
        <div class="scroll-wrapper <?php echo (!empty($register_error) || !empty($register_success)) ? 'force-open' : ''; ?>" id="loginScroll" onclick="unrollScroll()">
            <div class="click-prompt" id="promptText">Click to Unroll</div>
            
            <div class="scroll-roller top-roller"></div>
            
            <div class="scroll-paper">
                <div class="scroll-content">
                    <h2 class="login-title font-display">Sign the Pact</h2>
                    
                    <?php if(!empty($register_error)): ?>
                        <div class="message-box error"><?php echo $register_error; ?></div>
                    <?php endif; ?>
                    
                    <?php if(!empty($register_success)): ?>
                        <div class="message-box success"><?php echo $register_success; ?></div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <input type="text" name="username" class="rpg-input" placeholder="Choose Adventurer Name" required>
                        <input type="email" name="email" class="rpg-input" placeholder="Email Address" required>
                        <input type="password" name="password" class="rpg-input" placeholder="Create Secret Passphrase" required>
                        <button type="submit" class="btn btn-login font-display">JOIN THE GUILD</button>
                    </form>
                    <p class="mt-3" style="font-size: 1rem; color: var(--leather);">
                        Already sworn in? <a href="login.php" style="color: var(--gold); font-weight: bold;">Return to Login.</a>
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