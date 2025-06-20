<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require_once "Includes/database.php";
require_once "Includes/functions.php";

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Traitement des requêtes AJAX
    if (isset($_GET['component'])) {
        $componentName = cleanString($_GET['component']);

        if (file_exists("Controller/$componentName.php")) {
            require "Controller/$componentName.php";
            exit();
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['error' => 'Component not found']);
    exit();
}

if (isAuthenticated() && isset($_SESSION['user_id'])) {
    try {
        $query = "SELECT active FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || $user['active'] != 1) {
            $_SESSION = array();
            session_destroy();
            header("Location: index.php?error=account_disabled");
            exit();
        }

        $_SESSION['active'] = $user['active'];

    } catch (Exception $e) {
        error_log("Erreur vérification utilisateur actif: " . $e->getMessage());
    }
}

if (isset($_GET['disconnect']) && $_GET['disconnect'] == 'true') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php");
    exit();
}

$currentComponent = $_GET['component'] ?? '';
$bodyClass = '';

if (isset($_SESSION['auth']) && $_SESSION['auth']) {
    $bodyClass = 'with-navbar';
} else {
    if ($currentComponent === 'login' || $currentComponent === '' || $currentComponent === 'register') {
        $bodyClass = 'login-page';
    } else {
        $bodyClass = 'with-navbar';
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SymParking</title>
    <link rel="stylesheet" href="assets/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="<?= $bodyClass ?>">
<div class="container">
    <?php
    require '_partials/navbar.php';

    if (isAuthenticated()) {
        if (isset($_GET['component'])) {
            $componentName = cleanString($_GET['component']);

            if (canAccessComponent($componentName)) {
                if (file_exists("Controller/$componentName.php")) {
                    require "Controller/$componentName.php";
                } else {
                    if (isAdmin()) {
                        header("Location: index.php?component=users");
                        exit();
                    } else {
                        require "Controller/home.php";
                    }
                }
            } else {
                denyAccess();
            }
        } else {
            require "Controller/home.php";
        }
    } else {
        if (isset($_GET['component'])) {
            $componentName = cleanString($_GET['component']);

            if (canAccessComponent($componentName)) {
                if (file_exists("Controller/$componentName.php")) {
                    require "Controller/$componentName.php";
                } else {
                    require "Controller/login.php";
                }
            } else {
                require "Controller/login.php";
            }
        } else {
            require "Controller/login.php";
        }
    }
    ?>
</div>

</body>
</html>
</html>