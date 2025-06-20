<?php
function getAllPricingRules(PDO $pdo, bool $activeOnly = false): array {
    $whereClause = $activeOnly ? ' WHERE active = 1' : '';
    $query = "SELECT pr.*, 
                CASE pr.spot_type 
                    WHEN 1 THEN 'Normale' 
                    WHEN 2 THEN 'Handicapée' 
                    WHEN 3 THEN 'Réservée' 
                    ELSE 'Inconnue' 
                END as spot_type_name
              FROM pricing pr" . $whereClause . " 
              ORDER BY pr.spot_type ASC, pr.start_hour ASC";

    $res = $pdo->query($query);
    return $res->fetchAll(PDO::FETCH_ASSOC);
}

function getPricingRuleById(PDO $pdo, int $id): ?array {
    $query = "SELECT * FROM pricing WHERE id = :id";
    $res = $pdo->prepare($query);
    $res->bindParam(':id', $id, PDO::PARAM_INT);
    $res->execute();

    $rule = $res->fetch(PDO::FETCH_ASSOC);
    return $rule ?: null;
}
function createPricingRule(PDO $pdo, string $label, int $spotType, string $startHour, string $endHour, array $days, float $pricePerHour, int $minDurationMinutes = 0, bool $active = true): bool {
    $query = "INSERT INTO pricing (label, spot_type, start_hour, end_hour, days, price_per_hour, min_duration_minutes, active) 
              VALUES (:label, :spot_type, :start_hour, :end_hour, :days, :price_per_hour, :min_duration_minutes, :active)";

    $res = $pdo->prepare($query);
    return $res->execute([
        ':label' => $label,
        ':spot_type' => $spotType,
        ':start_hour' => $startHour,
        ':end_hour' => $endHour,
        ':days' => implode(',', $days),
        ':price_per_hour' => $pricePerHour,
        ':min_duration_minutes' => $minDurationMinutes,
        ':active' => $active ? 1 : 0
    ]);
}

function updatePricingRule(PDO $pdo, int $id, string $label, int $spotType, string $startHour, string $endHour, array $days, float $pricePerHour, int $minDurationMinutes = 0, bool $active = true): bool {
    $query = "UPDATE pricing 
              SET label = :label, spot_type = :spot_type, start_hour = :start_hour, end_hour = :end_hour, 
                  days = :days, price_per_hour = :price_per_hour, min_duration_minutes = :min_duration_minutes, active = :active 
              WHERE id = :id";

    $res = $pdo->prepare($query);
    return $res->execute([
        ':id' => $id,
        ':label' => $label,
        ':spot_type' => $spotType,
        ':start_hour' => $startHour,
        ':end_hour' => $endHour,
        ':days' => implode(',', $days),
        ':price_per_hour' => $pricePerHour,
        ':min_duration_minutes' => $minDurationMinutes,
        ':active' => $active ? 1 : 0
    ]);
}

function deletePricingRule(PDO $pdo, int $id): bool {
    $query = "DELETE FROM pricing WHERE id = :id";
    $res = $pdo->prepare($query);
    return $res->execute([':id' => $id]);
}

function togglePricingRuleStatus(PDO $pdo, int $id, bool $active): bool {
    $query = "UPDATE pricing SET active = :active WHERE id = :id";
    $res = $pdo->prepare($query);
    return $res->execute([
        ':id' => $id,
        ':active' => $active ? 1 : 0
    ]);
}

function calculatePriceForReservation(PDO $pdo, int $spotType, string $startTime, string $endTime): float {
    $start = new DateTime($startTime);
    $end = new DateTime($endTime);
    $totalPrice = 0.0;

    // Récupérer toutes les règles actives pour ce type de place
    $query = "SELECT * FROM pricing WHERE spot_type = :spot_type AND active = 1 ORDER BY price_per_hour DESC";
    $res = $pdo->prepare($query);
    $res->execute([':spot_type' => $spotType]);
    $rules = $res->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rules)) {

        $hours = $start->diff($end)->h + ($start->diff($end)->days * 24);
        return $hours * 5.0;
    }

    $current = clone $start;
    while ($current < $end) {
        $nextHour = clone $current;
        $nextHour->add(new DateInterval('PT1H'));

        if ($nextHour > $end) {
            $nextHour = clone $end;
        }

        $hours = $current->diff($nextHour)->h + ($current->diff($nextHour)->i / 60);

        $applicableRule = findApplicableRule($rules, $current);

        if ($applicableRule) {
            $hourlyRate = (float)$applicableRule['price_per_hour'];
            $totalPrice += $hours * $hourlyRate;
        } else {
            $totalPrice += $hours * 5.0;
        }

        $current = $nextHour;
    }

    return round($totalPrice, 2);
}

