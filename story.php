<?php
session_start();
require 'db.php';

if (!isset($_SESSION['member_id']) || !isset($_GET['id'])) {
    header("Location: my_journeys.php");
    exit();
}

$journey_id = (int)$_GET['id'];
$member_id = $_SESSION['member_id'];

$stmt = $conn->prepare("
    SELECT j.*, r.realm_name, r.description as realm_desc 
    FROM journeys j
    INNER JOIN realms r ON j.realm_id = r.realm_id
    WHERE j.journey_id = ? AND j.member_id = ?
");
$stmt->bind_param("ii", $journey_id, $member_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: my_journeys.php");
    exit();
}
$journey = $result->fetch_assoc();
$stmt->close();

$conn->query("CREATE TABLE IF NOT EXISTS party_chats (
    chat_id INT AUTO_INCREMENT PRIMARY KEY,
    journey_id INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['chat_message'])) {
    $chat_text = trim($_POST['chat_message']);
    if (!empty($chat_text)) {
        $chat_stmt = $conn->prepare("INSERT INTO party_chats (journey_id, username, message) VALUES (?, ?, ?)");
        $username = $_SESSION['username'] ?? $journey['character_name'];
        $chat_stmt->bind_param("iss", $journey_id, $username, $chat_text);
        $chat_stmt->execute();
        $chat_stmt->close();
        
        header("Location: story.php?id=" . $journey_id);
        exit();
    }
}

$bg_map = [
    'castle'      => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1920&q=80',
    'forest'      => 'https://images.unsplash.com/photo-1509114397022-ed747cca3f65?auto=format&fit=crop&w=1920&q=80',
    'dungeon'     => 'https://images.unsplash.com/photo-1514539079130-25950c84af65?auto=format&fit=crop&w=1920&q=80',
    'tavern'      => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=1920&q=80',
    'throne'      => 'https://images.unsplash.com/photo-1533158307587-828f0a76ef46?auto=format&fit=crop&w=1920&q=80',
    'library'     => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=1920&q=80',
    'swamp'       => 'https://images.unsplash.com/photo-1511497584788-876761142212?auto=format&fit=crop&w=1920&q=80',
    'battlefield' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=1920&q=80',
    'space'       => 'https://images.unsplash.com/photo-1506703719100-a0f3a48c0f86?auto=format&fit=crop&w=1920&q=80',
    'default'     => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1920&q=80'
];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['custom_action'])) {
    $action = trim($_POST['custom_action']);
    
    if (!empty($action)) {
        $log_stmt = $conn->prepare("INSERT INTO story_logs (journey_id, sender, message) VALUES (?, 'player', ?)");
        $log_stmt->bind_param("is", $journey_id, $action);
        $log_stmt->execute();
        $log_stmt->close();

        $api_key = "AQ.Ab8RN6K71_bXT7PwOYuVWa8T7VOvn1husfkhMx-10zo2JEZ7hg"; 
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $api_key;
        
        $system_prompt = "You are a Game Master for a grim tabletop RPG. The player is " . $journey['character_name'] . ", currently in the realm of " . $journey['realm_name'] . ". Action: '" . $action . "'. Describe what happens next (max 50 words). Choose one matching mood keyword for the scene from this exact list: 'castle', 'forest', 'dungeon', 'tavern', 'throne', 'library', 'swamp', 'battlefield', 'space'. You MUST respond with ONLY a valid JSON object in this exact format: {\"story\": \"your story text\", \"choices\": [{\"label\": \"Choice 1 description\", \"icon\": \"🗡️\"}, {\"label\": \"Choice 2 description\", \"icon\": \"🔍\"}, {\"label\": \"Choice 3 description\", \"icon\": \"🏃\"}], \"mood\": \"tavern\"}";
        
        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $system_prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "responseMimeType" => "application/json"
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $curl_err = curl_error($ch);
        curl_close($ch);
        
        $result_data = json_decode($response, true);
        
        $new_choices = [
            ["label" => "Examine the surrounding architecture.", "icon" => "🔍"],
            ["label" => "Proceed cautiously down the corridor.", "icon" => "🗡️"],
            ["label" => "Wait and listen for approaching entities.", "icon" => "👂"]
        ];

        $detected_mood = 'default';

        if ($curl_err) {
            $ai_response = "cURL Error: " . $curl_err;
        } elseif (isset($result_data['error'])) {
            $ai_response = "API Error: " . $result_data['error']['message'];
        } elseif (isset($result_data['candidates'][0]['content']['parts'][0]['text'])) {
            $ai_raw_text = trim($result_data['candidates'][0]['content']['parts'][0]['text']);
            $ai_data = json_decode($ai_raw_text, true);
            
            if (is_array($ai_data) && isset($ai_data['story']) && isset($ai_data['choices'])) {
                $ai_response = $ai_data['story'];
                $new_choices = $ai_data['choices'];
                if (isset($ai_data['mood']) && array_key_exists($ai_data['mood'], $bg_map)) {
                    $detected_mood = $ai_data['mood'];
                }
            } else {
                $ai_response = "JSON Parsing Failed: " . htmlspecialchars($ai_raw_text);
            }
        } else {
            $ai_response = "Unknown API Error occurred.";
        }

        $_SESSION['journey_mood_' . $journey_id] = $detected_mood;
        
        $ai_stmt = $conn->prepare("INSERT INTO story_logs (journey_id, sender, message) VALUES (?, 'ai', ?)");
        $ai_stmt->bind_param("is", $journey_id, $ai_response);
        $ai_stmt->execute();
        $ai_stmt->close();

        $new_progress = min(100, $journey['progress'] + 5);
        $prog_stmt = $conn->prepare("UPDATE journeys SET progress = ? WHERE journey_id = ?");
        $prog_stmt->bind_param("ii", $new_progress, $journey_id);
        $prog_stmt->execute();
        $prog_stmt->close();

        $conn->query("DELETE FROM journey_choices WHERE journey_id = $journey_id");
        $ins_choice = $conn->prepare("INSERT INTO journey_choices (journey_id, option_letter, choice_text, icon, votes) VALUES (?, ?, ?, ?, ?)");
        $letters = ['A', 'B', 'C'];
        
        for ($i = 0; $i < 3; $i++) {
            $letter = $letters[$i];
            $text = isset($new_choices[$i]['label']) ? substr($new_choices[$i]['label'], 0, 100) : "Proceed forward.";
            $icon = isset($new_choices[$i]['icon']) ? $new_choices[$i]['icon'] : "❓";
            $votes = rand(0, 2); 
            $ins_choice->bind_param("isssi", $journey_id, $letter, $text, $icon, $votes);
            $ins_choice->execute();
        }
        $ins_choice->close();

        header("Location: story.php?id=" . $journey_id);
        exit();
    }
}

