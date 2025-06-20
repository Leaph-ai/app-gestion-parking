<form id="register-form">
    <div class="login-container">
        <img src="assets/images/SymParkinglogo.png" alt="Logo" class="logo">
        <input id="firstNameField" name="firstName" type="text" class="input-field" placeholder="Prénom" required>
        <input id="lastNameField" name="lastName" type="text" class="input-field" placeholder="Nom" required>
        <input id="emailField" name="email" type="email" class="input-field" placeholder="Email" required>
        <input id="phoneNumberField" name="phoneNumber" type="tel" class="input-field" placeholder="Numéro de téléphone (optionnel)">
        <input id="passwordField" name="password" type="password" class="input-field" placeholder="Mot de passe" required>
        <input id="confirmPasswordField" name="confirmPassword" type="password" class="input-field" placeholder="Confirmer le mot de passe" required>
        <button id="registerButton" name="button" class="login-button button">S'INSCRIRE</button>
        <p class="login-link">Déjà inscrit ? <a href="index.php?component=login">Se connecter</a></p>
    </div>
</form>

<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        const registerButton = document.getElementById('registerButton');

        registerButton.addEventListener('click', async (e) => {
            e.preventDefault();

            const form = document.querySelector('#register-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const phoneNumber = document.getElementById('phoneNumberField').value.trim();
            if (phoneNumber) {
                const cleanPhone = phoneNumber.replace(/[\s\-\.]/g, '');
                if (!/^\+?[0-9]{8,15}$/.test(cleanPhone)) {
                    alert('Le numéro de téléphone n\'est pas valide. Format attendu : 8 à 15 chiffres, avec ou sans indicatif pays.');
                    return;
                }
            }

            const formData = new FormData(form);

            try {
                const response = await fetch('index.php?component=register', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.registration === true) {
                    alert('Inscription réussie ! Vous pouvez maintenant vous connecter.');
                    window.location.href = 'index.php?component=login';
                } else if (result.errors) {
                    alert(result.errors.join('\n'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'inscription.');
            }
        });
    });
</script>