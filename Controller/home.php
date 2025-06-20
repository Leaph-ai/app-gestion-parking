<?php
/**
 * @var PDO $pdo
 * Contrôleur pour la page d'accueil
 */


if (!isAuthenticated()) {
    header("Location: index.php?component=login");
    exit();
}

require_once "Model/home.php";


$username = $_SESSION['username'] ?? 'Utilisateur';
$userRole = $_SESSION['role'] ?? 1;
$isAdmin = isAdmin();


$pageTitle = 'Accueil - SymParking';
$welcomeMessage = "Bienvenue sur SymParking";

$dashboardData = getDashboardStats($pdo);

$userStats = [];
if (!$isAdmin) {
    $currentUserId = null;

    if (isset($_SESSION['user_id'])) {
        $currentUserId = (int)$_SESSION['user_id'];
    } else if (isset($_SESSION['username'])) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE CONCAT(first_name, ' ', last_name) = ?");
            $stmt->execute([$_SESSION['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $currentUserId = (int)$user['id'];
                $_SESSION['user_id'] = $currentUserId;
            }
        } catch (Exception $e) {
            error_log("Erreur récupération user ID: " . $e->getMessage());
        }
    }

    if ($currentUserId) {
        $userStats = getUserStats($pdo, $currentUserId);
    }
}

require "View/home.php";