$logs_stmt = $conn->prepare("SELECT sender, message, created_at FROM story_logs WHERE journey_id = ? ORDER BY created_at ASC");
$logs_stmt->bind_param("i", $journey_id);
$logs_stmt->execute();
$logs_result = $logs_stmt->get_result();

$latest_story = "";
$chronicle_log = [];
$entry_num = 1;
while ($row = $logs_result->fetch_assoc()) {
    if ($row['sender'] === 'ai') $latest_story = $row['message'];
    $sender_name = ($row['sender'] === 'ai') ? "Narrator" : $journey['character_name'];
    $chronicle_log[] = ["num" => $entry_num, "text" => "[" . $sender_name . "] " . $row['message']];
    $entry_num++;
}
$logs_stmt->close();

if (empty($latest_story)) {
    $latest_story = "You descend into the borders of " . $journey['realm_name'] . ". " . $journey['realm_desc'];
}

$active_mood_key = $_SESSION['journey_mood_' . $journey_id] ?? 'default';
$active_bg = $bg_map[$active_mood_key] ?? $bg_map['default'];

$choices_check = $conn->prepare("SELECT * FROM journey_choices WHERE journey_id = ?");
$choices_check->bind_param("i", $journey_id);
$choices_check->execute();
$choices_result = $choices_check->get_result();
$choices = [];

if ($choices_result->num_rows === 0) {
    $default_choices = [
        ['A', 'Examine the surrounding architecture for hidden mechanisms.', '🔍', 2],
        ['B', 'Proceed cautiously down the shadowed corridor with weapon drawn.', '🗡️', 1],
        ['C', 'Call out to test if any entities are nearby.', '🗣️', 0]
    ];
    $ins_choice = $conn->prepare("INSERT INTO journey_choices (journey_id, option_letter, choice_text, icon, votes) VALUES (?, ?, ?, ?, ?)");
    foreach ($default_choices as $dc) {
        $ins_choice->bind_param("isssi", $journey_id, $dc[0], $dc[1], $dc[2], $dc[3]);
        $ins_choice->execute();
    }
    $ins_choice->close();
    $choices_check->execute();
    $choices_result = $choices_check->get_result();
}