function findApplicableRule(array $rules, DateTime $dateTime): ?array {
    $dayOfWeek = strtolower($dateTime->format('D')); // mon, tue, etc.
    $time = $dateTime->format('H:i:s');

    $dayMapping = [
        'mon' => 'mon',
        'tue' => 'tue',
        'wed' => 'wed',
        'thu' => 'thu',
        'fri' => 'fri',
        'sat' => 'sat',
        'sun' => 'sun'
    ];

    $dbDay = $dayMapping[$dayOfWeek] ?? $dayOfWeek;

    foreach ($rules as $rule) {
        $ruleDays = explode(',', $rule['days']);

        // Vérifier si le jour correspond
        if (in_array($dbDay, $ruleDays)) {
            // Vérifier si l'heure correspond
            if ($time >= $rule['start_hour'] && $time < $rule['end_hour']) {
                return $rule;
            }
        }
    }

    return null;
}

function validatePricingRuleData(array $data): array {
    $errors = [];

    if (empty($data['label'])) {
        $errors[] = 'Le libellé est obligatoire';
    }

    if (empty($data['spot_type']) || !in_array((int)$data['spot_type'], [1, 2, 3])) {
        $errors[] = 'Le type de place est obligatoire et doit être valide';
    }

    if (empty($data['start_hour'])) {
        $errors[] = 'L\'heure de début est obligatoire';
    }

    if (empty($data['end_hour'])) {
        $errors[] = 'L\'heure de fin est obligatoire';
    }

    if (!empty($data['start_hour']) && !empty($data['end_hour'])) {
        if ($data['start_hour'] >= $data['end_hour']) {
            $errors[] = 'L\'heure de fin doit être après l\'heure de début';
        }
    }

    if (empty($data['days']) || !is_array($data['days'])) {
        $errors[] = 'Au moins un jour doit être sélectionné';
    } else {
        $validDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        foreach ($data['days'] as $day) {
            if (!in_array($day, $validDays)) {
                $errors[] = 'Jour invalide sélectionné';
                break;
            }
        }
    }

    if (empty($data['price_per_hour']) || (float)$data['price_per_hour'] <= 0) {
        $errors[] = 'Le prix par heure doit être supérieur à 0';
    }

    if (isset($data['min_duration_minutes']) && (int)$data['min_duration_minutes'] < 0) {
        $errors[] = 'La durée minimale ne peut pas être négative';
    }

    return $errors;
}

function getSpotTypesForPricing(): array {
    return [
        1 => 'Place Normale',
        2 => 'Place Handicapée',
        3 => 'Place Réservée'
    ];
}

function getDaysOfWeek(): array {
    return [
        'mon' => 'Lundi',
        'tue' => 'Mardi',
        'wed' => 'Mercredi',
        'thu' => 'Jeudi',
        'fri' => 'Vendredi',
        'sat' => 'Samedi',
        'sun' => 'Dimanche'
    ];
}

function getPricingStats(PDO $pdo): array {
    $stats = [];

    $query = "SELECT COUNT(*) as total FROM pricing";
    $res = $pdo->query($query);
    $stats['total'] = $res->fetch(PDO::FETCH_ASSOC)['total'];

    $query = "SELECT COUNT(*) as active FROM pricing WHERE active = 1";
    $res = $pdo->query($query);
    $stats['active'] = $res->fetch(PDO::FETCH_ASSOC)['active'];

    $stats['inactive'] = $stats['total'] - $stats['active'];

    $query = "SELECT MIN(price_per_hour) as min_price, MAX(price_per_hour) as max_price FROM pricing WHERE active = 1";
    $res = $pdo->query($query);
    $priceRange = $res->fetch(PDO::FETCH_ASSOC);
    $stats['min_price'] = $priceRange['min_price'] ?? 0;
    $stats['max_price'] = $priceRange['max_price'] ?? 0;

    return $stats;
}