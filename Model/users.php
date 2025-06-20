
<?php

function getUsers($pdo, $page, $sortBy = 'id', $sortOrder = 'asc') {
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $allowedColumns = ['id', 'first_name', 'last_name', 'email', 'phone_number', 'role', 'active'];
    $allowedOrders = ['asc', 'desc'];

    if (!in_array($sortBy, $allowedColumns)) {
        $sortBy = 'id';
    }
    if (!in_array($sortOrder, $allowedOrders)) {
        $sortOrder = 'asc';
    }

    $query = "SELECT id, first_name, last_name, email, phone_number, role, active FROM users ORDER BY {$sortBy} {$sortOrder} LIMIT :limit OFFSET :offset";
    $res = $pdo->prepare($query);
    $res->bindParam(':limit', $limit, PDO::PARAM_INT);
    $res->bindParam(':offset', $offset, PDO::PARAM_INT);
    $res->execute();
    return $res->fetchAll();
}

function getTotalUsers(PDO $pdo): int {
    $query = "SELECT COUNT(*) as total FROM users";
    $res = $pdo->query($query);
    $result = $res->fetch(PDO::FETCH_ASSOC);
    return (int)$result['total'];
}

function getUserById(PDO $pdo, int $id): array|false {
    $query = "SELECT id, first_name, last_name, email, phone_number, role, active FROM users WHERE id = :id";
    $res = $pdo->prepare($query);
    $res->bindParam(':id', $id);
    $res->execute();
    return $res->fetch(PDO::FETCH_ASSOC);
}

function createUser(PDO $pdo, string $firstName, string $lastName, string $email, string $phoneNumber, string $password, int $role = 1, int $active = 1): void {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (first_name, last_name, email, phone_number, password, role, active) VALUES (:first_name, :last_name, :email, :phone_number, :password, :role, :active)";
    $res = $pdo->prepare($query);
    $res->bindParam(':first_name', $firstName);
    $res->bindParam(':last_name', $lastName);
    $res->bindParam(':email', $email);
    $res->bindParam(':phone_number', $phoneNumber);
    $res->bindParam(':password', $hashedPassword);
    $res->bindParam(':role', $role);
    $res->bindParam(':active', $active);
    $res->execute();
}

function updateUser(PDO $pdo, int $id, string $firstName, string $lastName, string $email, string $phoneNumber, int $role, int $active): void {
    $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone_number = :phone_number, role = :role, active = :active WHERE id = :id";
    $res = $pdo->prepare($query);
    $res->bindParam(':id', $id);
    $res->bindParam(':first_name', $firstName);
    $res->bindParam(':last_name', $lastName);
    $res->bindParam(':email', $email);
    $res->bindParam(':phone_number', $phoneNumber);
    $res->bindParam(':role', $role);
    $res->bindParam(':active', $active);
    $res->execute();
}

function updateUserWithPassword(PDO $pdo, int $id, string $firstName, string $lastName, string $email, string $phoneNumber, string $password, int $role, int $active): void {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone_number = :phone_number, password = :password, role = :role, active = :active WHERE id = :id";
    $res = $pdo->prepare($query);
    $res->bindParam(':id', $id);
    $res->bindParam(':first_name', $firstName);
    $res->bindParam(':last_name', $lastName);
    $res->bindParam(':email', $email);
    $res->bindParam(':phone_number', $phoneNumber);
    $res->bindParam(':password', $hashedPassword);
    $res->bindParam(':role', $role);
    $res->bindParam(':active', $active);
    $res->execute();
}

function deleteUser(PDO $pdo, int $id): void {
    $query = "DELETE FROM users WHERE id = :id";
    $res = $pdo->prepare($query);
    $res->bindParam(':id', $id);
    $res->execute();
}

function emailExists(PDO $pdo, string $email, int $excludeId = null): bool {
    if ($excludeId) {
        $query = "SELECT id FROM users WHERE email = :email AND id != :exclude_id";
        $res = $pdo->prepare($query);
        $res->bindParam(':email', $email);
        $res->bindParam(':exclude_id', $excludeId);
    } else {
        $query = "SELECT id FROM users WHERE email = :email";
        $res = $pdo->prepare($query);
        $res->bindParam(':email', $email);
    }
    $res->execute();
    return $res->fetch() ? true : false;
}

function getCurrentUserId(PDO $pdo): int|null {
    if (!isset($_SESSION['username'])) {
        return null;
    }

    $fullName = $_SESSION['username'];
    $query = "SELECT id FROM users WHERE CONCAT(first_name, ' ', last_name) = :full_name";
    $res = $pdo->prepare($query);
    $res->bindParam(':full_name', $fullName);
    $res->execute();
    $result = $res->fetch(PDO::FETCH_ASSOC);
    return $result ? (int)$result['id'] : null;
}

/**
 * Vérifie le mot de passe actuel d'un utilisateur
 */
function verifyCurrentPassword(PDO $pdo, int $userId, string $password): bool {
    $query = "SELECT password FROM users WHERE id = :id";
    $res = $pdo->prepare($query);
    $res->bindParam(':id', $userId);
    $res->execute();
    $user = $res->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        return password_verify($password, $user['password']);
    }

    return false;
}