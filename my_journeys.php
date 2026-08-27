<?php
session_start();
require 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$current_member_id = $_SESSION['member_id'];

$active_query = "
    SELECT j.*, r.realm_name 
    FROM journeys j
    INNER JOIN realms r ON j.realm_id = r.realm_id
    WHERE j.member_id = ? AND j.status = 'active'
    ORDER BY j.last_played DESC
";

$stmt = $conn->prepare($active_query);
$stmt->bind_param("i", $current_member_id);
$stmt->execute();
$active_journeys = $stmt->get_result();

$completed_query = "
    SELECT j.character_name, j.status, j.last_played, r.realm_name 
    FROM journeys j
    INNER JOIN realms r ON j.realm_id = r.realm_id
    WHERE j.member_id = ? AND j.status != 'active'
    ORDER BY j.last_played DESC
";
$stmt2 = $conn->prepare($completed_query);
$stmt2->bind_param("i", $current_member_id);
$stmt2->execute();
$completed_journeys = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Journeys | Chronicle AI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Lora:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="my_journeys.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container mb-5">
        <section class="dashboard-header">
            <h1 class="dashboard-title font-display">Active Campaigns</h1>
            <p class="text-muted">Your current ongoing stories and character rosters.</p>
        </section>

        <div class="row g-4 mt-2">
            
            <?php if ($active_journeys->num_rows > 0): ?>
                <?php while ($journey = $active_journeys->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="journey-card">
                            <h4 class="font-display"><?php echo htmlspecialchars($journey['realm_name']); ?></h4>
                            
                            <div class="character-details">
                                <p class="mb-1">Name: <span><?php echo htmlspecialchars($journey['character_name']); ?></span></p>
                                <p class="mb-1">Class: <span><?php echo htmlspecialchars($journey['class_name']); ?> (Lvl <?php echo $journey['level']; ?>)</span></p>
                            </div>

                            <div class="progress-label">
                                <span>Story Progress</span>
                                <span><?php echo $journey['progress']; ?>%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: <?php echo $journey['progress']; ?>%;"></div>
                            </div>

                            <p class="text-center text-muted mb-3" style="font-size: 0.8rem; color: var(--parchment) !important;">Last Played: <?php echo date('M d, Y', strtotime($journey['last_played'])); ?></p>

                            <a href="story.php?id=<?php echo $journey['journey_id']; ?>" style="text-decoration: none;">
                                <button class="btn btn-resume">RESUME JOURNEY</button>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

            <div class="col-md-6 col-lg-4">
                <div class="journey-card" style="display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; border-style: dashed;">
                    <h4 class="font-display mb-3" style="border: none; color: var(--gold);">Begin Anew</h4>
                    <p class="mb-4" style="color: var(--parchment-light); font-size: 0.95rem;">Venture into an unexplored realm and weave a new destiny.</p>
                    <a href="create_journey.php" style="text-decoration: none; width: 100%;">
                        <button class="btn btn-resume" style="background-color: var(--leather-dark);">START NEW CAMPAIGN</button>
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="completed-panel">
                    <h4 class="font-display">The Hall of Heroes (Completed)</h4>
                    <ul class="graveyard-list">
                        <?php if ($completed_journeys->num_rows > 0): ?>
                            <?php while ($comp = $completed_journeys->fetch_assoc()): ?>
                                <li>
                                    <span><strong><?php echo htmlspecialchars($comp['character_name']); ?></strong> — <?php echo htmlspecialchars($comp['realm_name']); ?></span>
                                    <span class="text-muted"><?php echo ucfirst(htmlspecialchars($comp['status'])); ?> (<?php echo date('M d, Y', strtotime($comp['last_played'])); ?>)</span>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li>
                                <span class="text-muted font-italic" style="font-family: 'Lora', serif;">No fallen heroes... yet.</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-scroll-wrapper">
        <div class="footer-scroll-paper">
            <span>⛺ Rest area: Your progress is automatically transcribed to the ledger.</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>