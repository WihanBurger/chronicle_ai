<?php
session_start();
require 'db.php';

$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : 0;
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['realm_name']) && isset($_POST['option'])) {
    if (!$member_id) {
        header("Location: index.php");
        exit();
    }
    
    $realm_name = trim($_POST['realm_name']);
    $option = trim($_POST['option']);
    
    $check_vote = $conn->prepare("SELECT vote_id FROM realm_votes WHERE member_id = ? AND realm_name = ?");
    $check_vote->bind_param("is", $member_id, $realm_name);
    $check_vote->execute();
    $check_vote->store_result();
    
    if ($check_vote->num_rows === 0) {
        $ins_vote = $conn->prepare("INSERT INTO realm_votes (member_id, realm_name, voted_option) VALUES (?, ?, ?)");
        $ins_vote->bind_param("iss", $member_id, $realm_name, $option);
        $ins_vote->execute();
        $ins_vote->close();
        $message = "Your vote has been recorded in the guild archives!";
    } else {
        $message = "You have already cast your vote for this realm.";
    }
    $check_vote->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['join_journey_id'])) {
    if (!$member_id) {
        header("Location: index.php");
        exit();
    }
    
    $target_journey_id = (int)$_POST['join_journey_id'];
    
    $party_stmt = $conn->prepare("INSERT INTO journey_party (journey_id, character_name, class_name, avatar, status) VALUES (?, 'Adventurer', 'Rogue', '🗡️', 'Waiting')");
    $party_stmt->bind_param("i", $target_journey_id);
    if ($party_stmt->execute()) {
        $message = "Successfully enlisted in the party!";
    }
    $party_stmt->close();
}

$active_parties = [];
$parties_query = "
    SELECT j.journey_id, jp.character_name AS name, r.realm_name AS realm, m.username AS leader, j.progress 
    FROM journey_party jp
    INNER JOIN journeys j ON jp.journey_id = j.journey_id
    INNER JOIN realms r ON j.realm_id = r.realm_id
    INNER JOIN members m ON j.member_id = m.member_id
    LIMIT 20
";
$parties_result = $conn->query($parties_query);

if ($parties_result && $parties_result->num_rows > 0) {
    while ($row = $parties_result->fetch_assoc()) {
        $active_parties[] = [
            "journey_id" => $row['journey_id'],
            "name" => "Party of " . $row['name'],
            "realm" => $row['realm'],
            "leader" => $row['leader'],
            "objective" => "Campaign progress at " . $row['progress'] . "%"
        ];
    }
} else {
    $active_parties = [
        [
            "journey_id" => 0,
            "name" => "The Lone Wanderers",
            "realm" => "The Hollow Spire",
            "leader" => "System",
            "objective" => "Awaiting brave souls to form a guild"
        ]
    ];
}

$community_votes = [];
$realms_vote_query = "SELECT realm_name, description, status FROM realms";
$realms_vote_result = $conn->query($realms_vote_query);

