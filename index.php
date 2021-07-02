<?php
$debug = false;
if ($debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
unset($debug);

require_once 'utilities/Database.php';
require_once 'utilities/Exceptions.php';
require_once 'utilities/Permissions.php';
require_once 'utilities/RoutingEntry.php';
require_once 'utilities/Session.php';

require_once 'api/files.php';
require_once 'api/login.php';
require_once 'api/logout.php';
require_once 'api/register.php';

$endpoint = $_SERVER['REQUEST_URI'];

$routings = [
    new RoutingEntry('POST', '/api/register', 'isNotAuthorized', 'api_register'),
    new RoutingEntry('POST', '/api/login', 'isNotAuthorized', 'api_login'),
    new RoutingEntry('POST', '/api/logout', 'isAuthorized', 'api_logout'),
    new RoutingEntry('POST', '/api/files', 'isAuthorized', 'api_file_upload'),
    new RoutingEntry('GET', '/api/files', 'isAuthorized', 'api_files_list'),
    new RoutingEntry('GET', '/api/files/%id', 'isAuthorized', 'api_files_get'),
    new RoutingEntry('DELETE', '/api/files/%id', 'isAuthorized', 'api_files_remove')
];
unset($endpoint);

try {
    try {
        Database::createInstance();
        Session::createInstance();

        $filename = $_SERVER['REQUEST_URI'];
        $pos = strpos($filename, '?');

        if (is_int($pos)) {
            $filename = substr($filename, 1, $pos - 1);
        } else {
            $filename = substr($filename, 1);
        }
        if ($filename == '') {
            $filename = 'home';
        } else {
            $length = strlen($filename);
            if ($filename[$length - 1] == '/')
                $filename = substr($filename, 0, -1);
        }
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && !str_contains($filename, '/') && file_exists("templates/$filename.php")) {
            require_once 'templates/_head.php';
            require_once "templates/$filename.php";
            require_once 'templates/_foot.php';
        } else {
            $routing_entry = null;
            foreach ($routings as $entry) {
                $matches = [];
                if ($entry->isValid($matches)) {
                    $routing_entry = $entry;
                    break;
                }
            }

            if ($routing_entry === null) {
                throw new NotFoundJsonException(['message' => 'Requested resource not found.']);
            }
            foreach ($routing_entry->getPermissions() as $permission) {
                $permission();
            }
            $controller = $routing_entry->getAction();
            $reflexion = new ReflectionFunction($controller);
            if($reflexion->getReturnType() == 'array') {
                $result = $controller(...$matches);
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                $controller(...$matches);
            }
        }
    } catch (TemporaryRedirectException $e) {
        header('Location: ' . $e->getLocation());
    } catch (ApiJsonException $e) {
        if (ini_get('display_errors') !== '1')
            header('Content-Type: application/json');
        throw $e;
    } catch (HttpException $e) {
        throw $e;
    } catch (Throwable $e) {
        if (ini_get('display_errors') === '1') {
            throw $e;
        } else {
            throw new InternalServerErrorException($e);
        }
    }
} catch (Throwable $e) {
    if (ini_get('display_errors') === '1') {
        $erIt = $e;
        while ($erIt) {
            echo 'Line: ' . $erIt->getLine() . '<br>Trace: ' . $erIt->getTraceAsString() . '<br>';
            echo 'Class:' . get_class($erIt) . '<br>';
            $erIt = $erIt->getPrevious();
        }
    }
    echo $e->getMessage();
    if ($e->getCode() > 0) {
        http_response_code($e->getCode());
    }
} finally {
    Session::destroyInstance();
    Database::destroyInstance();
}