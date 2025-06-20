<?php
function getUser(PDO $pdo, string $email)
{
    $query = 'SELECT * FROM `users` WHERE email = :email';

    $res = $pdo->prepare($query);
    $res->bindParam(':email', $email);
    $res->execute();
    return $res->fetch();
}