while ($row = $choices_result->fetch_assoc()) {
    $choices[] = $row;
}
$choices_check->close();

$party = [["name" => $journey['character_name'], "class" => $journey['class_name'], "avatar" => "🥷", "status" => "Turn"]];
$party_stmt = $conn->prepare("SELECT character_name, class_name, avatar, status FROM journey_party WHERE journey_id = ?");
$party_stmt->bind_param("i", $journey_id);
$party_stmt->execute();
$party_result = $party_stmt->get_result();
if ($party_result->num_rows === 0) {
    $conn->query("INSERT INTO journey_party (journey_id, character_name, class_name, avatar, status) VALUES ($journey_id, 'Elara', 'Mage', '🧙‍♀️', 'Waiting'), ($journey_id, 'Thorne', 'Warrior', '🤺', 'Waiting')");
    $party[] = ["name" => "Elara", "class" => "Mage", "avatar" => "🧙‍♀️", "status" => "Waiting"];
    $party[] = ["name" => "Thorne", "class" => "Warrior", "avatar" => "🤺", "status" => "Waiting"];
} else {
    while ($p_row = $party_result->fetch_assoc()) {
        $party[] = ["name" => $p_row['character_name'], "class" => $p_row['class_name'], "avatar" => $p_row['avatar'], "status" => $p_row['status']];
    }
}
$party_stmt->close();

$inventory = [];
$inv_stmt = $conn->prepare("SELECT item_name, icon FROM journey_inventory WHERE journey_id = ?");
$inv_stmt->bind_param("i", $journey_id);
$inv_stmt->execute();
$inv_result = $inv_stmt->get_result();
if ($inv_result->num_rows === 0) {
    $conn->query("INSERT INTO journey_inventory (journey_id, item_name, icon) VALUES ($journey_id, 'Health Potion', '🍷'), ($journey_id, 'Ancient Key', '🗝️'), ($journey_id, 'Rope', '➰'), ($journey_id, 'Empty Slot', '▫️')");
    $inventory = [
        ["item" => "Health Potion", "icon" => "🍷"],
        ["item" => "Ancient Key", "icon" => "🗝️"],
        ["item" => "Rope", "icon" => "➰"],
        ["item" => "Empty Slot", "icon" => "▫️"]
    ];
} else {
    while ($i_row = $inv_result->fetch_assoc()) {
        $inventory[] = ["item" => $i_row['item_name'], "icon" => $i_row['icon']];
    }
}
$inv_stmt->close();

