<?php

function getParkingSpotById(PDO $pdo, int $id): ?array {
    $query = "SELECT * FROM parking_spots WHERE id = :id";
    $res = $pdo->prepare($query);
    $res->bindParam(':id', $id);
    $res->execute();
    $result = $res->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

function isSpotNumberUnique(PDO $pdo, int $number, ?int $excludeId = null): bool {
    return !parkingSpotNumberExists($pdo, $number, $excludeId);
}


function validateParkingSpotData(array $data, PDO $pdo, ?int $excludeId = null): array {
    $errors = [];

    if (empty($data['number']) || !is_numeric($data['number'])) {
        $errors[] = 'Le numéro de place est requis et doit être un nombre';
    } elseif ((int)$data['number'] <= 0) {
        $errors[] = 'Le numéro de place doit être un nombre positif';
    } elseif (parkingSpotNumberExists($pdo, (int)$data['number'], $excludeId)) {
        $errors[] = 'Ce numéro de place est déjà utilisé';
    }

    if (empty($data['type']) || !in_array((int)$data['type'], [1, 2, 3])) {
        $errors[] = 'Le type de place doit être : 1 (Normale), 2 (Handicapée) ou 3 (Réservée)';
    }

    return $errors;
}


function createParkingSpot(PDO $pdo, int $number, int $type, int $isOccupied = 0): void {
    $query = "INSERT INTO parking_spots (number, type, is_occupied) VALUES (:number, :type, :is_occupied)";
    $res = $pdo->prepare($query);
    $res->bindParam(':number', $number);
    $res->bindParam(':type', $type);
    $res->bindParam(':is_occupied', $isOccupied);
    $res->execute();
}

function updateParkingSpot(PDO $pdo, int $id, int $number, int $type, int $isOccupied): void {
    $query = "UPDATE parking_spots SET number = :number, type = :type, is_occupied = :is_occupied WHERE id = :id";
    $res = $pdo->prepare($query);
    $res->bindParam(':id', $id);
    $res->bindParam(':number', $number);
    $res->bindParam(':type', $type);
    $res->bindParam(':is_occupied', $isOccupied);
    $res->execute();
}
