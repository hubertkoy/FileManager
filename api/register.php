<?php

require_once 'utilities/Database.php';
require_once 'utilities/Exceptions.php';
require_once 'utilities/Session.php';
require_once 'utilities/InputHelper.php';

/**
 * @throws BadRequestJsonException
 * @throws ForbiddenJsonException
 * @throws ConflictJsonException
 */
function api_register(): array
{
    prepare_post_data('application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    $email = $data['email'] ?? throw new BadRequestJsonException(['message' => 'Email not found.']);
    $username = $data['username'] ?? throw new BadRequestJsonException(['message' => 'Username not found.']);
    $password = $data['passwd'] ?? throw new BadRequestJsonException(['message' => 'Password not found.']);
    $repassword = $data['repasswd'] ?? throw new BadRequestJsonException(['message' => 'Repassword not found.']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        throw new BadRequestJsonException(['message' => 'Invalid email.']);
    }
    if (filter_var($username, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/^[A-Za-z0-9_]{4,16}$/"]]) === false) {
        throw new BadRequestJsonException(['message' => 'Invalid username format.']);
    }
    if (strlen($password) < 5) {
        throw new BadRequestJsonException(['message' => 'Password too short.']);
    }
    if ($password !== $repassword) {
        throw new BadRequestJsonException(['message' => 'Passwords not match.']);
    }

    $db = Database::getInstance();
    $db->begin();

    $result = $db->query('SELECT 1 FROM users WHERE username = :username', [':username' => $username]);

    if ($result) {
        throw new ConflictJsonException(['message' => 'Username already taken.']);
    }

    $result = $db->query('SELECT 1 FROM users WHERE email = :email', [':email' => $email]);
    if ($result) {
        throw new ConflictJsonException(['message' => 'Email already in use.']);
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $db->query('INSERT INTO users (email, username, password, confirmed) VALUES(:email, :username, :password, :confirmed)',
        [':email' => $email, ':username' => $username, ':password' => $password, ':confirmed' => 1]);

    $db->commit();
    return ['message' => 'Account created successfully.'];
}
