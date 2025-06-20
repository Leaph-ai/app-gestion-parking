

<div class="home-container">
    <div class="welcome-section">
        <h1><i class="fas fa-home"></i> <?= htmlspecialchars($welcomeMessage) ?></h1>
        <p>Bonjour <?= htmlspecialchars($username) ?> !</p>

        <?php if ($isAdmin): ?>
            <div class="admin-badge">
                <i class="fas fa-crown"></i> Administrateur
            </div>
        <?php endif; ?>
    </div>

    <!-- Statistiques et fonctionnalités principales -->
    <div class="dashboard-cards">
        <?php if ($isAdmin): ?>
            <!-- === SECTION ADMIN === -->

            <!-- Statistiques générales -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Statistiques générales</h3>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?= $dashboardData['total_users'] ?></div>
                            <div class="stat-label">Utilisateurs</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $dashboardData['total_spots'] ?></div>
                            <div class="stat-label">Places totales</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $dashboardData['available_spots'] ?></div>
                            <div class="stat-label">Places libres</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= number_format($dashboardData['monthly_revenue'], 2) ?>€</div>
                            <div class="stat-label">Revenus du mois</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gestion des utilisateurs -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users"></i>
                    <h3>Gestion des utilisateurs</h3>
                </div>
                <div class="card-body">
                    <p>Gérez les comptes utilisateurs, leurs rôles et permissions.</p>
                    <div class="stats">
                        <strong><?= $dashboardData['active_users'] ?></strong> utilisateurs actifs sur
                        <strong><?= $dashboardData['total_users'] ?></strong> total
                        (<?= $dashboardData['admin_users'] ?> administrateurs)
                    </div>
                    <div class="card-actions">
                        <a href="index.php?component=users" class="btn btn-primary">
                            <i class="fas fa-users"></i> Voir les utilisateurs
                        </a>
                        <a href="index.php?component=user&action=create" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Créer un utilisateur
                        </a>
                    </div>
                </div>
            </div>

            <!-- Gestion des places de parking -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-car"></i>
                    <h3>Gestion des places</h3>
                </div>
                <div class="card-body">
                    <p>Gérez les places de parking et leur disponibilité.</p>
                    <div class="stats">
                        <strong><?= $dashboardData['available_spots'] ?></strong> places disponibles sur
                        <strong><?= $dashboardData['total_spots'] ?></strong> total
                        (<?= $dashboardData['occupied_spots'] ?> occupées)
                    </div>

                    <?php if (!empty($dashboardData['spots_by_type'])): ?>
                        <div class="spot-types-breakdown">
                            <?php foreach ($dashboardData['spots_by_type'] as $type => $typeStats): ?>
                                <?php if ($type !== 'admin'): // Exclure les places admin pour les utilisateurs normaux ?>
                                    <div class="type-stat">
                                        <span class="type-badge type-<?= strtolower($type) ?>">
                                            <?= ucfirst($type) ?>: <?= $typeStats['available'] ?>/<?= $typeStats['total'] ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="card-actions">
                        <a href="index.php?component=parking_spots" class="btn btn-primary">
                            <i class="fas fa-car"></i> Voir les places
                        </a>
                        <a href="index.php?component=parking_spot&action=create" class="btn btn-success">
                            <i class="fas fa-plus"></i> Créer une place
                        </a>
                    </div>
                </div>
            </div>

            <!-- Gestion des tarifs -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-euro-sign"></i>
                    <h3>Gestion des tarifs</h3>
                </div>
                <div class="card-body">
                    <p>Configurez les prix et règles de tarification.</p>
                    <div class="stats">
                        <strong><?= $dashboardData['pricing_rules'] ?></strong> règles de tarification configurées
                    </div>
                    <div class="card-actions">
                        <a href="index.php?component=pricing" class="btn btn-primary">
                            <i class="fas fa-euro-sign"></i> Gérer les tarifs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Gestion des réservations -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Réservations</h3>
                </div>
                <div class="card-body">
                    <p>Suivez et gérez toutes les réservations.</p>
                    <div class="stats">
                        <strong><?= $dashboardData['active_bookings'] ?></strong> réservations actives<br>
                        <strong><?= $dashboardData['today_bookings'] ?></strong> réservations aujourd'hui<br>
                        <strong><?= number_format($dashboardData['total_revenue'], 2) ?>€</strong> de revenus total
                    </div>
                    <div class="card-actions">
                        <a href="index.php?component=booking" class="btn btn-primary">
                            <i class="fas fa-calendar-check"></i> Voir les réservations
                        </a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- === SECTION UTILISATEUR === -->

            <!-- Mes statistiques -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i>
                    <h3>Mes statistiques</h3>
                </div>
                <div class="card-body">
                    <div class="user-stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?= $userStats['total_bookings'] ?></div>
                            <div class="stat-label">Réservations</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $userStats['active_bookings'] ?></div>
                            <div class="stat-label">Actives</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= number_format($userStats['total_spent'], 2) ?>€</div>
                            <div class="stat-label">Total dépensé</div>
                        </div>
                    </div>

                    <?php if ($userStats['next_booking']): ?>
                        <div class="next-booking-info">
                            <h4><i class="fas fa-clock"></i> Prochaine réservation</h4>
                            <p>
                                Place <strong><?= $userStats['next_booking']['spot_number'] ?></strong>
                                (<?= ucfirst($userStats['next_booking']['type']) ?>)<br>
                                Le <?= date('d/m/Y à H:i', strtotime($userStats['next_booking']['start_time'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Places disponibles maintenant -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-car-side"></i>
                    <h3>Places disponibles</h3>
                </div>
                <div class="card-body">
                    <p><?= $dashboardData['available_spots'] ?> places libres sur <?= $dashboardData['total_spots'] ?> total</p>

                    <?php if (!empty($dashboardData['spots_by_type'])): ?>
                        <div class="available-spots-summary">
                            <?php foreach ($dashboardData['spots_by_type'] as $type => $typeStats): ?>
                                <?php if ($type !== 'admin'): // Exclure les places admin pour les utilisateurs ?>
                                    <div class="type-summary">
                                        <span class="type-badge type-<?= strtolower($type) ?>">
                                            <?= ucfirst($type) ?>
                                        </span>
                                        <span class="type-count">
                                            <?= $typeStats['available'] ?> disponibles
                                        </span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="card-actions">
                        <a href="index.php?component=booking" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nouvelle réservation
                        </a>
                        <a href="index.php?component=booking" class="btn btn-primary">
                            <i class="fas fa-list"></i> Mes réservations
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Section commune : Mon profil -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-cog"></i>
                <h3>Mon profil</h3>
            </div>
            <div class="card-body">
                <p>Gérez vos informations personnelles et votre mot de passe.</p>
                <div class="card-actions">
                    <a href="index.php?component=user&action=profile" class="btn btn-primary">
                        <i class="fas fa-user-cog"></i> Modifier mon profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>