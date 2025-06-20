<div class="with-navbar">
    <div class="pricing-container">
        <!-- Header -->
        <div class="pricing-header">
            <h1>
                <i class="fas fa-calendar-check"></i>
                <?= isAdmin() ? 'Gestion des réservations' : 'Mes réservations' ?>
            </h1>
            <div class="header-actions">
                <button id="toggle-form-btn" class="btn btn-success" onclick="toggleQuickForm()">
                    <i class="fas fa-plus"></i> Réservation rapide
                </button>
            </div>
        </div>

        <!-- Formulaire rapide de réservation (modifié) -->
        <div id="quick-form-container" class="quick-form-container" style="display: none;">
            <div class="quick-form-card">
                <div class="quick-form-header">
                    <h3><i class="fas fa-calendar-plus"></i> Créer une réservation</h3>
                    <button type="button" class="btn-close" onclick="toggleQuickForm()">×</button>
                </div>
                <div class="quick-form-body">
                    <form id="quick-booking-form">
                        <!-- Sélection des horaires en premier -->
                        <div class="form-row-quick">
                            <div class="form-group-quick">
                                <label for="quick_start_time">
                                    <i class="fas fa-clock"></i> Heure de début
                                </label>
                                <input type="datetime-local" id="quick_start_time" name="start_time"
                                       class="form-control-quick" required
                                       min="<?= date('Y-m-d\TH:i') ?>">
                            </div>
                            <div class="form-group-quick">
                                <label for="quick_end_time">
                                    <i class="fas fa-clock"></i> Heure de fin
                                </label>
                                <input type="datetime-local" id="quick_end_time" name="end_time"
                                       class="form-control-quick" required>
                            </div>
                        </div>

                        <!-- Bouton pour chercher les places disponibles -->
                        <div class="form-actions">
                            <button type="button" id="search-spots-btn" class="btn btn-primary">
                                <i class="fas fa-search"></i> Rechercher places disponibles
                            </button>
                        </div>

                        <!-- Statistiques des places -->
                        <div id="spots-stats" style="display: none;" class="spots-stats">
                            <h4><i class="fas fa-chart-pie"></i> Places disponibles</h4>
                            <div class="stats-grid">
                                <div class="stat-item stat-normal">
                                    <i class="fas fa-car"></i>
                                    <span class="stat-label">Normales</span>
                                    <span class="stat-count" id="normal-count">0</span>
                                </div>
                                <div class="stat-item stat-handicapped">
                                    <i class="fas fa-wheelchair"></i>
                                    <span class="stat-label">Handicapées</span>
                                    <span class="stat-count" id="handicapped-count">0</span>
                                </div>
                                <?php if (isAdmin()): ?>
                                <div class="stat-item stat-admin">
                                    <i class="fas fa-crown"></i>
                                    <span class="stat-label">Staff</span>
                                    <span class="stat-count" id="admin-count">0</span>
                                </div>
                                <?php endif; ?>
                                <div class="stat-item stat-total">
                                    <i class="fas fa-parking"></i>
                                    <span class="stat-label">Total</span>
                                    <span class="stat-count" id="total-count">0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sélection de la place -->
                        <div id="spot-selection" style="display: none;">
                            <div class="form-group-quick">
                                <label for="quick_spot_id">
                                    <i class="fas fa-parking"></i> Choisir une place
                                </label>
                                <select id="quick_spot_id" name="spot_id" class="form-control-quick" required>
                                    <option value="">Sélectionnez une place</option>
                                </select>
                            </div>
                        </div>

                        <!-- Affichage du prix et disponibilité -->
                        <div id="booking-info" style="display: none;">
                            <div class="info-box">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <div id="availability-status"></div>
                                    <div id="price-info" style="display: none;">
                                        <strong>Prix estimé : <span id="calculated-price">0.00</span> €</strong>
                                        <p>Le prix final sera calculé selon les règles de tarification.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="form-actions">
                            <button type="button" id="check-availability-btn" class="btn btn-secondary" style="display: none;">
                                <i class="fas fa-calculator"></i> Calculer le prix
                            </button>
                            <div id="payment-section" style="display: none;">
                                <div id="paypal-button-container"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if (isAdmin()): ?>
            <!-- Statistiques pour les admins -->
            <div class="quick-form-container">
                <div class="quick-form-card">
                    <div class="quick-form-header">
                        <h3><i class="fas fa-chart-bar"></i> Statistiques des réservations</h3>
                    </div>
                    <div class="quick-form-body">
                        <div class="form-row-quick">
                            <div class="info-item">
                                <label>Total réservations :</label>
                                <span><?= $stats['total'] ?></span>
                            </div>
                            <div class="info-item">
                                <label>Réservations actives :</label>
                                <span><?= $stats['active'] ?></span>
                            </div>
                            <div class="info-item">
                                <label>Réservations annulées :</label>
                                <span><?= $stats['cancelled'] ?></span>
                            </div>
                            <div class="info-item">
                                <label>Chiffre d'affaires :</label>
                                <span><?= number_format($stats['revenue'], 2) ?> €</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Table des réservations -->
        <div class="users-table-container">
            <?php if (empty($bookings)): ?>
                <div class="no-data">
                    <i class="fas fa-calendar-times" style="font-size: 48px; color: #6c757d; margin-bottom: 20px;"></i>
                    <p><?= isAdmin() ? 'Aucune réservation trouvée' : 'Vous n\'avez pas encore de réservation' ?></p>
                    <button class="btn btn-primary" onclick="toggleQuickForm()">
                        <i class="fas fa-plus"></i> Créer une réservation
                    </button>
                </div>
            <?php else: ?>
                <table class="users-table">
                    <thead>
                    <tr>
                        <th>
                            <a href="?component=booking&sort=id&order=<?= $sortBy === 'id' && $sortOrder === 'asc' ? 'desc' : 'asc' ?>" class="sort-header">
                                ID <?= $sortBy === 'id' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' ?>
                            </a>
                        </th>
                        <?php if (isAdmin()): ?>
                            <th>
                                <a href="?component=booking&sort=user_id&order=<?= $sortBy === 'user_id' && $sortOrder === 'asc' ? 'desc' : 'asc' ?>" class="sort-header">
                                    Utilisateur <?= $sortBy === 'user_id' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' ?>
                                </a>
                            </th>
                        <?php endif; ?>
                        <th>
                            <a href="?component=booking&sort=spot_id&order=<?= $sortBy === 'spot_id' && $sortOrder === 'asc' ? 'desc' : 'asc' ?>" class="sort-header">
                                Place <?= $sortBy === 'spot_id' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a href="?component=booking&sort=start_time&order=<?= $sortBy === 'start_time' && $sortOrder === 'asc' ? 'desc' : 'asc' ?>" class="sort-header">
                                Début <?= $sortBy === 'start_time' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a href="?component=booking&sort=end_time&order=<?= $sortBy === 'end_time' && $sortOrder === 'asc' ? 'desc' : 'asc' ?>" class="sort-header">
                                Fin <?= $sortBy === 'end_time' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' ?>
                            </a>
                        </th>
                        <th>Prix</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>#<?= $booking['id'] ?></td>
                            <?php if (isAdmin()): ?>
                                <td><?= htmlspecialchars($booking['user_name']) ?></td>
                            <?php endif; ?>
                            <td>
                                <?= htmlspecialchars($booking['spot_label']) ?>
                                <span class="type-badge type-<?= $booking['spot_type'] ?>">
                                        Type <?= $booking['spot_type'] ?>
                                    </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($booking['start_time'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($booking['end_time'])) ?></td>
                            <td><?= number_format($booking['total_price'], 2) ?> €</td>
                            <td>
                                <?php if ($booking['is_cancelled']): ?>
                                    <span class="status-badge status-inactive">Annulée</span>
                                <?php else: ?>
                                    <span class="status-badge status-active">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <?php if (!$booking['is_cancelled']): ?>
                                    <button class="btn btn-sm btn-delete" onclick="cancelBooking(<?= $booking['id'] ?>)">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?component=booking&page=<?= $page - 1 ?>&sort=<?= $sortBy ?>&order=<?= $sortOrder ?>" class="pagination-btn">
                                <i class="fas fa-chevron-left"></i> Précédent
                            </a>
                        <?php endif; ?>

                        <div class="pagination-numbers">
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?component=booking&page=<?= $i ?>&sort=<?= $sortBy ?>&order=<?= $sortOrder ?>"
                                   class="pagination-number <?= $i === $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <a href="?component=booking&page=<?= $page + 1 ?>&sort=<?= $sortBy ?>&order=<?= $sortOrder ?>" class="pagination-btn">
                                Suivant <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- SDK PayPal -->
<script src="https://www.paypal.com/sdk/js?client-id=ARFJ5sSahjtryD3Fam8-8Fpcjx7dukDaR27nmnFu5oZWQWEOH3qcWfYxk5FQJxdAPAHHfPABnJPX6Pop&currency=EUR"></script>

<!-- Services JavaScript -->
<script src="assets/javascript/services/booking.js"></script>

<!-- Composants JavaScript -->
<script src="assets/javascript/components/booking.js"></script>