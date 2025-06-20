<?php
function getBookings(PDO $pdo, int $page = 1, string $sortBy = 'id', string $sortOrder = 'desc', int $userId = null): array {
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $allowedColumns = ['id', 'user_id', 'spot_id', 'start_time', 'end_time', 'total_price', 'is_cancelled'];
    $allowedOrders = ['asc', 'desc'];

    if (!in_array($sortBy, $allowedColumns)) {
        $sortBy = 'id';
    }
    if (!in_array($sortOrder, $allowedOrders)) {
        $sortOrder = 'desc';
    }

    $whereClause = '';
    $params = [];

    if ($userId) {
        $whereClause = 'WHERE b.user_id = :user_id';
        $params[':user_id'] = $userId;
    }

    $query = "
        SELECT b.*, 
               CONCAT(u.first_name, ' ', u.last_name) as user_name,
               CONCAT('Place #', ps.number) as spot_label,
               ps.type as spot_type
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN parking_spots ps ON b.spot_id = ps.id
        {$whereClause}
        ORDER BY {$sortBy} {$sortOrder}
        LIMIT :limit OFFSET :offset
    ";

    $res = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $res->bindValue($key, $value);
    }
    $res->bindValue(':limit', $limit, PDO::PARAM_INT);
    $res->bindValue(':offset', $offset, PDO::PARAM_INT);
    $res->execute();

    return $res->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalBookings(PDO $pdo, int $userId = null): int {
    $whereClause = '';
    $params = [];

    if ($userId) {
        $whereClause = 'WHERE user_id = :user_id';
        $params[':user_id'] = $userId;
    }

    $query = "SELECT COUNT(*) as total FROM bookings {$whereClause}";
    $res = $pdo->prepare($query);

    foreach ($params as $key => $value) {
        $res->bindValue($key, $value);
    }

    $res->execute();
    $result = $res->fetch(PDO::FETCH_ASSOC);
    return (int)$result['total'];
}

function getBookingById(PDO $pdo, int $id): array|false {
    $query = "
        SELECT b.*, 
               CONCAT(u.first_name, ' ', u.last_name) as user_name,
               u.email as user_email,
               CONCAT('Place #', ps.number) as spot_label,
               ps.type as spot_type
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN parking_spots ps ON b.spot_id = ps.id
        WHERE b.id = :id
    ";

    $res = $pdo->prepare($query);
    $res->bindParam(':id', $id, PDO::PARAM_INT);
    $res->execute();

    return $res->fetch(PDO::FETCH_ASSOC);
}

function createBooking(PDO $pdo, int $userId, int $spotId, string $startTime, string $endTime, float $totalPrice): int {
    $query = "
        INSERT INTO bookings (user_id, spot_id, start_time, end_time, total_price, is_cancelled)
        VALUES (:user_id, :spot_id, :start_time, :end_time, :total_price, 0)
    ";

    $res = $pdo->prepare($query);
    $res->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $res->bindParam(':spot_id', $spotId, PDO::PARAM_INT);
    $res->bindParam(':start_time', $startTime);
    $res->bindParam(':end_time', $endTime);
    $res->bindParam(':total_price', $totalPrice);
    $res->execute();

    return (int)$pdo->lastInsertId();
}

function cancelBooking(PDO $pdo, int $bookingId): bool {
    $query = "UPDATE bookings SET is_cancelled = 1 WHERE id = :id";
    $res = $pdo->prepare($query);
    $res->bindParam(':id', $bookingId, PDO::PARAM_INT);
    return $res->execute();
}

function isSpotAvailable(PDO $pdo, int $spotId, string $startTime, string $endTime, int $excludeBookingId = null): bool {
    $excludeClause = $excludeBookingId ? 'AND id != :exclude_id' : '';

    $query = "
        SELECT COUNT(*) as count
        FROM bookings
        WHERE spot_id = :spot_id
        AND is_cancelled = 0
        AND (
            (start_time <= :start_time AND end_time > :start_time)
            OR (start_time < :end_time AND end_time >= :end_time)
            OR (start_time >= :start_time AND end_time <= :end_time)
        )
        {$excludeClause}
    ";

    $res = $pdo->prepare($query);
    $res->bindParam(':spot_id', $spotId, PDO::PARAM_INT);
    $res->bindParam(':start_time', $startTime);
    $res->bindParam(':end_time', $endTime);

    if ($excludeBookingId) {
        $res->bindParam(':exclude_id', $excludeBookingId, PDO::PARAM_INT);
    }

    $res->execute();
    $result = $res->fetch(PDO::FETCH_ASSOC);

    return (int)$result['count'] === 0;
}

function getAvailableSpots(PDO $pdo): array {
    $query = "
        SELECT id, number, type, is_occupied as active,
               CASE 
                   WHEN type = 1 THEN 'normal'
                   WHEN type = 2 THEN 'handicapped'
                   WHEN type = 3 THEN 'admin'
                   ELSE 'unknown'
               END as type_string,
               CASE 
                   WHEN type = 1 THEN 'Place normale'
                   WHEN type = 2 THEN 'Place handicapée'
                   WHEN type = 3 THEN 'Place réservée admin'
                   ELSE 'Type inconnu'
               END as type_label
        FROM parking_spots
        WHERE is_occupied = 0
        ORDER BY type, number
    ";

    $res = $pdo->prepare($query);
    $res->execute();
    return $res->fetchAll(PDO::FETCH_ASSOC);
}


