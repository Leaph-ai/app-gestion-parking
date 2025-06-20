
<?php
function getParkingSpots($pdo, $page, $sortBy = 'id', $sortOrder = 'asc') {
    $limit = 10;

    $page = max(1, (int)$page);
    $offset = ($page - 1) * $limit;

    $allowedColumns = ['id', 'number', 'type', 'is_occupied'];
    $allowedOrders = ['asc', 'desc'];

    if (!in_array($sortBy, $allowedColumns)) {
        $sortBy = 'id';
    }
    if (!in_array($sortOrder, $allowedOrders)) {
        $sortOrder = 'asc';
    }

    $query = "SELECT id, number, type, is_occupied FROM parking_spots ORDER BY {$sortBy} {$sortOrder} LIMIT :limit OFFSET :offset";
    $res = $pdo->prepare($query);
    $res->bindParam(':limit', $limit, PDO::PARAM_INT);
    $res->bindParam(':offset', $offset, PDO::PARAM_INT);
    $res->execute();
    return $res->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalParkingSpots(PDO $pdo): int {
    $res = $pdo->query("SELECT COUNT(*) as count FROM parking_spots");
    $result = $res->fetch(PDO::FETCH_ASSOC);
    return (int)$result['count'];
}

function deleteParkingSpot(PDO $pdo, int $id): void {
    $query = "DELETE FROM parking_spots WHERE id = :id";
    $res = $pdo->prepare($query);
    $res->bindParam(':id', $id);
    $res->execute();
}
function getParkingSpotTypes(): array {
    return [
        1 => 'Normale',
        2 => 'Handicapée',
        3 => 'Réservée'
    ];
}
function parkingSpotNumberExists(PDO $pdo, int $number, ?int $excludeId = null): bool {
    $query = "SELECT COUNT(*) as count FROM parking_spots WHERE number = :number";

    if ($excludeId !== null) {
        $query .= " AND id != :exclude_id";
    }

    $res = $pdo->prepare($query);
    $res->bindParam(':number', $number);

    if ($excludeId !== null) {
        $res->bindParam(':exclude_id', $excludeId);
    }

    $res->execute();
    $result = $res->fetch(PDO::FETCH_ASSOC);
    return (int)$result['count'] > 0;
}