if ($realms_vote_result && $realms_vote_result->num_rows > 0) {
    while ($realm_row = $realms_vote_result->fetch_assoc()) {
        $r_name = $realm_row['realm_name'];
        
        $count_a = 0;
        $count_b = 0;
        
        $vote_counts_query = $conn->prepare("SELECT voted_option, COUNT(*) as total FROM realm_votes WHERE realm_name = ? GROUP BY voted_option");
        $vote_counts_query->bind_param("s", $r_name);
        $vote_counts_query->execute();
        $vc_result = $vote_counts_query->get_result();
        while ($vc = $vc_result->fetch_assoc()) {
            if ($vc['voted_option'] === 'A') $count_a = $vc['total'];
            if ($vc['voted_option'] === 'B') $count_b = $vc['total'];
        }
        $vote_counts_query->close();
        
        $votes_a = 42 + $count_a;
        $votes_b = 38 + $count_b;
        $total_votes = $votes_a + $votes_b;
        $progress = ($realm_row['status'] === 'active') ? 100 : min(95, 40 + ($total_votes * 5));

        $community_votes[] = [
            "realm" => $r_name,
            "progress" => $progress,
            "proposal" => $realm_row['description'],
            "option_a" => "Expand Exploration Route",
            "votes_a" => $votes_a,
            "option_b" => "Secure Perimeter Defenses",
            "votes_b" => $votes_b
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Hub | Chronicle AI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Lora:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="community.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container my-2 flex-grow-1 d-flex flex-column overflow-hidden" style="max-height: calc(100vh - 85px);">
        <section class="community-header">
            <h1 class="community-title font-display">The Adventurers Guild Hall</h1>
            <p class="text-muted mb-1" style="font-size: 0.9rem;">Join active parties, participate in global realm progression votes, and forge alliances.</p>
        </section>

        <?php if (!empty($message)): ?>
            <div class="alert alert-dark text-center py-1 my-1" role="alert" style="background-color: var(--leather); color: var(--gold); border: 1px solid var(--gold); font-size: 0.9rem;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="row g-3 mt-0 flex-grow-1 overflow-hidden pb-2">
            <div class="col-lg-6 h-100">
                <div class="dnd-panel">
                    <h4 class="font-display">Open Parties & Guilds</h4>
                    <div class="scrollable-content">
                        <?php foreach ($active_parties as $party): ?>
                            <div class="party-card">
                                <h5 class="font-display mb-1" style="font-size: 1.1rem;"><?php echo htmlspecialchars($party['name']); ?></h5>
                                <p class="text-muted mb-1" style="font-size: 0.85rem;">
                                    Realm: <strong><?php echo htmlspecialchars($party['realm']); ?></strong> | Commander: <?php echo htmlspecialchars($party['leader']); ?>
                                </p>
                                <p class="mb-2" style="font-size: 0.9rem;">Objective: <em><?php echo htmlspecialchars($party['objective']); ?></em></p>
                                
                                <?php if ($party['journey_id'] > 0): ?>
                                    <form action="community.php" method="POST">
                                        <input type="hidden" name="join_journey_id" value="<?php echo $party['journey_id']; ?>">
                                        <button type="submit" class="btn btn-rpg btn-sm">JOIN PARTY</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>NO ACTIVE PARTIES</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 h-100">
                <div class="dnd-panel">
                    <h4 class="font-display">Active Realm Voting</h4>
                    <div class="scrollable-content">
                        <?php foreach ($community_votes as $vote): ?>
                            <div class="voting-card">
                                <h5 class="font-display mb-1" style="font-size: 1.1rem;"><?php echo htmlspecialchars($vote['realm']); ?></h5>
                                <div class="progress mt-1 mb-1" style="height: 8px; background-color: var(--leather);">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $vote['progress']; ?>%; background-color: var(--gold);" aria-valuenow="<?php echo $vote['progress']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted d-block mb-1" style="font-size: 0.8rem;">Progression: <?php echo $vote['progress']; ?>% complete towards opening this world.</small>
                                
                                <p class="mb-2" style="font-size: 0.9rem;"><strong>Current Poll:</strong> <?php echo htmlspecialchars($vote['proposal']); ?></p>
                                
                                <form action="community.php" method="POST" class="d-grid gap-1">
                                    <input type="hidden" name="realm_name" value="<?php echo htmlspecialchars($vote['realm']); ?>">
                                    
                                    <button type="submit" name="option" value="A" class="btn btn-outline-dark text-start py-1 px-2" style="font-size: 0.85rem;">
                                        🗳️ <?php echo htmlspecialchars($vote['option_a']); ?> (<strong><?php echo $vote['votes_a']; ?></strong> votes)
                                    </button>
                                    
                                    <button type="submit" name="option" value="B" class="btn btn-outline-dark text-start py-1 px-2" style="font-size: 0.85rem;">
                                        🗳️ <?php echo htmlspecialchars($vote['option_b']); ?> (<strong><?php echo $vote['votes_b']; ?></strong> votes)
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-scroll-wrapper">
        <div class="footer-scroll-paper">
            <span>🛡️ Guild Bulletin: Community votes directly shape the procedural generation of upcoming realms.</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>