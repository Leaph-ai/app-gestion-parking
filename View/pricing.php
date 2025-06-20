<div class="pricing-container">
    <div class="pricing-header">
        <h1>
            <i class="fas fa-euro-sign"></i>
            <?= isAdmin() ? 'Gestion des tarifs' : 'Consulter les tarifs' ?>
        </h1>

        <?php if (isAdmin()): ?>
            <div class="header-actions">
                <button onclick="toggleQuickForm()" class="btn btn-success" id="toggle-form-btn">
                    <i class="fas fa-plus"></i>
                    Création rapide
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isAdmin()): ?>
        <div id="quick-form-container" class="quick-form-container" style="display: none;">
            <div class="quick-form-card">
                <div class="quick-form-header">
                    <h3>
                        <i class="fas fa-bolt"></i>
                        Création rapide d'une règle
                    </h3>
                    <button onclick="toggleQuickForm()" class="btn-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="quick-pricing-form" class="quick-form">
                    <div class="quick-form-body">
                        <div class="form-row-quick">
                            <div class="form-group-quick">
                                <label for="quick_label">Libellé *</label>
                                <input type="text" id="quick_label" name="label" class="form-control-quick"
                                       placeholder="Ex: Tarif semaine journée" required>
                            </div>

                            <div class="form-group-quick">
                                <label for="quick_spot_type">Type de place *</label>
                                <select id="quick_spot_type" name="spot_type" class="form-control-quick" required>
                                    <option value="">Choisir</option>
                                    <?php foreach ($spotTypes as $typeId => $typeName): ?>
                                        <option value="<?= $typeId ?>"><?= htmlspecialchars($typeName) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group-quick">
                                <label for="quick_price">Prix/heure (€) *</label>
                                <input type="number" id="quick_price" name="price_per_hour" class="form-control-quick"
                                       step="0.01" min="0" placeholder="5.00" required>
                            </div>
                        </div>

                        <div class="form-row-quick">
                            <div class="form-group-quick">
                                <label for="quick_start_hour">Début *</label>
                                <input type="time" id="quick_start_hour" name="start_hour" class="form-control-quick"
                                       value="08:00" required>
                            </div>

                            <div class="form-group-quick">
                                <label for="quick_end_hour">Fin *</label>
                                <input type="time" id="quick_end_hour" name="end_hour" class="form-control-quick"
                                       value="18:00" required>
                            </div>

                            <div class="form-group-quick">
                                <label for="quick_min_duration">Durée min. (min)</label>
                                <input type="number" id="quick_min_duration" name="min_duration_minutes"
                                       class="form-control-quick" value="0" min="0">
                            </div>
                        </div>

                        <div class="form-section-quick">
                            <label class="section-label">Jours d'application *</label>
                            <div class="days-quick">
                                <?php foreach ($daysOfWeek as $dayCode => $dayName): ?>
                                    <label class="day-quick">
                                        <input type="checkbox" name="days[]" value="<?= $dayCode ?>">
                                        <span><?= substr($dayName, 0, 3) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-section-quick">
                            <label class="toggle-label">
                                <input type="checkbox" name="active" checked>
                                <span>Règle active</span>
                            </label>
                        </div>
                    </div>

                    <div class="quick-form-footer">
                        <button type="button" onclick="resetQuickForm()" class="btn btn-secondary">
                            <i class="fas fa-undo"></i>
                            Réinitialiser
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i>
                            Créer la règle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="stats-container">
        <div class="stat-card total">
            <div class="stat-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['total'] ?></div>
                <div class="stat-label">Total règles</div>
            </div>
        </div>

        <div class="stat-card active">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['active'] ?></div>
                <div class="stat-label">Actives</div>
            </div>
        </div>

        <?php if (isAdmin()): ?>
            <div class="stat-card inactive">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?= $stats['inactive'] ?></div>
                    <div class="stat-label">Inactives</div>
                </div>
            </div>
        <?php endif; ?>

        <div class="stat-card price-range">
            <div class="stat-icon">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number"><?= number_format($stats['min_price'], 2) ?>€ - <?= number_format($stats['max_price'], 2) ?>€</div>
                <div class="stat-label">Fourchette de prix</div>
            </div>
        </div>
    </div>

    <?php if (!isAdmin()): ?>
        <div class="price-calculator">
            <div class="calculator-card">
                <div class="calculator-header">
                    <h3>
                        <i class="fas fa-calculator"></i>
                        Simulateur de prix
                    </h3>
                </div>
                <div class="calculator-body">
                    <div class="calculator-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="calc_spot_type">Type de place</label>
                                <select id="calc_spot_type" class="form-control">
                                    <option value="">Sélectionnez un type</option>
                                    <?php foreach ($spotTypes as $typeId => $typeName): ?>
                                        <option value="<?= $typeId ?>"><?= htmlspecialchars($typeName) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="calc_start_time">Début</label>
                                <input type="datetime-local" id="calc_start_time" class="form-control"
                                       min="<?= date('Y-m-d\TH:i', strtotime('+1 hour')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="calc_end_time">Fin</label>
                                <input type="datetime-local" id="calc_end_time" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="button" onclick="calculatePrice()" class="btn btn-primary">
                                    <i class="fas fa-calculator"></i>
                                    Calculer
                                </button>
                            </div>
                        </div>
                        <div id="price-result" class="price-result" style="display: none;">
                            <div class="result-card">
                                <h4>Prix estimé</h4>
                                <div class="price-amount" id="calculated-price">0.00€</div>
                                <div class="price-details" id="price-details"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $rulesByType = [];
    foreach ($rules as $rule) {
        $rulesByType[$rule['spot_type']][] = $rule;
    }
    ?>

    <?php foreach ($spotTypes as $typeId => $typeName): ?>
        <?php if (isset($rulesByType[$typeId])): ?>
            <div class="spot-type-section">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-parking"></i>
                        <?= htmlspecialchars($typeName) ?>
                    </h2>
                    <span class="rules-count"><?= count($rulesByType[$typeId]) ?> règle(s)</span>
                </div>

                <div class="rules-table-container">
                    <table class="rules-table">
                        <thead>
                        <tr>
                            <th>Libellé</th>
                            <th>Période</th>
                            <th>Jours</th>
                            <th>Prix/heure</th>
                            <th>Durée min.</th>
                            <?php if (isAdmin()): ?>
                                <th>Statut</th>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rulesByType[$typeId] as $rule): ?>
                            <tr class="<?= $rule['active'] ? 'active-rule' : 'inactive-rule' ?>">
                                <td>
                                    <div class="rule-label">
                                        <?= htmlspecialchars($rule['label']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="time-period">
                                        <?= date('H:i', strtotime($rule['start_hour'])) ?>
                                        -
                                        <?= date('H:i', strtotime($rule['end_hour'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="days-list">
                                        <?php
                                        $ruleDays = explode(',', $rule['days']);
                                        $dayNames = [];
                                        foreach ($ruleDays as $day) {
                                            if (isset($daysOfWeek[$day])) {
                                                $dayNames[] = substr($daysOfWeek[$day], 0, 3);
                                            }
                                        }
                                        echo implode(', ', $dayNames);
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="price-value">
                                        <?= number_format((float)$rule['price_per_hour'], 2) ?>€
                                    </div>
                                </td>
                                <td>
                                    <div class="min-duration">
                                        <?= $rule['min_duration_minutes'] > 0 ? $rule['min_duration_minutes'] . ' min' : 'Aucune' ?>
                                    </div>
                                </td>

                                <?php if (isAdmin()): ?>
                                    <td>
                                        <div class="status-toggle">
                                            <label class="toggle-switch">
                                                <input type="checkbox"
                                                    <?= $rule['active'] ? 'checked' : '' ?>
                                                       onchange="toggleRuleStatus(<?= $rule['id'] ?>, this.checked)">
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="actions">
                                        <?php if (isAdmin()): ?>
                                            <button onclick="editRule(<?= $rule['id'] ?>)" class="btn btn-edit btn-sm" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteRule(<?= $rule['id'] ?>)" class="btn btn-delete btn-sm" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (empty($rules)): ?>
        <div class="no-rules">
            <i class="fas fa-info-circle"></i>
            <h3>Aucune règle de tarification trouvée</h3>
            <p><?= isAdmin() ? 'Créez votre première règle de tarification.' : 'Aucune règle active pour le moment.' ?></p>
        </div>
    <?php endif; ?>
</div>

<script src="assets/javascript/components/pricing.js"></script>
<script src="assets/javascript/services/pricing.js"></script>