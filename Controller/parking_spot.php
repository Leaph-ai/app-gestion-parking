
<?php
/**
 * @var PDO $pdo
 */

require "Model/parking_spots.php";
require "Model/parking_spot.php";

$errors = [];
$parkingSpot = null;
$isEdit = false;

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if (isset($_GET["action"]) && $_GET["action"] === "edit" && isset($_GET["id"])) {
    $isEdit = true;
    $spotId = (int)$_GET["id"];

    $parkingSpot = getParkingSpotById($pdo, $spotId);
    if (!$parkingSpot) {
        header("Location: index.php?component=parking_spots");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = (int)($_POST['number'] ?? 0);
    $type = (int)($_POST['type'] ?? 1);

    $isOccupied = 0; // Par défaut libre
    if ($isEdit) {
        $isOccupied = isset($_POST['is_occupied']) ? 1 : 0;
    }

    $excludeId = $isEdit ? (int)$_GET["id"] : null;
    $errors = validateParkingSpotData([
        'number' => $number,
        'type' => $type
    ], $pdo, $excludeId);

    if (empty($errors)) {
        try {
            if ($isEdit) {
                $spotId = (int)$_GET["id"];
                updateParkingSpot($pdo, $spotId, $number, $type, $isOccupied);
            } else {
                createParkingSpot($pdo, $number, $type, 0);
            }

            header("Location: index.php?component=parking_spots");
            exit();
        } catch (Exception $e) {
            $errors[] = 'Une erreur est survenue lors de l\'enregistrement';
        }
    }
}

require "View/parking_spot.php";