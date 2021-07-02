<?php
require_once 'utilities/Session.php';
$loggedIn = Session::getInstance()->isAuthorized();
?>
<div class="container mt-5 mb-5">
    <div class="row d-flex align-items-center justify-content-center">
        <div class="col-md-6">
            <div class="card px-5 py-5">
                <?php if (!$loggedIn) { ?>
                    <h1 class="h3 mb-3 fw-normal">Login</h1>
                    <form method="post" action="/api/login" id="login-form">
                        <div class="form-input"><i class="fa fa-user"></i>
                            <input type="text" name="username" class="form-control" placeholder="User name" required>
                        </div>
                        <div class="form-input"><i class="fa fa-lock"></i>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" id="login-submit" class="btn btn-primary mt-4 signup">Login</button>
                        <div class="text-center mt-4"><span>Don't have account yet?</span>
                            <a href="/register" class="text-decoration-none">Register</a>
                        </div>
                    </form>
                    <script>
                        const loginForm = document.getElementById('login-form');
                        const loginButton = document.getElementById('login-submit');
                        let responseStatus;
                        loginButton.addEventListener('click', e => {
                            e.preventDefault();
                            const username = loginForm.username.value;
                            const password = loginForm.password.value;
                            fetch('/api/login', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    username: username,
                                    password: password
                                })
                            }).then(response => {
                                responseStatus = response.status;
                                return response.json()
                            }).then(data => {
                                const messageBox = document.getElementById('message');
                                messageBox.innerText = data['message'];
                                messageBox.removeAttribute("hidden");
                                if (responseStatus !== 200) {
                                    messageBox.setAttribute("class", "m-3 alert alert-danger");
                                } else {
                                    messageBox.setAttribute("class", "m-3 alert alert-success");
                                    setTimeout(function () {
                                        window.location.href = '/';
                                    }, 5000);
                                    loginForm.innerHTML = "Web page redirects after 5 seconds.";
                                }
                            });
                        })
                    </script>
                <?php } else { ?>
                    <h5>You are already logged in!</h5>
                <?php } ?>
            </div>
        </div>
    </div>
</div>