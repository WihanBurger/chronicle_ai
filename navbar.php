<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .rpg-navbar-container {
        background-color: #3E2723;
        border-bottom: 3px solid #C49A52;
        padding: 1rem 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 50;
        font-family: 'Lora', serif;
        flex-wrap: wrap;
    }
    .rpg-navbar-brand {
        color: #EADCB6;
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.8rem;
        font-weight: 700;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .rpg-navbar-brand:hover {
        color: #C49A52;
    }
    .rpg-menu-toggle {
        display: none;
        background: none;
        border: 1px solid #C49A52;
        color: #EADCB6;
        padding: 0.4rem 0.8rem;
        font-size: 1.2rem;
        cursor: pointer;
        border-radius: 4px;
    }
    .rpg-navbar-nav {
        list-style: none;
        display: flex;
        gap: 1.5rem;
        align-items: center;
        margin: 0;
        padding: 0;
    }
    .rpg-nav-link {
        color: #EADCB6;
        text-decoration: none;
        font-size: 1.1rem;
        transition: color 0.3s;
        padding-bottom: 2px;
    }
    .rpg-nav-link:hover, .rpg-nav-link.active {
        color: #C49A52;
    }
    .rpg-nav-link.active {
        border-bottom: 1px solid #C49A52;
    }
    .rpg-logout-btn {
        color: #D96A6A !important;
        font-weight: bold;
        border: 1px solid #D96A6A;
        padding: 0.3rem 0.8rem;
        border-radius: 4px;
        border-bottom: 1px solid #D96A6A !important;
    }
    .rpg-logout-btn:hover {
        background-color: rgba(217, 106, 106, 0.1);
    }
    .rpg-sign-btn {
        background-color: #C49A52;
        color: #3E2723 !important;
        font-family: 'Cormorant Garamond', serif;
        font-weight: bold;
        padding: 0.4rem 1rem;
        border-radius: 4px;
        text-decoration: none;
        text-transform: uppercase;
    }
    .rpg-sign-btn:hover {
        background-color: #EADCB6;
    }

    @media (max-width: 768px) {
        .rpg-menu-toggle {
            display: block;
        }
        .rpg-navbar-nav {
            display: none;
            flex-direction: column;
            width: 100%;
            gap: 1rem;
            padding-top: 1rem;
            text-align: center;
        }
        .rpg-navbar-nav.show {
            display: flex;
        }
    }
</style>

<header class="rpg-navbar-container">
    <a class="rpg-navbar-brand" href="index.php">Chronicle AI</a>
    <button class="rpg-menu-toggle" onclick="toggleRpgMenu()">☰ Menu</button>
    <ul class="rpg-navbar-nav" id="rpgNavbarNav">
        <li><a class="rpg-nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a></li>
        <li><a class="rpg-nav-link <?php echo ($current_page == 'archive.php') ? 'active' : ''; ?>" href="archive.php">Archive</a></li>
        <li><a class="rpg-nav-link <?php echo ($current_page == 'community.php') ? 'active' : ''; ?>" href="community.php">Community</a></li>
        <?php if(isset($_SESSION['member_id'])): ?>
            <li><a class="rpg-nav-link <?php echo ($current_page == 'my_journeys.php') ? 'active' : ''; ?>" href="my_journeys.php">My Journeys</a></li>
            <li>
                <a class="rpg-nav-link rpg-logout-btn" href="logout.php">
                    Logout (<?php echo htmlspecialchars($_SESSION['username'] ?? 'Adventurer'); ?>)
                </a>
            </li>
        <?php else: ?>
            <li><a class="rpg-nav-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>" href="login.php">Login</a></li>
            <li>
                <a class="rpg-sign-btn" href="register.php">Sign Pact</a>
            </li>
        <?php endif; ?>
    </ul>
</header>

<script>
    function toggleRpgMenu() {
        const nav = document.getElementById('rpgNavbarNav');
        nav.classList.toggle('show');
    }
</script>