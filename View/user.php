<div class="user-form-container">
    <div class="user-form-header">
        <h1>
            <i class="fas fa-user-<?= $isEdit ? 'edit' : 'plus' ?>"></i>
            <?php if ($isProfile): ?>
                Mon profil
            <?php elseif ($isEdit): ?>
                Modifier l'utilisateur
            <?php else: ?>
                Créer un utilisateur
            <?php endif; ?>
        </h1>

        <?php if ($isProfile): ?>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        <?php else: ?>
            <a href="index.php?component=users" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="user-form-card">
        <form method="POST" class="user-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">
                        <i class="fas fa-user"></i> Prénom *
                    </label>
                    <input type="text"
                           id="first_name"
                           name="first_name"
                           class="form-input"
                           value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="last_name">
                        <i class="fas fa-user"></i> Nom *
                    </label>
                    <input type="text"
                           id="last_name"
                           name="last_name"
                           class="form-input"
                           value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                           required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email *
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-input"
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="phone_number">
                        <i class="fas fa-phone"></i> Numéro de téléphone
                    </label>
                    <input type="tel"
                           id="phone_number"
                           name="phone_number"
                           class="form-input"
                           value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>"
                           placeholder="Ex: +33123456789 ou 0123456789">
                    <small class="form-help">
                        Format : 8 à 15 chiffres, avec ou sans indicatif pays (optionnel)
                    </small>
                </div>
            </div>

            <?php if ($isProfile): ?>
                <div class="form-group">
                    <label for="current_password">
                        <i class="fas fa-lock"></i> Mot de passe actuel *
                    </label>
                    <input type="password"
                           id="current_password"
                           name="current_password"
                           class="form-input"
                           placeholder="Entrez votre mot de passe actuel"
                           required>
                    <small class="form-help">
                        Votre mot de passe actuel est requis pour toute modification de profil.
                    </small>
                </div>

                <div class="form-group">
                    <label for="new_password">
                        <i class="fas fa-key"></i> Nouveau mot de passe (optionnel)
                    </label>
                    <input type="password"
                           id="new_password"
                           name="new_password"
                           class="form-input"
                           placeholder="Nouveau mot de passe (laisser vide pour ne pas changer)">
                    <small class="form-help">
                        Laissez vide si vous ne souhaitez pas changer votre mot de passe. Minimum 8 caractères.
                    </small>
                </div>
            <?php elseif (!$isEdit): ?>
                <div class="form-group">
                    <label for="new_password">
                        <i class="fas fa-lock"></i> Mot de passe *
                    </label>
                    <input type="password"
                           id="new_password"
                           name="new_password"
                           class="form-input"
                           placeholder="Mot de passe"
                           required>
                    <small class="form-help">
                        Minimum 8 caractères.
                    </small>
                </div>
            <?php endif; ?>


            <?php if ($canManageUsers && !$isProfile): ?>
                <div class="admin-section">
                    <h3><i class="fas fa-user-shield"></i> Options administrateur</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="role">
                                <i class="fas fa-user-tag"></i> Rôle
                            </label>
                            <select id="role" name="role" class="form-input">
                                <option value="1" <?= (!isset($user) || $user['role'] == 1) ? 'selected' : '' ?>>
                                    Utilisateur
                                </option>
                                <option value="2" <?= (isset($user) && $user['role'] == 2) ? 'selected' : '' ?>>
                                    Administrateur
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="active">
                                <i class="fas fa-user-check"></i> Statut du compte
                            </label>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox"
                                       id="active"
                                       name="active"
                                       value="1"
                                       style="width: auto; margin: 0;"
                                    <?= (!isset($user) || $user['active'] == 1) ? 'checked' : '' ?>>
                                <label for="active" style="margin: 0; font-weight: normal;">
                                    Compte actif
                                </label>
                            </div>
                            <small class="form-help">
                                Décochez pour désactiver le compte. Un compte inactif ne peut pas se connecter.
                            </small>
                        </div>
                    </div>

                    <?php if ($isEdit): ?>
                        <div class="password-info">
                            <div class="info-box">
                                <i class="fas fa-info-circle"></i>
                                <p><strong>Sécurité :</strong> Seul l'utilisateur peut modifier son propre mot de passe.
                                    Pour réinitialiser un mot de passe, demandez à l'utilisateur d'utiliser la fonction
                                    "Mot de passe oublié" sur la page de connexion.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Affichage des informations pour l'utilisateur normal -->
                <div class="user-info-section">
                    <h3><i class="fas fa-info-circle"></i> Informations du compte</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Rôle :</label>
                            <span class="role-badge <?= ($user['role'] ?? 1) == 2 ? 'role-admin' : 'role-user' ?>">
                                <?= ($user['role'] ?? 1) == 2 ? 'Administrateur' : 'Utilisateur' ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Statut :</label>
                            <span class="status-badge <?= ($user['active'] ?? 1) == 1 ? 'status-active' : 'status-inactive' ?>">
                                <?= ($user['active'] ?? 1) == 1 ? 'Actif' : 'Inactif' ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?php if ($isProfile): ?>
                        Mettre à jour mon profil
                    <?php elseif ($isEdit): ?>
                        Modifier l'utilisateur
                    <?php else: ?>
                        Créer l'utilisateur
                    <?php endif; ?>
                </button>

                <?php if ($isProfile): ?>
                    <button type="button" id="delete-account-btn" class="btn btn-delete">
                        <i class="fas fa-user-times"></i> Supprimer mon compte
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if ($isProfile): ?>
    <script type="module">
        import { handleDeleteAccount } from "./assets/javascript/components/user.js";
        handleDeleteAccount();
    </script>
<?php endif; ?>