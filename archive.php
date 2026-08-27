<?php
session_start();
require 'db.php';

$archived_chronicles = [];

$query = "
    SELECT j.character_name AS title, r.realm_name AS realm, j.progress, m.username AS author, j.created_at 
    FROM journeys j
    INNER JOIN realms r ON j.realm_id = r.realm_id
    INNER JOIN members m ON j.member_id = m.member_id
    ORDER BY j.journey_id DESC
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $archived_chronicles[] = [
            "title" => "Journey of " . $row['title'],
            "realm" => $row['realm'],
            "date" => date("M Y", strtotime($row['created_at'])),
            "summary" => "An active campaign in " . $row['realm'] . " led by " . $row['author'] . ", currently at " . $row['progress'] . "% completion.",
            "author" => $row['author']
        ];
    }
} else {
    $archived_chronicles = [
        [
            "title" => "No Chronicles Found",
            "realm" => "The Void",
            "date" => "Present",
            "summary" => "No adventurer has yet recorded their journey in the global archive.",
            "author" => "System"
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chronicle Archive | Chronicle AI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Lora:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="archive.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container mb-5">
        <section class="archive-header">
            <h1 class="archive-title font-display">The Great Archive</h1>
            <p class="text-muted">Past completed journeys, preserved forever in the registry.</p>
        </section>

        <section class="row g-4 mt-3">
            <?php foreach ($archived_chronicles as $chronicle): ?>
                <div class="col-md-4">
                    <div class="archive-card">
                        <h4 class="font-display"><?php echo htmlspecialchars($chronicle['title']); ?></h4>
                        <div class="archive-meta">
                            Realm: <?php echo htmlspecialchars($chronicle['realm']); ?><br>
                            Chronicled by: <?php echo htmlspecialchars($chronicle['author']); ?> | <?php echo htmlspecialchars($chronicle['date']); ?>
                        </div>
                        <p><?php echo htmlspecialchars($chronicle['summary']); ?></p>
                        <button class="btn btn-read-chronicle">READ FULL CHRONICLE</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </div>

    <div class="footer-scroll-wrapper mb-4">
        <div class="footer-scroll-paper">
            <span>📜 Rule of the Realm: Nothing written in the archive can ever be unwritten or deleted.</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>