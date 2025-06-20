<?php
/**
 * @var PDO $pdo
 */

require "Model/parking_spots.php";

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $spotId = (int)$_GET['id'];

    try {
        deleteParkingSpot($pdo, $spotId);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit();
        }
    } catch (Exception $e) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erreur lors de la suppression']);
            exit();
        }
    }

    header("Location: index.php?component=parking_spots");
    exit();
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

$allowedSortColumns = ['id', 'number', 'type', 'is_occupied'];
if (!in_array($sortBy, $allowedSortColumns)) {
    $sortBy = 'id';
}

$parkingSpots = getParkingSpots($pdo, $page, $sortBy, $sortOrder);


$totalSpots = getTotalParkingSpots($pdo);
$totalPages = ceil($totalSpots / 10);

require "View/parking_spots.php";