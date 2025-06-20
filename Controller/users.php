<?php
/**
 * @var PDO $pdo
 */

require "Model/users.php";

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    $currentUserId = getCurrentUserId($pdo);

    if ($userId !== $currentUserId) {
        try {
            deleteUser($pdo, $userId);
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
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Vous ne pouvez pas supprimer votre propre compte']);
            exit();
        }
    }

    header("Location: index.php?component=users");
    exit();
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

$allowedSortColumns = ['id', 'first_name', 'last_name', 'email', 'phone_number', 'role', 'active'];
if (!in_array($sortBy, $allowedSortColumns)) {
    $sortBy = 'id';
}

$users = getUsers($pdo, $page, $sortBy, $sortOrder);

$totalUsers = getTotalUsers($pdo);
$totalPages = ceil($totalUsers / 10);

require "View/users.php";