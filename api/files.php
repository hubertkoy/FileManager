<?php

require_once 'utilities/Database.php';
require_once 'utilities/Exceptions.php';
require_once 'utilities/InputHelper.php';
require_once 'utilities/Session.php';

/**
 * @throws InternalServerErrorJsonException
 */
function api_file_upload(): array
{
    $session = Session::getInstance();
    $user_id = $session['id'];
    $filename = pathinfo($_POST['filename'], PATHINFO_FILENAME) . '.' . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $path = 'download/' . uuid_v4();
    $mimetype = mime_content_type($_FILES['file']['tmp_name']);
    $size = $_FILES['file']['size'];
    $db = Database::getInstance();
    $db->begin();
    $db->query('INSERT INTO files(user_id, name, path, mimetype, size, uploaded) VALUES(:user_id, :name, :path, :mimetype, :size, 1)',
        [':user_id' => $user_id, ':name' => $filename, ':path' => $path, ':mimetype' => $mimetype, ':size' => $size]);

    if (!move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
        throw new InternalServerErrorJsonException(['message' => 'No free space on the server.']);
    }

    $db->commit();
    return ['message' => 'File successfully uploaded.'];

}

/**
 * @throws NotFoundJsonException|InternalServerErrorJsonException
 */
function api_files_remove(int $id): array
{
    $db = Database::getInstance();

    #TODO future feature
    $access_for_all = false;

    $db->begin();
    if ($access_for_all) {
        $result = $db->query('SELECT path FROM files WHERE id = :id', [':id' => $id]);
    } else {
        $session = Session::getInstance();
        $user_id = $session['id'];

        $result = $db->query('SELECT path FROM files WHERE id = :id AND user_id = :user_id', [':id' => $id, ':user_id' => $user_id]);
    }
    if(!$result) {
        throw new NotFoundJsonException(['message'=>'File not found.']);
    }

    $row = $result->fetch();
    $path = $row[0];
    if (!unlink($path)) {
        throw new InternalServerErrorJsonException(['message'=>'Storage error.']);
    }

    $db->query('DELETE FROM files WHERE id = :id', [':id' => $id]);
    $db->commit();
    return ['message'=>'File removed successfully.'];
}

/**
 * @throws NotFoundJsonException
 * @throws InternalServerErrorJsonException
 */
function api_files_get(int $id): void
{
    $db = Database::getInstance();

    #TODO future feature
    $access_for_all = false;

    if ($access_for_all) {
        $result = $db->query('SELECT path, size, mimetype, name FROM files WHERE id = :id', [':id' => $id]);
    } else {
        $session = Session::getInstance();
        $user_id = $session['id'];
        $result = $db->query('SELECT path, size, mimetype, name FROM files WHERE id = :id AND user_id = :user_id', [':id' => $id, ':user_id' => $user_id]);
    }

    if (!$result) {
        throw new NotFoundJsonException(['message' => 'File not found.']);
    }

    $row = $result->fetch();
    $path = $row[0];
    $size = $row[1];
    $mimetype = $row[2];
    $filename = $row[3];

    if (!file_exists($path)) {
        throw new InternalServerErrorJsonException(['message' => 'File lost.']);
    }

    header("Content-type: $mimetype");
    header("Content-length: $size");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    readfile($path);
}

function api_files_list(): array
{
    $db = Database::getInstance();

    #TODO future feature
    $list_all = false;

    if ($list_all) {
        $result = $db->query('SELECT f.id, u.username, f.name, f.mimetype, f.size, f.uploaded, f.created_at FROM files AS f JOIN users AS u ON u.id = f.user_id');
    } else {
        $session = Session::getInstance();
        $user_id = $session['id'];
        $result = $db->query('SELECT f.id, u.username, f.name, f.mimetype, f.size, f.uploaded, f.created_at FROM files AS f JOIN users AS u ON u.id = f.user_id WHERE f.user_id = :user_id', [':user_id' => $user_id]);
    }
    $files = [];
    if($result) {
        foreach ($result->fetchAll() as $row) {
            $files[] = [
                'id' => $row[0],
                'username' => $row[1],
                'filename' => $row[2],
                'mimetype' => $row[3],
                'size' => $row[4],
                'uploaded' => $row[5],
                'created_at' => $row[6]
            ];
        }
    }
    return ['files' => $files];
}