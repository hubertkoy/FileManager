<?php
require_once 'utilities/Session.php';
$loggedIn = Session::getInstance()->isAuthorized();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Title</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php
require_once '_nav.php';
?>
<div class="container">
<div id="message" class="m-3 alert alert-success" hidden></div>
</div>
<div id="main" class="container">