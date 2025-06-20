<?php
/**
 * Footer du site - Informations de contact et liens utiles
 */
?>

<footer class="site-footer">
    <div class="footer-container">
        <!-- Informations de contact -->
        <div class="footer-section contact-info">
            <h4><i class="fas fa-building"></i> SymParking</h4>
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <strong>Adresse :</strong><br>
                    123 Avenue des Parkings<br>
                    75001 Paris, France
                </div>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                    <strong>Téléphone :</strong><br>
                    <a href="tel:+33123456789">+33 1 23 45 67 89</a>
                </div>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <strong>Email :</strong><br>
                    <a href="mailto:contact@parkingplus.fr">contact@parkingplus.fr</a>
                </div>
            </div>
        </div>

        <!-- Horaires d'ouverture -->
        <div class="footer-section opening-hours">
            <h4><i class="fas fa-clock"></i> Horaires d'accès</h4>
            <div class="hours-list">
                <div class="hour-item">
                    <span class="day">Lundi - Vendredi :</span>
                    <span class="time">24h/24</span>
                </div>
                <div class="hour-item">
                    <span class="day">Samedi - Dimanche :</span>
                    <span class="time">24h/24</span>
                </div>
                <div class="hour-item special">
                    <span class="day">Support client :</span>
                    <span class="time">8h - 20h</span>
                </div>
            </div>
        </div>

        <!-- Navigation rapide -->
        <div class="footer-section quick-nav">
            <h4><i class="fas fa-link"></i> Navigation</h4>
            <div class="nav-links">
                <?php if (isAuthenticated()): ?>
                    <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
                    <a href="index.php?component=booking"><i class="fas fa-calendar-alt"></i> Mes réservations</a>
                    <a href="index.php?component=parking_spots"><i class="fas fa-car"></i> Places disponibles</a>
                    <a href="index.php?component=user"><i class="fas fa-user"></i> Mon profil</a>
                    <?php if (isAdmin()): ?>
                        <a href="index.php?component=users"><i class="fas fa-users"></i> Gestion des utilisateurs</a>
                        <a href="index.php?component=pricing"><i class="fas fa-euro-sign"></i> Tarification</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="index.php?component=home"><i class="fas fa-home"></i> Accueil</a>
                    <a href="index.php?component=login"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                    <a href="index.php?component=register"><i class="fas fa-user-plus"></i> Inscription</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informations pratiques -->
        <div class="footer-section practical-info">
            <h4><i class="fas fa-info-circle"></i> Informations pratiques</h4>
            <div class="info-list">
                <div class="info-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Paiement sécurisé PayPal</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-mobile-alt"></i>
                    <span>Accès mobile 24h/24</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Parking sécurisé et surveillé</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-wheelchair"></i>
                    <span>Accès PMR disponible</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de copyright -->
    <div class="footer-bottom">
        <div class="footer-container">
            <div class="copyright">
                <p>&copy; <?= date('Y') ?> SymParking. Tous droits réservés.</p>
            </div>
            <div class="footer-links">
                <a href="#" onclick="showTerms()">Conditions d'utilisation</a>
                <a href="#" onclick="showPrivacy()">Politique de confidentialité</a>
                <a href="#" onclick="showContact()">Nous contacter</a>
            </div>
        </div>
    </div>
</footer>

<script>
    // Fonctions pour les liens du footer
    function showTerms() {
        alert('Conditions d\'utilisation - Cette fonctionnalité sera bientôt disponible.');
    }

    function showPrivacy() {
        alert('Politique de confidentialité - Cette fonctionnalité sera bientôt disponible.');
    }

    function showContact() {
        alert('Pour nous contacter, utilisez les informations ci-dessus ou appelez le +33 1 23 45 67 89');
    }
</script>