<?php
function createUser(PDO $pdo, string $firstName, string $lastName, string $email, string $password, string $phoneNumber = '')
{
    $checkQuery = 'SELECT * FROM `users` WHERE email = :email';
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        return ['success' => false, 'error' => 'Cet email est déjà utilisé'];
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = 'INSERT INTO `users` (first_name, last_name, email, phone_number, password) 
              VALUES (:firstName, :lastName, :email, :phoneNumber, :password)';

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phoneNumber', $phoneNumber);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();

        return ['success' => true, 'userId' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Erreur lors de la création du compte: ' . $e->getMessage()];
    }
}