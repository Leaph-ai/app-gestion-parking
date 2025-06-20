<?php

/**
 * Récupère les statistiques complètes pour le tableau de bord
 * @param PDO $pdo
 * @return array
 */
function getDashboardStats(PDO $pdo): array {
    try {
        $stats = [];

        // === STATISTIQUES UTILISATEURS ===
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_users'] = (int)($result['count'] ?? 0);

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE active = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['active_users'] = (int)($result['count'] ?? 0);

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 2");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['admin_users'] = (int)($result['count'] ?? 0);

        // === STATISTIQUES PLACES DE PARKING ===
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM parking_spots");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_spots'] = (int)($result['count'] ?? 0);

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM parking_spots WHERE is_occupied = 0");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['available_spots'] = (int)($result['count'] ?? 0);

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM parking_spots WHERE is_occupied = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['occupied_spots'] = (int)($result['count'] ?? 0);

        // === STATISTIQUES PAR TYPE DE PLACE ===
        // Mapping des types numériques vers des noms
        $typeNames = [
            1 => 'normal',
            2 => 'handicapped',
            3 => 'admin'
        ];

        $stmt = $pdo->query("
            SELECT 
                type,
                COUNT(*) as total,
                SUM(CASE WHEN is_occupied = 0 THEN 1 ELSE 0 END) as available
            FROM parking_spots 
            GROUP BY type
        ");
        $typeStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stats['spots_by_type'] = [];
        foreach ($typeStats as $type) {
            $typeName = $typeNames[$type['type']] ?? 'unknown';
            $stats['spots_by_type'][$typeName] = [
                'total' => (int)$type['total'],
                'available' => (int)$type['available'],
                'occupied' => (int)$type['total'] - (int)$type['available']
            ];
        }

        // === STATISTIQUES RÉSERVATIONS ===
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_bookings'] = (int)($result['count'] ?? 0);

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE is_cancelled = 0");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['active_bookings'] = (int)($result['count'] ?? 0);

        $stmt = $pdo->query("
            SELECT COUNT(*) as count 
            FROM bookings 
            WHERE DATE(start_time) = CURDATE() AND is_cancelled = 0
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['today_bookings'] = (int)($result['count'] ?? 0);

        // === REVENUS ===
        $stmt = $pdo->query("
            SELECT COALESCE(SUM(total_price), 0) as total 
            FROM bookings 
            WHERE is_cancelled = 0
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_revenue'] = (float)($result['total'] ?? 0);

        $stmt = $pdo->query("
            SELECT COALESCE(SUM(total_price), 0) as total 
            FROM bookings 
            WHERE is_cancelled = 0 
            AND MONTH(start_time) = MONTH(CURDATE()) 
            AND YEAR(start_time) = YEAR(CURDATE())
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['monthly_revenue'] = (float)($result['total'] ?? 0);

        // === STATISTIQUES TARIFS ===
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM pricing");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['pricing_rules'] = (int)($result['count'] ?? 0);

        return $stats;

    } catch (Exception $e) {
        error_log("Erreur getDashboardStats: " . $e->getMessage());
        return getDefaultStats();
    }
}

/**
 * Récupère les statistiques spécifiques à un utilisateur
 * @param PDO $pdo
 * @param int $userId
 * @return array
 */
function getUserStats(PDO $pdo, int $userId): array {
    try {
        $stats = [];

        // Nombre de réservations de l'utilisateur
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM bookings WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_bookings'] = (int)($result['count'] ?? 0);

        // Réservations actives (non annulées)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM bookings 
            WHERE user_id = ? AND is_cancelled = 0
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['active_bookings'] = (int)($result['count'] ?? 0);

        // Total dépensé
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(total_price), 0) as total 
            FROM bookings 
            WHERE user_id = ? AND is_cancelled = 0
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_spent'] = (float)($result['total'] ?? 0);

        // Prochaine réservation
        $stmt = $pdo->prepare("
            SELECT 
                b.start_time,
                b.end_time,
                ps.number as spot_number,
                ps.type
            FROM bookings b
            JOIN parking_spots ps ON b.spot_id = ps.id
            WHERE b.user_id = ? 
            AND b.is_cancelled = 0 
            AND b.start_time > NOW()
            ORDER BY b.start_time ASC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $nextBooking = $stmt->fetch(PDO::FETCH_ASSOC);

        // Convertir le type numérique en nom
        if ($nextBooking) {
            $typeNames = [
                1 => 'normale',
                2 => 'handicapée',
                3 => 'staff'
            ];
            $nextBooking['type'] = $typeNames[$nextBooking['type']] ?? 'inconnue';
        }

        $stats['next_booking'] = $nextBooking ?: null;

        return $stats;

    } catch (Exception $e) {
        error_log("Erreur getUserStats: " . $e->getMessage());
        return [
            'total_bookings' => 0,
            'active_bookings' => 0,
            'total_spent' => 0,
            'next_booking' => null
        ];
    }
}

/**
 * Retourne les statistiques par défaut en cas d'erreur
 * @return array
 */
function getDefaultStats(): array {
    return [
        'total_users' => 0,
        'active_users' => 0,
        'admin_users' => 0,
        'total_spots' => 0,
        'available_spots' => 0,
        'occupied_spots' => 0,
        'spots_by_type' => [],
        'total_bookings' => 0,
        'active_bookings' => 0,
        'today_bookings' => 0,
        'total_revenue' => 0,
        'monthly_revenue' => 0,
        'pricing_rules' => 0
    ];
}

/**
 * Récupère le résumé des revenus par période
 * @param PDO $pdo
 * @return array
 */
function getRevenueStats(PDO $pdo): array {
    try {
        $stats = [];

        // Revenus de la semaine
        $stmt = $pdo->query("
            SELECT COALESCE(SUM(total_price), 0) as total 
            FROM bookings 
            WHERE is_cancelled = 0 
            AND start_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['weekly_revenue'] = (float)($result['total'] ?? 0);

        // Revenus du jour
        $stmt = $pdo->query("
            SELECT COALESCE(SUM(total_price), 0) as total 
            FROM bookings 
            WHERE is_cancelled = 0 
            AND DATE(start_time) = CURDATE()
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['daily_revenue'] = (float)($result['total'] ?? 0);

        // Revenus de l'année
        $stmt = $pdo->query("
            SELECT COALESCE(SUM(total_price), 0) as total 
            FROM bookings 
            WHERE is_cancelled = 0 
            AND YEAR(start_time) = YEAR(CURDATE())
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['yearly_revenue'] = (float)($result['total'] ?? 0);

        return $stats;

    } catch (Exception $e) {
        error_log("Erreur getRevenueStats: " . $e->getMessage());
        return [
            'weekly_revenue' => 0,
            'daily_revenue' => 0,
            'yearly_revenue' => 0
        ];
    }
}

/**
 * Récupère les places disponibles pour la page d'accueil
 * @param PDO $pdo
 * @return array
 */
function getAvailableSpotsSummary(PDO $pdo): array {
    try {
        $spots = [];

        // Toutes les places disponibles
        $stmt = $pdo->query("
            SELECT 
                ps.id,
                ps.number,
                ps.type,
                CASE 
                    WHEN ps.type = 1 THEN 'Normale'
                    WHEN ps.type = 2 THEN 'Handicapée'
                    WHEN ps.type = 3 THEN 'Staff'
                    ELSE 'Inconnue'
                END as type_label
            FROM parking_spots ps
            WHERE ps.is_occupied = 0
            ORDER BY ps.number ASC
            LIMIT 10
        ");

        $spots = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $spots;

    } catch (Exception $e) {
        error_log("Erreur getAvailableSpotsSummary: " . $e->getMessage());
        return [];
    }
}