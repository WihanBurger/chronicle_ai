<?php
session_start();
require 'db.php';

$realms_query = "SELECT realm_id, realm_name, description, status FROM realms ORDER BY status ASC";
$realms_result = $conn->query($realms_query);

$features = [
    [
        "title" => "Dynamic Storytelling",
        "desc" => "Our AI adapts, weaving your actions into an unforgettable narrative tapestry.",
        "color_var" => "var(--blue)"
    ],
    [
        "title" => "Explore Vast Realms",
        "desc" => "Uncover deep secrets across ever-expanding, procedurally-generated fantasy worlds.",
        "color_var" => "var(--gold)"
    ],
    [
        "title" => "Join a Party",
        "desc" => "Form guilds with friends, collaborate on crucial choices, and master cooperative quests.",
        "color_var" => "var(--slate)"
    ]
];

$ticker_events = [
    ["icon" => "🌲", "color" => "#4a7c49", "text" => "A Rogue from the Shadowlands joined..."],
    ["icon" => "♟️", "color" => "#3b7bbf", "text" => "A Mage from the Spire voted for... <span class='log-icon' style='background-color: #8c5b4f; margin-left: 10px;'>❌</span> The party decided to ..."],
    ["icon" => "🎒", "color" => "#b58d68", "text" => "A party discovered the Lost City of ... The party chose to explore ..."],
    ["icon" => "✔️", "color" => "#7b8c62", "text" => "User chosen path: A Barbarian from the Wildlands joined."]
];

function getRealmImage($realm_name) {
    $name = strtolower($realm_name);
    if (strpos($name, 'hollow') !== false || strpos($name, 'spire') !== false) {
        return 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80';
    } elseif (strpos($name, 'neon') !== false || strpos($name, 'grit') !== false) {
        return 'https://images.unsplash.com/photo-1519501025264-65ba15a82390?auto=format&fit=crop&w=800&q=80';
    } elseif (strpos($name, 'void') !== false || strpos($name, 'whisper') !== false) {
        return 'https://images.unsplash.com/photo-1506703719100-a0f3a48c0f86?auto=format&fit=crop&w=800&q=80';
    } else {
        return 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=800&q=80';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chronicle AI | Discover Your Destiny</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <style>
        .realm-banner {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 1rem;
            border: 2px solid var(--leather);
            box-shadow: inset 0 0 10px rgba(0,0,0,0.4);
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <section class="hero-section">
            <?php if(isset($_SESSION['member_id'])): ?>
                <h1 class="hero-title">Welcome Back,<br><?php echo htmlspecialchars($_SESSION['username']); ?>.</h1>
                <p class="hero-subtitle">Your legacy awaits. Continue your unscripted adventures.</p>
                <a href="my_journeys.php" style="text-decoration: none;">
                    <button class="btn btn-primary-rpg">VIEW YOUR JOURNEYS</button>
                </a>
            <?php else: ?>
                <h1 class="hero-title">Discover Your Destiny.<br>Every Journey Is Unique.</h1>
                <p class="hero-subtitle">Experience Unscripted Narrative Adventures.<br>AI-Guided Storytelling for Every Adventurer.</p>
                <a href="register.php" style="text-decoration: none;">
                    <button class="btn btn-primary-rpg">START NEW JOURNEY</button>
                </a>
            <?php endif; ?>
        </section>

        <section class="row g-4 mb-5">
            <?php foreach ($features as $feature): ?>
                <div class="col-md-4">
                    <div class="feature-card" style="background-color: <?php echo $feature['color_var']; ?>;">
                        <h4 class="font-display"><?php echo htmlspecialchars($feature['title']); ?></h4>
                        <p><?php echo htmlspecialchars($feature['desc']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <h2 class="realm-section-title font-display">Select Your Realm</h2>
        <section class="row g-4 mb-5">
            <?php if ($realms_result && $realms_result->num_rows > 0): ?>
                <?php while ($realm = $realms_result->fetch_assoc()): 
                    $current_image = getRealmImage($realm['realm_name']);
                ?>
                    <div class="col-12 col-md-6">
                        <div class="realm-card <?php echo $realm['status'] === 'locked' ? 'locked' : ''; ?>">
                            <img src="<?php echo htmlspecialchars($current_image); ?>" alt="Realm Banner" class="realm-banner">
                            <h4 class="font-display"><?php echo htmlspecialchars($realm['realm_name']); ?></h4>
                            <p><?php echo htmlspecialchars($realm['description']); ?></p>
                            
                            <?php if($realm['status'] === 'locked'): ?>
                                <div class="progress mt-3 mb-2" style="height: 10px; background-color: var(--leather);">
                                    <div class="progress-bar" role="progressbar" style="width: 75%; background-color: var(--gold);" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            <?php endif; ?>

                            <?php 
                                $btn_text = ($realm['status'] === 'locked') ? "VOTING IN PROGRESS" : "START STORY";
                                $btn_link = ($realm['status'] === 'locked') ? "community.php" : "create_journey.php"; 
                            ?>

                            <a href="<?php echo $btn_link; ?>" style="display: block; text-decoration: none;">
                                <button class="realm-btn <?php echo $realm['status'] === 'locked' ? 'locked-btn disabled' : ''; ?>">
                                    <?php echo $btn_text; ?>
                                </button>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center w-100">The realms are currently hidden from view...</p>
            <?php endif; ?>
        </section>

        <div class="footer-scroll-wrapper">
            <div class="footer-scroll-paper">
                <ul class="ledger-log">
                    <?php foreach ($ticker_events as $event): ?>
                        <li class="ledger-item">
                            <span class="log-icon" style="background-color: <?php echo $event['color']; ?>;">
                                <?php echo $event['icon']; ?>
                            </span>
                            <span><?php echo $event['text']; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>