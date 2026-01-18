<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/SportVarietyFit/Assets/css/header_footer.css">
    <link rel="icon" href="/SportVarietyFit/bildes/SportVarietyFitIcon.png">
</head>
<body>
<header>
    <div class="logo">
        <a href="/SportVarietyFit/Index.php"><img src="/SportVarietyFit/bildes/SportVarietyFitLogoWhite.png" alt="SportVariety Fit Logo"></a>
    </div>
    <nav>
        <ul class="menu">
            <li><a href="/SportVarietyFit/Index.php">Home</a></li>
            <li><a href="#">Challenges</a></li>
            <li><a href="#">Group Chats</a></li>
            <li><a href="/SportVarietyFit/Form/contact.php">Contact</a></li>

            <div class="profile">
                <img src="/SportVarietyFit/bildes/profile.png" alt="Profile">
                <ul class="profile-dropdown">
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li><a href="#">Profile</a></li>
                        <li><a href="#">Logout</a></li>
                    <?php } else { ?>
                        <li><a href="#">Login</a></li>
                        <li><a href="#">Register</a></li>
                    <?php } ?>
                </ul>
            </div>
        </ul>
    </nav>
    <div class="hamburger" onclick="toggleMenu()">&#9776;</div>
    <div class="mobile-nav">
        <ul>
            <li><a href="/SportVarietyFit/Index.php">Home</a></li>
            <li><a href="#">Challenges</a></li>
            <li><a href="#">Group Chats</a></li>
            <li><a href="/SportVarietyFit/Form/contact.php">Contact</a></li>
            <li class="mobile-profile">
                <a href="#" onclick="toggleMobileProfileDropdown()">Profile</a>
                <ul class="mobile-profile-dropdown">
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li><a href="#">Profile</a></li>
                        <li><a href="#">Logout</a></li>
                    <?php } else { ?>
                        <li><a href="#">Login</a></li>
                        <li><a href="#">Register</a></li>
                    <?php } ?>
                </ul>
            </li>
        </ul>
    </div>
</header>
<script>
    function toggleMenu() {
        const mobileNav = document.querySelector('.mobile-nav');
        const hamburger = document.querySelector('.hamburger');
        
        mobileNav.classList.toggle('active');
        
        hamburger.classList.toggle('active');
    }

    function toggleProfileDropdown() {
        const profileDropdown = document.querySelector('.profile-dropdown');
        profileDropdown.classList.toggle('active');
    }

    function toggleMobileProfileDropdown() {
        const mobileProfileDropdown = document.querySelector('.mobile-profile-dropdown');
        mobileProfileDropdown.classList.toggle('active');
    }
</script>
</body>
</html>