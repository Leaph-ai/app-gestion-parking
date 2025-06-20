<?php

if (!function_exists('cleanString')) {
    function cleanString(string $value): string
    {
        return trim(htmlspecialchars($value, ENT_QUOTES));
    }
}

if (!function_exists('canAccessComponent')) {
    function canAccessComponent(string $componentName, ?int $userRole = null): bool {
        $publicComponents = ['login', 'register', 'pricing'];

        $userComponents = ['dashboard', 'booking', 'pricing'];

        $adminComponents = ['users', 'parking_spots'];

        $specialComponents = ['user', 'parking_spot'];

        if (in_array($componentName, $publicComponents)) {
            return true;
        }

        if (!isset($_SESSION['auth']) || !$_SESSION['auth']) {
            return false;
        }

        $userRole = $userRole ?? ($_SESSION['role'] ?? 1);

        if (in_array($componentName, $adminComponents)) {
            return $userRole === 2;
        }

        if (in_array($componentName, $userComponents)) {
            return $userRole >= 1;
        }

        if (in_array($componentName, $specialComponents)) {
            return handleSpecialComponent($componentName, $userRole);
        }

        return false;
    }
}

if (!function_exists('handleSpecialComponent')) {
    function handleSpecialComponent(string $componentName, int $userRole): bool {
        switch ($componentName) {
            case 'user':
                $action = $_GET['action'] ?? '';
                if (in_array($action, ['profile', 'delete-profile'])) {
                    return true;
                }
                return $userRole === 2;

            case 'parking_spot':
                return $userRole === 2;

            default:
                return false;
        }
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool {
        return isset($_SESSION['auth']) && $_SESSION['auth'] && ($_SESSION['role'] ?? 1) === 2;
    }
}

function authenticateUser(PDO $pdo, string $email, string $password): array {
    try {
        $query = "SELECT id, first_name, last_name, email, password, role, active FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
        }

        if ($user['active'] != 1) {
            return ['success' => false, 'message' => 'Votre compte a été désactivé. Contactez l\'administrateur.'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
        }

        $_SESSION['auth'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['active'] = $user['active']; // Stocker le statut actif en session

        return ['success' => true, 'message' => 'Connexion réussie'];

    } catch (Exception $e) {
        error_log("Erreur d'authentification: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur technique lors de la connexion'];
    }
}

function isAuthenticated(): bool {
    return isset($_SESSION['auth']) && $_SESSION['auth'] === true && isset($_SESSION['active']) && $_SESSION['active'] == 1;
}


if (!function_exists('denyAccess')) {
    function denyAccess(string $message = "Vous n'avez pas les permissions nécessaires pour accéder à cette page."): void {
        echo '<div class="access-denied">';
        echo '<h1><i class="fas fa-exclamation-triangle"></i> Accès refusé</h1>';
        echo '<p>' . htmlspecialchars($message) . '</p>';
        echo '<a href="index.php" class="btn btn-primary">Retour à l\'accueil</a>';
        echo '</div>';
    }
}

if (!function_exists('getCurrentUserRole')) {
    function getCurrentUserRole(): int {
        return $_SESSION['role'] ?? 1;
    }
}

