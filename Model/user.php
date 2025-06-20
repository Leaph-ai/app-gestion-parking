
<?php
/**
 * @var PDO $pdo
 */

require "Model/users.php";

$errors = [];
$user = null;
$isEdit = false;
$isProfile = false;
$canManageUsers = isAdmin();

if (!canAccessComponent('user')) {
    header("Location: index.php");
    exit();
}

if (isset($_GET["action"]) && $_GET["action"] === "delete-profile") {
    $userId = getCurrentUserId($pdo);

    if ($userId) {
        try {
            deleteUser($pdo, $userId);
            $_SESSION = array();
            session_destroy();
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            $errors[] = 'Une erreur est survenue lors de la suppression du compte';
        }
    }
}

if (isset($_GET["action"]) && $_GET["action"] === "edit" && isset($_GET["id"])) {
    $isEdit = true;
    $userId = (int)$_GET["id"];
    $currentUserId = getCurrentUserId($pdo);

    if ($canManageUsers) {
        if ($userId === $currentUserId) {
            header("Location: index.php?component=users");
            exit();
        }
    } else {
        if ($userId !== $currentUserId) {
            header("Location: index.php");
            exit();
        }
        $isProfile = true;
    }

    $user = getUserById($pdo, $userId);
    if (!$user) {
        $redirectUrl = $canManageUsers ? "index.php?component=users" : "index.php";
        header("Location: $redirectUrl");
        exit();
    }
}

if (isset($_GET["action"]) && $_GET["action"] === "create") {
    if (!$canManageUsers) {
        header("Location: index.php");
        exit();
    }
}

if (isset($_GET["action"]) && $_GET["action"] === "profile") {
    $isEdit = true;
    $isProfile = true;
    $userId = getCurrentUserId($pdo);
    $user = getUserById($pdo, $userId);
    if (!$user) {
        header("Location: index.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = cleanString($_POST['first_name'] ?? '');
    $lastName = cleanString($_POST['last_name'] ?? '');
    $email = cleanString($_POST['email'] ?? '');
    $phoneNumber = cleanString($_POST['phone_number'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    $role = 1;
    $active = 1;
    if ($canManageUsers && !$isProfile) {
        $role = isset($_POST['role']) ? (int)$_POST['role'] : 1;
        $active = isset($_POST['active']) ? 1 : 0; // Checkbox cochée = 1, non cochée = 0
    }

    // Validation
    if (empty($firstName)) {
        $errors[] = 'Le prénom est requis';
    }

    if (empty($lastName)) {
        $errors[] = 'Le nom est requis';
    }

    if (empty($email)) {
        $errors[] = 'L\'email est requis';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'email n\'est pas valide';
    }

    if (!empty($phoneNumber)) {
        $phoneNumber = preg_replace('/[\s\-\.]/', '', $phoneNumber);
        if (!preg_match('/^\+?[0-9]{8,15}$/', $phoneNumber)) {
            $errors[] = 'Le numéro de téléphone n\'est pas valide (8 à 15 chiffres)';
        }
    }

    if ($isProfile) {
        if (empty($currentPassword)) {
            $errors[] = 'Votre mot de passe actuel est requis pour modifier votre profil';
        } else {
            $userId = getCurrentUserId($pdo);
            if (!verifyCurrentPassword($pdo, $userId, $currentPassword)) {
                $errors[] = 'Le mot de passe actuel est incorrect';
            }
        }

        if (!empty($newPassword) && strlen($newPassword) < 8) {
            $errors[] = 'Le nouveau mot de passe doit contenir au moins 8 caractères';
        }
    } else {
        if (!$isEdit && empty($newPassword)) {
            $errors[] = 'Le mot de passe est requis';
        }

        if (!empty($newPassword) && strlen($newPassword) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
        }
    }

    $excludeId = $isEdit ? ($isProfile ? getCurrentUserId($pdo) : (int)$_GET["id"]) : null;
    if (emailExists($pdo, $email, $excludeId)) {
        $errors[] = 'Cet email est déjà utilisé';
    }

    if (empty($errors)) {
        try {
            if ($isEdit) {
                $userId = $isProfile ? getCurrentUserId($pdo) : (int)$_GET["id"];

                if ($isProfile) {
                    $role = $user['role'];
                    $active = $user['active'];
                }

                $passwordToUse = $isProfile ? $newPassword : $newPassword;

                if (!empty($passwordToUse)) {
                    updateUserWithPassword($pdo, $userId, $firstName, $lastName, $email, $phoneNumber, $passwordToUse, $role, $active);
                } else {
                    updateUser($pdo, $userId, $firstName, $lastName, $email, $phoneNumber, $role, $active);
                }

                $successMessage = $isProfile ? 'Votre profil a été mis à jour avec succès' : 'Utilisateur modifié avec succès';
            } else {
                createUser($pdo, $firstName, $lastName, $email, $phoneNumber, $newPassword, $role, $active);
                $successMessage = 'Utilisateur créé avec succès';
            }

            $redirectUrl = $isProfile ? "index.php?component=user&action=profile&success=" . urlencode($successMessage) :
                "index.php?component=users&success=" . urlencode($successMessage);
            header("Location: $redirectUrl");
            exit();

        } catch (Exception $e) {
            $errors[] = 'Une erreur est survenue lors de l\'enregistrement';
        }
    }
}

require "View/user.php";