function calculateBookingPrice(PDO $pdo, int $spotType, string $startTime, string $endTime): float {
    require_once "Model/pricing.php";
    return calculatePriceForReservation($pdo, $spotType, $startTime, $endTime);
}

function getBookingStats(PDO $pdo): array {
    $stats = [];

    $query = "SELECT COUNT(*) as total FROM bookings";
    $res = $pdo->query($query);
    $stats['total'] = $res->fetch(PDO::FETCH_ASSOC)['total'];

    $query = "SELECT COUNT(*) as active FROM bookings WHERE is_cancelled = 0";
    $res = $pdo->query($query);
    $stats['active'] = $res->fetch(PDO::FETCH_ASSOC)['active'];

    $query = "SELECT COUNT(*) as cancelled FROM bookings WHERE is_cancelled = 1";
    $res = $pdo->query($query);
    $stats['cancelled'] = $res->fetch(PDO::FETCH_ASSOC)['cancelled'];

    $query = "SELECT COALESCE(SUM(total_price), 0) as revenue FROM bookings WHERE is_cancelled = 0";
    $res = $pdo->query($query);
    $stats['revenue'] = $res->fetch(PDO::FETCH_ASSOC)['revenue'];

    return $stats;
}

function validateBookingData(array $data): array {
    $errors = [];

    if (empty($data['spot_id'])) {
        $errors[] = 'La place de parking est requise';
    }

    if (empty($data['start_time'])) {
        $errors[] = 'L\'heure de début est requise';
    }

    if (empty($data['end_time'])) {
        $errors[] = 'L\'heure de fin est requise';
    }

    if (!empty($data['start_time']) && !empty($data['end_time'])) {
        $start = new DateTime($data['start_time']);
        $end = new DateTime($data['end_time']);

        if ($start >= $end) {
            $errors[] = 'L\'heure de fin doit être postérieure à l\'heure de début';
        }

        if ($start < new DateTime()) {
            $errors[] = 'L\'heure de début ne peut pas être dans le passé';
        }
    }

    return $errors;
}
function getAvailableSpotsForPeriod(PDO $pdo, string $startTime = null, string $endTime = null, bool $isAdmin = false): array {
    $timeClause = '';
    $params = [];
    
    if ($startTime && $endTime) {
        $timeClause = "
            AND ps.id NOT IN (
                SELECT DISTINCT b.spot_id 
                FROM bookings b 
                WHERE b.is_cancelled = 0
                AND (
                    (b.start_time <= :start_time AND b.end_time > :start_time)
                    OR (b.start_time < :end_time AND b.end_time >= :end_time)
                    OR (b.start_time >= :start_time AND b.end_time <= :end_time)
                )
            )
        ";
        $params[':start_time'] = $startTime;
        $params[':end_time'] = $endTime;
    }

    $adminClause = $isAdmin ? '' : 'AND ps.type != 3';
    
    $query = "
        SELECT ps.id, ps.number, ps.type, ps.is_occupied,
               CASE 
                   WHEN ps.type = 1 THEN 'normal'
                   WHEN ps.type = 2 THEN 'handicapped'
                   WHEN ps.type = 3 THEN 'admin'
                   ELSE 'unknown'
               END as type_string,
               CASE 
                   WHEN ps.type = 1 THEN 'Place normale'
                   WHEN ps.type = 2 THEN 'Place handicapée'
                   WHEN ps.type = 3 THEN 'Place réservée admin'
                   ELSE 'Type inconnu'
               END as type_label,
               CASE 
                   WHEN ps.type = 1 THEN '#28a745'
                   WHEN ps.type = 2 THEN '#007bff'
                   WHEN ps.type = 3 THEN '#dc3545'
                   ELSE '#6c757d'
               END as type_color
        FROM parking_spots ps
        WHERE ps.is_occupied = 0
        {$adminClause}
        {$timeClause}
        ORDER BY ps.type, ps.number
    ";

    $res = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $res->bindValue($key, $value);
    }
    $res->execute();
    
    return $res->fetchAll(PDO::FETCH_ASSOC);
}

function getSpotCountsByType(PDO $pdo, string $startTime = null, string $endTime = null, bool $isAdmin = false): array {
    $spots = getAvailableSpotsForPeriod($pdo, $startTime, $endTime, $isAdmin);
    
    $counts = [
        'normal' => 0,
        'handicapped' => 0,
        'admin' => 0,
        'total' => 0
    ];
    
    foreach ($spots as $spot) {
        // Utiliser type_string au lieu de type directement
        if (isset($spot['type_string'])) {
            $typeString = $spot['type_string'];
            if (isset($counts[$typeString])) {
                $counts[$typeString]++;
            }
            $counts['total']++;
        }
    }
    
    return $counts;
}