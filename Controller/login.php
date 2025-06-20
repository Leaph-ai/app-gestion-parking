
<?php
/**
 * @var PDO $pdo
 */

require 'Model/login.php';

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');

    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = cleanString($_POST['email']);
        $password = cleanString($_POST['password']);

        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['errors' => ['Veuillez entrer une adresse email valide']]);
            exit();
        }

        $result = authenticateUser($pdo, $email, $password);

        if ($result['success']) {
            echo json_encode(['authentication' => true]);
        } else {
            echo json_encode(['errors' => [$result['message']]]);
        }
        exit();
    } else {
        echo json_encode(['errors' => ['Veuillez remplir tous les champs']]);
        exit();
    }
} else {
    require 'View/login.php';
}