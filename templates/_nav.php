<?php
require_once 'utilities/Session.php';
$loggedIn = Session::getInstance()->isAuthorized();

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="Eighth navbar example">
    <div class="container">
        <a class="navbar-brand" href="/">Demo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar"
                aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <button class="btn btn-secondary mx-1 <?php if ($_SERVER['REQUEST_URI'] == '/') echo ' active'; ?>"
                            onclick="window.location.href='/';">Home
                    </button>
                </li>
                <?php if (!$loggedIn) { ?>
                    <li class="nav-item">
                        <button class="btn btn-secondary mx-1 <?php if ($_SERVER['REQUEST_URI'] == '/login') echo ' active'; ?>"
                                onclick="window.location.href='/login';">Login
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-secondary mx-1 <?php if ($_SERVER['REQUEST_URI'] == '/register') echo ' active'; ?>"
                                onclick="window.location.href='/register';">Register
                        </button>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <button class="btn btn-secondary mx-1 <?php if ($_SERVER['REQUEST_URI'] == '/files') echo ' active'; ?>"
                                onclick="window.location.href='/files';">Files
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-secondary mx-1" id="logout-button">Logout</button>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>