$chat_messages = [];
$chat_query = $conn->prepare("SELECT username, message FROM party_chats WHERE journey_id = ? ORDER BY created_at ASC");
$chat_query->bind_param("i", $journey_id);
$chat_query->execute();
$chat_res = $chat_query->get_result();
if ($chat_res->num_rows > 0) {
    while ($c_row = $chat_res->fetch_assoc()) {
        $chat_messages[] = ["user" => $c_row['username'], "msg" => $c_row['message']];
    }
} else {
    $chat_messages = [
        ["user" => "System", "msg" => "Welcome to the party chat. Coordinate your actions here!"]
    ];
}
$chat_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Story | Chronicle AI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Lora:wght@400;500;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        parchment: { DEFAULT: '#EADCB6', light: '#F4EECA' },
                        leather: { DEFAULT: '#3E2723', dark: '#1A0F0D' },
                        ink: '#2B2824',
                        gold: '#C49A52',
                        cyan: '#48B8D0',
                        crimson: '#D96A6A'
                    },
                    fontFamily: {
                        lora: ['Lora', 'serif'],
                        cormorant: ['"Cormorant Garamond"', 'serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="story.css?v=<?php echo time(); ?>">
</head>
<body style="background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), url('<?php echo $active_bg; ?>'); background-size: cover; background-position: center; background-attachment: fixed;" class="text-ink font-lora min-h-screen selection:bg-gold selection:text-leather-dark">

    <svg style="visibility: hidden; position: absolute;" width="0" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1">
        <defs>
            <filter id="torn-edge">
                <feTurbulence type="fractalNoise" baseFrequency="0.04" numOctaves="3" result="noise" />
                <feDisplacementMap in="SourceGraphic" in2="noise" scale="12" xChannelSelector="R" yChannelSelector="G" />
            </filter>
        </defs>
    </svg>

    <?php include 'navbar.php'; ?>

    <div class="text-center py-6 px-4">
        <h2 class="font-cormorant font-bold text-4xl uppercase tracking-widest text-parchment mb-4 drop-shadow-md"><?php echo htmlspecialchars($journey['realm_name']); ?></h2>
        <div class="flex justify-center gap-4">
            <?php foreach ($party as $member): ?>
                <div class="w-12 h-12 rounded-full bg-leather border-2 <?php echo $member['status'] === 'Turn' ? 'border-cyan shadow-[0_0_15px_rgba(72,184,208,0.6)] scale-110' : 'border-gold'; ?> flex items-center justify-center text-2xl shadow-lg transition-all duration-300 cursor-help" title="<?php echo htmlspecialchars($member['name']); ?>">
                    <?php echo $member['avatar']; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="w-full px-4 md:px-8 2xl:px-16 pb-12 mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-leather text-parchment p-6 rounded-lg shadow-[inset_0_0_20px_rgba(0,0,0,0.8),_0_6px_12px_rgba(0,0,0,0.3)] border-4 border-leather-dark relative before:absolute before:inset-1.5 before:border before:border-gold/30 before:pointer-events-none before:rounded-sm">
                    <h5 class="font-cormorant font-bold text-xl uppercase tracking-widest text-gold border-b-2 border-gold/50 pb-2 text-center mb-6">Character Journal</h5>
                    
                    <h5 class="font-cormorant font-bold text-lg uppercase tracking-widest text-gold text-center mb-4 mt-2">Inventory</h5>
                    <div class="grid grid-cols-2 gap-3 relative z-10">
                        <?php foreach ($inventory as $inv): ?>
                            <div class="bg-black/40 border border-gold/40 hover:border-gold hover:bg-black/60 transition duration-300 rounded p-3 text-center cursor-pointer group">
                                <div class="text-3xl mb-1 group-hover:scale-110 transition-transform"><?php echo $inv['icon']; ?></div>
                                <div class="text-xs font-medium text-parchment-light leading-tight"><?php echo htmlspecialchars($inv['item']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 flex flex-col gap-6">
                
                <div class="story-scroll-wrapper relative z-10">
                    <div class="scroll-roller"></div>
                    <div class="story-scroll-paper relative px-10 md:px-16 py-12 z-20 min-h-[350px] max-h-[450px] flex items-center justify-center overflow-y-auto custom-scroll" id="storyScrollArea">
                        <div class="scroll-ornament before:top-4 before:left-6 after:bottom-4 after:right-6 after:rotate-180"></div>
                        <div class="scroll-ornament before:top-4 before:right-6 before:-scale-x-100 after:bottom-4 after:left-6 after:-scale-y-100"></div>
                        
                        <div class="relative z-10 w-full text-center">
                            <p class="text-[1.45rem] leading-relaxed text-ink font-medium drop-shadow-[0_1px_1px_rgba(255,255,255,0.5)]">
                                <?php echo nl2br(htmlspecialchars($latest_story)); ?>
                            </p>
                        </div>
                    </div>
                    <div class="scroll-roller"></div>
                </div>

                <div class="bg-leather border-2 border-leather-dark rounded-xl p-6 shadow-2xl relative before:absolute before:inset-2 before:border before:border-gold/20 before:pointer-events-none before:rounded-lg">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5 relative z-10">
                        <?php foreach ($choices as $choice): ?>
                            <form action="story.php?id=<?php echo $journey_id; ?>" method="POST" class="h-full">
                                <input type="hidden" name="custom_action" value="<?php echo htmlspecialchars($choice['choice_text']); ?>">
                                <button type="submit" class="w-full h-full bg-parchment-light text-ink border-2 border-gold/70 rounded-lg p-4 text-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_10px_20px_rgba(0,0,0,0.4)] hover:border-white hover:bg-white group">
                                    <div class="text-4xl mb-2 group-hover:scale-110 transition-transform"><?php echo $choice['icon']; ?></div>
                                    <strong class="font-cormorant uppercase tracking-wider text-lg block mb-1 text-leather">Option <?php echo $choice['option_letter']; ?></strong>
                                    <span class="text-sm leading-snug block mb-3 line-clamp-3"><?php echo htmlspecialchars($choice['choice_text']); ?></span>
                                    <span class="inline-block bg-leather text-gold px-2 py-0.5 rounded text-xs font-bold border border-gold/50 shadow-inner group-hover:bg-leather-dark">Votes: <?php echo $choice['votes']; ?>/3</span>
                                </button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                    
                    <form action="story.php?id=<?php echo $journey_id; ?>" method="POST" class="relative z-10">
                        <div class="flex gap-2">
                            <input type="text" name="custom_action" class="w-full bg-parchment-light border-2 border-gold/70 focus:border-white focus:ring-2 focus:ring-white/50 rounded-lg py-3 px-5 font-lora text-ink text-base placeholder-leather/50 transition-all outline-none shadow-inner" placeholder="Type your custom action... (or press ENTER to roll)" required autocomplete="off">
                            <button type="submit" class="bg-gold text-leather font-cormorant font-bold uppercase tracking-wider px-6 py-3 rounded-lg border-2 border-white hover:bg-parchment transition duration-300 shadow-md">
                                Perform
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-3 space-y-6">
                <div class="bg-leather text-parchment p-6 rounded-lg shadow-[inset_0_0_20px_rgba(0,0,0,0.8),_0_6px_12px_rgba(0,0,0,0.3)] border-4 border-leather-dark relative before:absolute before:inset-1.5 before:border before:border-gold/30 before:pointer-events-none before:rounded-sm">
                    <h5 class="font-cormorant font-bold text-xl uppercase tracking-widest text-gold border-b-2 border-gold/50 pb-2 text-center mb-4">Session Chronicle</h5>
                    <ul class="h-[280px] overflow-y-auto custom-scroll pr-2 space-y-3 relative z-10" id="chronicleLogArea">
                        <?php foreach ($chronicle_log as $log): ?>
                            <li class="text-sm border-b border-gold/20 pb-2 last:border-0 leading-relaxed text-parchment-light">
                                <strong class="text-gold block mb-0.5 tracking-wide text-xs uppercase">Entry <?php echo $log['num']; ?>:</strong> 
                                <?php echo htmlspecialchars($log['text']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="bg-leather text-parchment p-6 rounded-lg shadow-[inset_0_0_20px_rgba(0,0,0,0.8),_0_6px_12px_rgba(0,0,0,0.3)] border-4 border-leather-dark relative before:absolute before:inset-1.5 before:border before:border-gold/30 before:pointer-events-none before:rounded-sm">
                    <h5 class="font-cormorant font-bold text-xl uppercase tracking-widest text-gold border-b-2 border-gold/50 pb-2 text-center mb-4">Party Chat</h5>
                    <div class="bg-black/30 border border-gold/40 rounded p-4 h-40 overflow-y-auto custom-scroll mb-4 relative z-10 text-sm space-y-3" id="partyChatBox">
                        <?php foreach ($chat_messages as $msg): ?>
                            <p class="leading-relaxed">
                                <strong class="text-gold tracking-wide"><?php echo htmlspecialchars($msg['user']); ?>:</strong> 
                                <span class="text-parchment-light"><?php echo htmlspecialchars($msg['msg']); ?></span>
                            </p>
                        <?php endforeach; ?>
                    </div>
                    
                    <form action="story.php?id=<?php echo $journey_id; ?>" method="POST" class="relative z-10">
                        <div class="flex gap-2">
                            <input type="text" name="chat_message" class="w-full bg-parchment-light border-2 border-gold/50 focus:border-white rounded p-2.5 font-lora text-ink text-sm outline-none placeholder-leather/60 transition shadow-inner" placeholder="Whisper to party..." required autocomplete="off">
                            <button type="submit" class="bg-gold text-leather font-cormorant font-bold px-3 py-2 rounded border border-white hover:bg-parchment transition text-sm">Send</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        const logArea = document.getElementById('chronicleLogArea');
        if(logArea) logArea.scrollTop = logArea.scrollHeight;

        const chatBox = document.getElementById('partyChatBox');
        if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>