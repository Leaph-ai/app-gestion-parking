<?php
$current_page = $_GET['component'] ?? '';
$userRole = $_SESSION['role'] ?? 1;
?>

<nav>
    <a href="index.php">
        <img src="assets/images/SymParkinglogo_nav.png" alt="Logo">
    </a>

    <a href="index.php?component=pricing" class="<?= $current_page === 'pricing' ? 'active' : '' ?>">
        <i class="fas fa-euro-sign"></i> Tarifs
    </a>

    <?php if (isset($_SESSION['auth']) && $_SESSION['auth']) : ?>
        <a href="index.php?component=booking" class="<?= $current_page === 'booking' ? 'active' : '' ?>">
            <i class="fas fa-parking"></i> Réserver une place
        </a>

        <?php if ($userRole === 2) : // Réservé aux administrateurs ?>
            <a href="index.php?component=parking_spots" class="<?= $current_page === 'parking_spots' || $current_page === 'parking_spot' ? 'active' : '' ?>">
                <i class="fas fa-parking"></i> Gestion des places
            </a>
            <a href="index.php?component=users" class="<?= $current_page === 'users' || $current_page === 'user' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Utilisateurs
            </a>
        <?php endif; ?>

        <div class="user-dropdown">
            <div class="user-info" onclick="toggleDropdown()">
                <i class="fas fa-user me-2"></i>
                <span><?php echo $_SESSION['username']; ?></span>
                <?php if ($userRole === 2) : ?>
                    <span class="admin-badge"><i class="fas fa-crown"></i> Admin</span>
                <?php endif; ?>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
            </div>

            <div class="dropdown-menu" id="userDropdown">
                <div class="dropdown-header">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo $_SESSION['username']; ?></span>
                    <?php if ($userRole === 2) : ?>
                        <small class="role-indicator">Administrateur</small>
                    <?php else : ?>
                        <small class="role-indicator">Utilisateur</small>
                    <?php endif; ?>
                </div>

                <div class="dropdown-divider"></div>

                <a href="index.php?component=user&action=profile" class="dropdown-item">
                    <i class="fas fa-user-cog"></i> Mon profil
                </a>

                <div class="dropdown-divider"></div>

                <a href="index.php?disconnect=true" class="dropdown-item logout-item">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    <?php else : ?>
        <div class="user">
            <a href="index.php?component=login">
                <i class="fa-solid fa-right-to-bracket me-2"></i>Connexion
            </a>
            <a href="index.php?component=register">
                <i class="fa-solid fa-user-plus me-2"></i>Inscription
            </a>
        </div>
    <?php endif; ?>
</nav>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        const arrow = document.querySelector('.dropdown-arrow');

        dropdown.classList.toggle('show');
        arrow.classList.toggle('rotated');
    }

    // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', function(event) {
        const userDropdown = document.querySelector('.user-dropdown');
        const dropdown = document.getElementById('userDropdown');

        if (!userDropdown.contains(event.target)) {
            dropdown.classList.remove('show');
            document.querySelector('.dropdown-arrow').classList.remove('rotated');
        }
    });
</script>