<?php if (isset($_GET['error']) && $_GET['error'] === 'account_disabled'): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        Votre compte a été désactivé. Contactez l'administrateur pour plus d'informations.
    </div>
<?php endif; ?>

<form id="login-form">
    <div class="login-container">
        <img src="assets/images/SymParkinglogo.png" alt="Logo" class="logo">
        <input id="emailField" name="email" type="email" class="input-field" placeholder="Email" required>
        <input id="passwordField" name="password" type="password" class="input-field" placeholder="Mot de passe" required>
        <button id="loginButton" name="button" class="login-button button">SE CONNECTER</button>
        <p class="login-link">Pas encore de compte ? <a href="index.php?component=register">S'inscrire</a></p>
        <p id="error-message" style="color: red; display: none;"></p>
    </div>
</form>

<script src="./assets/javascript/login.js" type="module"></script>
<script type="module">
    import { login } from './assets/javascript/services/login.js';

    document.addEventListener('DOMContentLoaded', () => {
        const loginButton = document.getElementById('loginButton');
        const errorMessage = document.getElementById('error-message');

        loginButton.addEventListener('click', async (e) => {
            e.preventDefault();
            errorMessage.style.display = 'none';

            const formLogin = document.querySelector('#login-form');
            const emailInput = formLogin.elements['email'];
            const passwordInput = formLogin.elements['password'];

            // Vérification manuelle des champs
            if (!emailInput.value || !passwordInput.value) {
                errorMessage.textContent = 'Veuillez remplir tous les champs';
                errorMessage.style.display = 'block';
                return;
            }

            if (!formLogin.checkValidity()) {
                formLogin.reportValidity();
                return;
            }

            try {
                const email = emailInput.value;
                const password = passwordInput.value;

                console.log('Valeurs du formulaire:', { email, password });

                const loginResult = await login(email, password);

                if (loginResult.authentication === true) {
                    window.location.href = 'index.php';
                } else if (loginResult.errors) {
                    errorMessage.textContent = loginResult.errors.join('\n');
                    errorMessage.style.display = 'block';
                }
            } catch (error) {
                console.error('Erreur lors de la connexion:', error);
                errorMessage.textContent = 'Une erreur est survenue lors de la connexion';
                errorMessage.style.display = 'block';
            }
        });
    });
</script>