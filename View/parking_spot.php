<div class="user-form-container">
    <div class="user-form-header">
        <h1>
            <i class="fas fa-parking"></i>
            <?php if ($isEdit): ?>
                Modifier la place de parking
            <?php else: ?>
                Créer une place de parking
            <?php endif; ?>
        </h1>

        <a href="index.php?component=parking_spots" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
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
                    <label for="number">
                        <i class="fas fa-hashtag"></i> Numéro de place *
                    </label>
                    <input type="number"
                           id="number"
                           name="number"
                           class="form-input"
                           value="<?= htmlspecialchars($parkingSpot['number'] ?? '') ?>"
                           min="1"
                           required>
                    <small class="form-help">
                        Numéro unique pour identifier la place de parking
                    </small>
                </div>

                <div class="form-group">
                    <label for="type">
                        <i class="fas fa-tag"></i> Type de place *
                    </label>
                    <select id="type" name="type" class="form-input" required>
                        <option value="1" <?= (!isset($parkingSpot) || $parkingSpot['type'] == 1) ? 'selected' : '' ?>>
                            Normale
                        </option>
                        <option value="2" <?= (isset($parkingSpot) && $parkingSpot['type'] == 2) ? 'selected' : '' ?>>
                            Handicapée
                        </option>
                        <option value="3" <?= (isset($parkingSpot) && $parkingSpot['type'] == 3) ? 'selected' : '' ?>>
                            Réservée
                        </option>
                    </select>
                    <small class="form-help">
                        Type de place selon les besoins spécifiques
                    </small>
                </div>
            </div>

            <!-- Statut d'occupation UNIQUEMENT en mode édition -->
            <?php if ($isEdit): ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="is_occupied">
                            <i class="fas fa-car"></i> Statut d'occupation
                        </label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox"
                                   id="is_occupied"
                                   name="is_occupied"
                                   value="1"
                                   style="width: auto; margin: 0;"
                                <?= (isset($parkingSpot) && $parkingSpot['is_occupied'] == 1) ? 'checked' : '' ?>>
                            <label for="is_occupied" style="margin: 0; font-weight: normal;">
                                Place occupée
                            </label>
                        </div>
                        <small class="form-help">
                            Cochez si la place est actuellement occupée par un véhicule
                        </small>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?php if ($isEdit): ?>
                        Modifier la place
                    <?php else: ?>
                        Créer la place
                    <?php endif; ?>
                </button>
            </div>
        </form>
    </div>
</div>