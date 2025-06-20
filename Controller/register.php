
<?php
/**
 * @var PDO $pdo
 */

require 'Model/register.php';

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');

    if (!empty($_POST['firstName']) && !empty($_POST['lastName']) &&
        !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirmPassword'])) {

        $firstName = cleanString($_POST['firstName']);
        $lastName = cleanString($_POST['lastName']);
        $email = cleanString($_POST['email']);
        $phoneNumber = cleanString($_POST['phoneNumber'] ?? '');
        $password = cleanString($_POST['password']);
        $confirmPassword = cleanString($_POST['confirmPassword']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['errors' => ['Veuillez entrer une adresse email valide']]);
            exit();
        }
        if (!empty($phoneNumber)) {
            $phoneNumber = preg_replace('/[\s\-\.]/', '', $phoneNumber);
            if (!preg_match('/^\+?[0-9]{8,15}$/', $phoneNumber)) {
                echo json_encode(['errors' => ['Le numéro de téléphone n\'est pas valide (8 à 15 chiffres)']]);
                exit();
            }
        }

        if ($password !== $confirmPassword) {
            echo json_encode(['errors' => ['Les mots de passe ne correspondent pas']]);
            exit();
        }

        if (strlen($password) < 8) {
            echo json_encode(['errors' => ['Le mot de passe doit contenir au moins 8 caractères']]);
            exit();
        }

        $result = createUser($pdo, $firstName, $lastName, $email, $password, $phoneNumber);

        if ($result['success']) {
            echo json_encode(['registration' => true]);
            exit();
        } else {
            echo json_encode(['errors' => [$result['error']]]);
            exit();
        }
    } else {
        echo json_encode(['errors' => ['Veuillez remplir tous les champs obligatoires']]);
        exit();
    }
} else {
    require 'View/register.php';
}