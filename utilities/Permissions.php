<?php

require_once 'utilities/Exceptions.php';
require_once 'utilities/Session.php';

/**
 * @throws ForbiddenJsonException
 */
function isAuthorized(): void
{
    $session = Session::getInstance();
    if (!$session->isAuthorized()) {
        throw new ForbiddenJsonException(['message' => 'You are already logged out!']);
    }
}

/**
 * @throws ForbiddenJsonException
 */
function isNotAuthorized(): void
{
    $session = Session::getInstance();
    if ($session->isAuthorized()) {
        throw new ForbiddenJsonException(['message' => 'You are already logged in!']);
    }
}
/* TODO future feature
function isAdmin(): void
{

}
*/