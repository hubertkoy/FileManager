<?php

require_once 'utilities/Database.php';
require_once 'utilities/Exceptions.php';
require_once 'utilities/InputHelper.php';
require_once 'utilities/Session.php';

/**
 * @throws BadRequestJsonException
 * @throws UnauthorizedJsonException
 */
function api_login(): array
{
    prepare_post_data('application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? throw new BadRequestJsonException(['message' => 'Username not found.']);
    $password = $data['password'] ?? throw new BadRequestJsonException(['message' => 'Password not found.']);

    $db = Database::getInstance();

    $result = $db->query('SELECT id, password FROM users WHERE username = :username', [':username' => $username]);

    if ($result) {
        $row = $result->fetch();
        $userid = intval($row[0]);
        $dbpassword = $row[1];
        if (!password_verify($password, $dbpassword)) {
            throw new UnauthorizedJsonException(['message' => 'Invalid credentials.']);
        }
    } else {
        throw new UnauthorizedJsonException(['message' => 'Invalid credentials.']);
    }

    $session = Session::getInstance();
    $session['id'] = $userid;

    return ['message' => 'Successfully logged in.', 'id' => $userid];
}