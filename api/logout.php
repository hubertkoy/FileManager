<?php

require_once 'utilities/Session.php';

function api_logout(): array
{
    Session::getInstance()->destroy();
    return ['message'=>'Logged out successfully.'];
}