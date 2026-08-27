-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2026 at 08:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chronicle_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `journeys`
--

CREATE TABLE `journeys` (
  `journey_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `realm_id` int(11) NOT NULL,
  `character_name` varchar(50) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `level` int(11) DEFAULT 1,
  `progress` int(11) DEFAULT 0,
  `status` enum('active','completed','dead') DEFAULT 'active',
  `last_played` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hp` int(11) DEFAULT 80,
  `max_hp` int(11) DEFAULT 100,
  `mp` int(11) DEFAULT 50,
  `max_mp` int(11) DEFAULT 50,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journeys`
--

INSERT INTO `journeys` (`journey_id`, `member_id`, `realm_id`, `character_name`, `class_name`, `level`, `progress`, `status`, `last_played`, `hp`, `max_hp`, `mp`, `max_mp`, `created_at`) VALUES
(1, 2, 1, 'Wihan', 'Paladin', 1, 40, 'active', '2026-08-26 09:47:02', 80, 100, 50, 50, '2026-08-27 03:08:47'),
(2, 2, 2, 'Wihan', 'Paladin', 1, 55, 'active', '2026-08-26 11:55:59', 80, 100, 50, 50, '2026-08-27 03:08:47'),
(3, 2, 1, 'Wihan', 'Rogue', 1, 100, 'active', '2026-08-27 07:44:17', 83, 100, 49, 50, '2026-08-27 03:08:47');

-- --------------------------------------------------------

--
-- Table structure for table `journey_choices`
--

CREATE TABLE `journey_choices` (
  `choice_id` int(11) NOT NULL,
  `journey_id` int(11) NOT NULL,
  `option_letter` char(1) NOT NULL,
  `choice_text` text NOT NULL,
  `icon` varchar(10) DEFAULT '⚔️',
  `votes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journey_choices`
--

INSERT INTO `journey_choices` (`choice_id`, `journey_id`, `option_letter`, `choice_text`, `icon`, `votes`) VALUES
(61, 3, 'A', 'Examine the surrounding architecture.', '🔍', 2),
(62, 3, 'B', 'Proceed cautiously down the corridor.', '🗡️', 1),
(63, 3, 'C', 'Wait and listen for approaching entities.', '👂', 0);

-- --------------------------------------------------------

--
-- Table structure for table `journey_inventory`
--

CREATE TABLE `journey_inventory` (
  `item_id` int(11) NOT NULL,
  `journey_id` int(11) NOT NULL,
  `item_name` varchar(50) NOT NULL,
  `icon` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journey_inventory`
--

INSERT INTO `journey_inventory` (`item_id`, `journey_id`, `item_name`, `icon`) VALUES
(1, 3, 'Health Potion', '🍷'),
(2, 3, 'Ancient Key', '🗝️'),
(3, 3, 'Rope', '➰'),
(4, 3, 'Empty Slot', '▫️');

-- --------------------------------------------------------

--
-- Table structure for table `journey_party`
--

CREATE TABLE `journey_party` (
  `party_id` int(11) NOT NULL,
  `journey_id` int(11) NOT NULL,
  `character_name` varchar(50) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `avatar` varchar(10) NOT NULL,
  `status` varchar(20) DEFAULT 'Waiting'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journey_party`
--

INSERT INTO `journey_party` (`party_id`, `journey_id`, `character_name`, `class_name`, `avatar`, `status`) VALUES
(1, 3, 'Elara', 'Mage', '🧙‍♀️', 'Waiting'),
(2, 3, 'Thorne', 'Warrior', '🤺', 'Waiting'),
(3, 3, 'Adventurer', 'Rogue', '🗡️', 'Waiting');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 1,
  `joined_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `username`, `email`, `password_hash`, `role_id`, `joined_at`) VALUES
(2, 'Wihan', 'wihanburger16@gmail.com', '$2y$10$XrpO/QoiwEv5GjCMo8ngseQa.T1Fuw4Tus4SDHTdUmy4tRP1oWftq', 1, '2026-08-20 13:09:22');

-- --------------------------------------------------------

--
-- Table structure for table `party_chats`
--

CREATE TABLE `party_chats` (
  `chat_id` int(11) NOT NULL,
  `journey_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `party_chats`
--

INSERT INTO `party_chats` (`chat_id`, `journey_id`, `username`, `message`, `created_at`) VALUES
(1, 3, 'Wihan', 'Heyy', '2026-08-27 05:29:46'),
(2, 3, 'Wihan', 'lol', '2026-08-27 05:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `realms`
--

CREATE TABLE `realms` (
  `realm_id` int(11) NOT NULL,
  `realm_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `status` enum('active','locked') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `realms`
--

INSERT INTO `realms` (`realm_id`, `realm_name`, `description`, `status`) VALUES
(1, 'The Hollow Spire', 'Rich etched corpo, greed, and rogue AIs.', 'active'),
(2, 'Neon Grit', 'Neon-lit rain, corpo greed, and rogue AIs.', 'active'),
(3, 'Void\'s Whisper', 'Eldritch mysteries, whispering deep space, and madness.', 'active'),
(4, 'Locked World', 'Sci-Fi Opera - 75% complete', 'locked');

-- --------------------------------------------------------

--
-- Table structure for table `realm_votes`
--

CREATE TABLE `realm_votes` (
  `vote_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `realm_name` varchar(100) NOT NULL,
  `voted_option` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `realm_votes`
--

INSERT INTO `realm_votes` (`vote_id`, `member_id`, `realm_name`, `voted_option`) VALUES
(1, 2, 'The Hollow Spire', 'A'),
(2, 2, 'Locked World', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'adventurer'),
(2, 'guildmaster');

-- --------------------------------------------------------

--
-- Table structure for table `story_logs`
--

CREATE TABLE `story_logs` (
  `log_id` int(11) NOT NULL,
  `journey_id` int(11) NOT NULL,
  `sender` enum('ai','player') NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `story_logs`
--

INSERT INTO `story_logs` (`log_id`, `journey_id`, `sender`, `message`, `created_at`) VALUES
(1, 1, 'player', 'Examine the area for hidden traps.', '2026-08-26 09:38:26'),
(2, 1, 'ai', 'The realm falls strangely silent... (The connection to the archive was lost).', '2026-08-26 09:38:27'),
(3, 1, 'player', 'Forcefully breach the pathway.', '2026-08-26 09:39:14'),
(4, 1, 'ai', 'The realm falls strangely silent... (The connection to the archive was lost).', '2026-08-26 09:39:14'),
(5, 1, 'player', 'Try to bypass the security lock quietly.', '2026-08-26 09:41:24'),
(6, 1, 'ai', 'The realm falls strangely silent... (The connection to the archive was lost).', '2026-08-26 09:41:25'),
(7, 1, 'player', 'Examine the area for hidden traps.', '2026-08-26 09:41:35'),
(8, 1, 'ai', 'The realm falls strangely silent... (The connection to the archive was lost).', '2026-08-26 09:41:35'),
(9, 1, 'player', 'Wee walk forward', '2026-08-26 09:41:46'),
(10, 1, 'ai', 'The realm falls strangely silent... (The connection to the archive was lost).', '2026-08-26 09:41:47'),
(11, 1, 'player', 'Examine the area for hidden traps.', '2026-08-26 09:44:40'),
(12, 1, 'ai', 'API Error: {\n  &quot;error&quot;: {\n    &quot;code&quot;: 404,\n    &quot;message&quot;: &quot;models/gemini-1.5-flash is not found for API version v1beta, or is not supported for generateContent. Call ModelService.ListModels to see the list of available models and their supported methods.&quot;,\n    &quot;status&quot;: &quot;NOT_FOUND&quot;\n  }\n}\n', '2026-08-26 09:44:40'),
(13, 1, 'player', 'Examine the area for hidden traps.', '2026-08-26 09:46:52'),
(14, 1, 'ai', 'Squinting through the gloom, you discern a wire of braided human hair, taut and shivering across the stone. It traces upward into the ceiling’s weeping shadows, where rusted, jagged iron spikes hang suspended, waiting for a single misstep. \n\nThe Spire holds its breath.', '2026-08-26 09:46:58'),
(15, 1, 'player', 'Examine the area for hidden traps.', '2026-08-26 09:46:58'),
(16, 1, 'ai', 'Your fingers trace the frost-slicked stone. In the gloom, you spot a razor-thin tripwire of calcified bone stretched across the archway, linked to a rusted mechanism dripping with black, acidic bile. The silence of the Spire is suffocating. \n\nHow do you proceed?', '2026-08-26 09:47:02'),
(17, 2, 'player', 'Examine the area for hidden traps.', '2026-08-26 09:47:24'),
(18, 2, 'ai', 'You crouch as neon light bleeds through the toxic smog, scanning the oil-slicked debris. There—hidden beneath rusted scrap, a copper tripwire hums with lethal voltage, wired to a buried charge. One misstep here means vaporized flesh. The trap is laid bare before you. \n\nWhat is your next move?', '2026-08-26 09:47:28'),
(19, 2, 'player', 'i disarm the trap', '2026-08-26 09:47:59'),
(20, 2, 'ai', 'Your grease-stained fingers bypass the tripmine\'s corroded circuitry. With a tense, metallic *snip*, the pulsing crimson laser sputters and dies, swallowed by the wet neon glare of the alley. The immediate threat is gone, Wihan, but the cold rain keeps falling. How do you proceed?', '2026-08-26 09:48:03'),
(21, 2, 'player', 'Try to bypass the security lock quietly.', '2026-08-26 11:54:32'),
(22, 2, 'ai', 'Your trembling fingers splice the damp wiring. The security panel flickers from crimson to a sickly green. With a soft, hydraulic sigh, the heavy blast door inches open, revealing a pitch-black corridor. A stench of wet rust and ozone washes over you. \n\nHow do you proceed?', '2026-08-26 11:54:44'),
(23, 2, 'player', 'Try to bypass the security lock quietly.', '2026-08-26 11:54:44'),
(24, 2, 'ai', 'You slide your deck into the port, fingers damp with greasy rain. The lock\'s neon red eye pulses, then stutters into a dull amber. A heavy pneumatic seal *hisses* open, releasing a breath of stale, metallic air. \n\nFrom the dark corridor ahead, a low, rhythmic clicking begins.', '2026-08-26 11:54:52'),
(25, 2, 'player', 'Examine the area for hidden traps.', '2026-08-26 11:54:52'),
(26, 2, 'ai', 'Raindrops sizzle against flickering violet neon, casting long, jagged shadows. As you scan the slick asphalt, a faint, crystalline glint catches your eye—a monofilament tripwire stretched ankle-high, humming with lethal voltage. The darkness ahead holds its breath. \n\nHow do you proceed?', '2026-08-26 11:55:02'),
(27, 2, 'player', 'Examine the area for hidden traps.', '2026-08-26 11:55:02'),
(28, 2, 'ai', 'You crouch in the oil-slicked grime. Beneath the buzzing, blood-red neon glare, you catch a faint shimmer: monofilament wire stretched taut across the rusted grating, wired to a block of dented military explosive. One wrong step, Wihan, and you are mist.', '2026-08-26 11:55:13'),
(29, 2, 'player', 'Try to bypass the security lock quietly.', '2026-08-26 11:55:13'),
(30, 2, 'ai', 'You slide your probe into the terminal. Acid rain drips from the neon sign above, sizzling on the casing. With a faint *click*, the lock\'s pulse slows, but the display bleeds a warning red. A hiss of pressurized steam sighs from the dark ahead.', '2026-08-26 11:55:25'),
(31, 2, 'player', 'Try to bypass the security lock quietly.', '2026-08-26 11:55:25'),
(32, 2, 'ai', 'API Error: {\n  &quot;error&quot;: {\n    &quot;code&quot;: 503,\n    &quot;message&quot;: &quot;This model is currently experiencing high demand. Spikes in demand are usually temporary. Please try again later.&quot;,\n    &quot;status&quot;: &quot;UNAVAILABLE&quot;\n  }\n}\n', '2026-08-26 11:55:26'),
(33, 2, 'player', 'Forcefully breach the pathway.', '2026-08-26 11:55:26'),
(34, 2, 'ai', 'With a brutal kick, you shatter the flickering barrier. Shards rain down like frozen neon tears. Smoke billows from the breached corridor, smelling of scorched copper and old blood. A low, synthetic alarm begins to wail in the dark. \n\nHow do you proceed, Wihan?', '2026-08-26 11:55:36'),
(35, 2, 'player', 'Forcefully breach the pathway.', '2026-08-26 11:55:36'),
(36, 2, 'ai', 'Metal shrieks as you shatter the barrier, unleashing a shower of dead-neon sparks and acrid smoke. The pathway lies ruptured before you, bleeding static into the grim, oil-slicked rain. A hollow dark yawns within the depths of Neon Grit. \n\nWhat do you do?', '2026-08-26 11:55:48'),
(37, 2, 'player', 'Forcefully breach the pathway.', '2026-08-26 11:55:48'),
(38, 2, 'ai', 'Your boot shatters the reinforced barrier, sending jagged acrylic flying. Static shrieks through your comms. Before you lies a suffocating, oil-slick corridor drowned in flickering violet neon. A heavy, metallic stench fills your lungs. \n\nThe path is open, Wihan. What do you do?', '2026-08-26 11:55:59'),
(39, 3, 'player', 'i use my rope to decend deeper', '2026-08-27 04:38:05'),
(40, 3, 'ai', 'You anchor your frayed rope to a weeping stone and slide into the freezing dark. The Hollow Spire swallows your light. Far below, whispers rise from the abyss, and your rope suddenly jerks violently. \n\nWhat do you do?', '2026-08-27 04:38:22'),
(41, 3, 'player', 'Call out to test if any entities are nearby.', '2026-08-27 04:44:34'),
(42, 3, 'ai', 'The realm shifts quietly in response to your choice, revealing a deeper passage.', '2026-08-27 04:44:34'),
(43, 3, 'player', 'Proceed cautiously down the corridor.', '2026-08-27 04:44:50'),
(44, 3, 'ai', 'The realm shifts quietly in response to your choice, revealing a deeper passage.', '2026-08-27 04:44:51'),
(45, 3, 'player', 'Proceed cautiously down the corridor.', '2026-08-27 04:45:03'),
(46, 3, 'ai', 'The realm shifts quietly in response to your choice, revealing a deeper passage.', '2026-08-27 04:45:04'),
(47, 3, 'player', 'Proceed cautiously down the corridor.', '2026-08-27 04:45:09'),
(48, 3, 'ai', 'The realm shifts quietly in response to your choice, revealing a deeper passage.', '2026-08-27 04:45:10'),
(49, 3, 'player', 'i proceed', '2026-08-27 04:45:15'),
(50, 3, 'ai', 'The realm shifts quietly in response to your choice, revealing a deeper passage.', '2026-08-27 04:45:15'),
(51, 3, 'player', 'Proceed cautiously down the corridor.', '2026-08-27 04:47:47'),
(52, 3, 'ai', 'API Error: API key not valid. Please pass a valid API key.', '2026-08-27 04:47:47'),
(53, 3, 'player', 'Proceed cautiously down the corridor.', '2026-08-27 04:48:39'),
(54, 3, 'ai', 'API Error: models/gemini-1.5-flash is not found for API version v1beta, or is not supported for generateContent. Call ModelService.ListModels to see the list of available models and their supported methods.', '2026-08-27 04:48:40'),
(55, 3, 'player', 'Proceed cautiously down the corridor.', '2026-08-27 04:49:56'),
(56, 3, 'ai', 'Wihan steps quietly, his boots pressing against cold, weeping stone. The silence of the Hollow Spire is broken by a low, wet breathing ahead. Shadows stretch abnormally. A flickering torchlight reveals a fork: a narrow drainage grate to his left, and a door bound in rusted iron straight ahead.', '2026-08-27 04:50:12'),
(57, 3, 'player', 'Examine the drainage grate for traps', '2026-08-27 04:50:28'),
(58, 3, 'ai', 'As Wihan leans close to the rusted grate, a faint click echoes. Cold grease coats the iron bars, concealing thin, taut tripwires connected to darts hidden in the dark sludge below. A hiss of escaping pressure warms the damp air.', '2026-08-27 04:50:44'),
(59, 3, 'player', 'Search for another route', '2026-08-27 04:54:33'),
(60, 3, 'ai', 'API Error: API key not valid. Please pass a valid API key.', '2026-08-27 04:54:33'),
(61, 3, 'player', 'Wait and listen for approaching entities.', '2026-08-27 04:55:10'),
(62, 3, 'ai', 'Silence clings to the cold stone of the Hollow Spire. Then, a wet, scraping sound echoes from the spiral stairs below, accompanied by a rhythmic, clicking breath. Something many-legged is climbing toward you, tasting the stagnant air for your scent.', '2026-08-27 04:55:24'),
(63, 3, 'player', 'Draw your blade and prepare an ambush', '2026-08-27 04:55:42'),
(64, 3, 'ai', 'Your cold steel slides silently from its sheath. You press into the dripping shadows of the spire as dragging footsteps echo near. A hunched, ash-skinned monstrosity rounds the corner, sniff-searching the air. It is mere inches away, entirely unaware of your poised, lethal edge.', '2026-08-27 04:55:51'),
(65, 3, 'player', 'Observe its weak points first', '2026-08-27 04:56:23'),
(66, 3, 'ai', 'Wihan narrows his eyes, studying the pulsating obsidian mass of the Spire. Its stone skin cracks, revealing a throbbing, violet heart leaking raw, agonizing energy. The structure groans, leaning perilously. A single, well-placed strike could bring it down—or trigger a catastrophic collapse.', '2026-08-27 04:56:38'),
(67, 3, 'player', 'Analyze the energy leak to find a safer exploit.', '2026-08-27 05:09:45'),
(68, 3, 'ai', 'Wihan peers into the jagged rift. The ethereal bleed of the Hollow Spire hums with decay, but you spot a rhythmic fluctuation. By channeling your focus, you isolate a stable frequency, a brief window where the raw, agonizing power can be siphoned without tearing your mind apart.', '2026-08-27 05:10:05'),
(69, 3, 'player', 'Siphon the stable frequency to empower yourself', '2026-08-27 07:17:25'),
(70, 3, 'ai', 'As you draw the humming, crystalline frequency into your chest, the Hollow Spire\'s ancient stone walls groan. Raw, cold energy floods your veins, but the tower\'s stability fractures. Black dust rains down as the foundations shift beneath your feet. Power always comes with a terrible cost.', '2026-08-27 07:17:49'),
(71, 3, 'player', 'Channel the excess energy to blast open the sealed chamber door', '2026-08-27 07:38:14'),
(72, 3, 'ai', 'Wihan unleashes the crackling energy. It surges from his fingertips, shattering the ancient obsidian seal with a deafening crack. The door splinters into razor-sharp shards, revealing a yawning chasm of darkness where cold whispers beckon. His veins burn with the agonizing magical feedback.', '2026-08-27 07:38:25'),
(73, 3, 'player', 'i use a healing potion', '2026-08-27 07:38:54'),
(74, 3, 'ai', 'API Error: You exceeded your current quota, please check your plan and billing details. For more information on this error, head to: https://ai.google.dev/gemini-api/docs/rate-limits. To monitor your current usage, head to: https://ai.dev/rate-limit. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 20, model: gemini-3.5-flash\nPlease retry in 6.625042383s.', '2026-08-27 07:38:54'),
(75, 3, 'player', 'Examine the surrounding architecture.', '2026-08-27 07:41:24'),
(76, 3, 'ai', 'API Error: You exceeded your current quota, please check your plan and billing details. For more information on this error, head to: https://ai.google.dev/gemini-api/docs/rate-limits. To monitor your current usage, head to: https://ai.dev/rate-limit. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 20, model: gemini-3.5-flash\nPlease retry in 35.032569275s.', '2026-08-27 07:41:26'),
(77, 3, 'player', 'Proceed cautiously down the corridor.', '2026-08-27 07:44:16'),
(78, 3, 'ai', 'API Error: You exceeded your current quota, please check your plan and billing details. For more information on this error, head to: https://ai.google.dev/gemini-api/docs/rate-limits. To monitor your current usage, head to: https://ai.dev/rate-limit. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 20, model: gemini-3.5-flash\nPlease retry in 44.16694107s.', '2026-08-27 07:44:16'),
(79, 3, 'player', 'Proceed cautiously down the corridor.', '2026-08-27 07:46:49'),
(80, 3, 'ai', 'API Error: You exceeded your current quota, please check your plan and billing details. For more information on this error, head to: https://ai.google.dev/gemini-api/docs/rate-limits. To monitor your current usage, head to: https://ai.dev/rate-limit. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 20, model: gemini-3.5-flash\nPlease retry in 11.261805495s.', '2026-08-27 07:46:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `journeys`
--
ALTER TABLE `journeys`
  ADD PRIMARY KEY (`journey_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `realm_id` (`realm_id`);

--
-- Indexes for table `journey_choices`
--
ALTER TABLE `journey_choices`
  ADD PRIMARY KEY (`choice_id`),
  ADD KEY `journey_id` (`journey_id`);

--
-- Indexes for table `journey_inventory`
--
ALTER TABLE `journey_inventory`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `journey_id` (`journey_id`);

--
-- Indexes for table `journey_party`
--
ALTER TABLE `journey_party`
  ADD PRIMARY KEY (`party_id`),
  ADD KEY `journey_id` (`journey_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `party_chats`
--
ALTER TABLE `party_chats`
  ADD PRIMARY KEY (`chat_id`);

--
-- Indexes for table `realms`
--
ALTER TABLE `realms`
  ADD PRIMARY KEY (`realm_id`);

--
-- Indexes for table `realm_votes`
--
ALTER TABLE `realm_votes`
  ADD PRIMARY KEY (`vote_id`),
  ADD UNIQUE KEY `unique_user_realm_vote` (`member_id`,`realm_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `story_logs`
--
ALTER TABLE `story_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `journey_id` (`journey_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `journeys`
--
ALTER TABLE `journeys`
  MODIFY `journey_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `journey_choices`
--
ALTER TABLE `journey_choices`
  MODIFY `choice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `journey_inventory`
--
ALTER TABLE `journey_inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `journey_party`
--
ALTER TABLE `journey_party`
  MODIFY `party_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `party_chats`
--
ALTER TABLE `party_chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `realms`
--
ALTER TABLE `realms`
  MODIFY `realm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `realm_votes`
--
ALTER TABLE `realm_votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `story_logs`
--
ALTER TABLE `story_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `journeys`
--
ALTER TABLE `journeys`
  ADD CONSTRAINT `journeys_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journeys_ibfk_2` FOREIGN KEY (`realm_id`) REFERENCES `realms` (`realm_id`);

--
-- Constraints for table `journey_choices`
--
ALTER TABLE `journey_choices`
  ADD CONSTRAINT `journey_choices_ibfk_1` FOREIGN KEY (`journey_id`) REFERENCES `journeys` (`journey_id`) ON DELETE CASCADE;

--
-- Constraints for table `journey_inventory`
--
ALTER TABLE `journey_inventory`
  ADD CONSTRAINT `journey_inventory_ibfk_1` FOREIGN KEY (`journey_id`) REFERENCES `journeys` (`journey_id`) ON DELETE CASCADE;

--
-- Constraints for table `journey_party`
--
ALTER TABLE `journey_party`
  ADD CONSTRAINT `journey_party_ibfk_1` FOREIGN KEY (`journey_id`) REFERENCES `journeys` (`journey_id`) ON DELETE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `story_logs`
--
ALTER TABLE `story_logs`
  ADD CONSTRAINT `story_logs_ibfk_1` FOREIGN KEY (`journey_id`) REFERENCES `journeys` (`journey_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
