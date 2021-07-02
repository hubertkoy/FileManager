<?php
require_once 'utilities/Session.php';
$loggedIn = Session::getInstance()->isAuthorized();
?>
<div class="container mt-5 mb-5">
    <div class="row d-flex align-items-center justify-content-center">
        <div class="col-md-6">
            <div class="card px-5 py-5">
                <?php if (!$loggedIn) { ?>
                    <h1 class="h3 mb-3 fw-normal">Registration</h1>
                    <form action="/api/register" method="post" id="register-form">
                        <div class="form-input">
                            <i class="fa fa-envelope"></i>
                            <input type="email" name="email" class="form-control" placeholder="Email address">
                        </div>
                        <div class="form-input"><i class="fa fa-user"></i>
                            <input type="text" name="username" class="form-control" placeholder="User name">
                        </div>
                        <div class="form-input"><i class="fa fa-lock"></i>
                            <input type="password" name="passwd" class="form-control" placeholder="Password">
                        </div>
                        <div class="form-input"><i class="fa fa-lock"></i>
                            <input type="password" name="repasswd" class="form-control" placeholder="Repeat password">
                        </div>
                        <button type="submit" id="register-submit" class="btn btn-primary mt-4 signup">Register</button>
                        <div class="text-center mt-4"><span>Already registered?</span>
                            <a href="/login" class="text-decoration-none">Login</a>
                        </div>
                    </form>
                    <script>
                        const registerForm = document.getElementById('register-form');
                        const registerButton = document.getElementById('register-submit');
                        registerButton.addEventListener('click', e => {
                            e.preventDefault();
                            const email = registerForm.email.value;
                            const username = registerForm.username.value;
                            const passwd = registerForm.passwd.value;
                            const repasswd = registerForm.repasswd.value;
                            fetch('/api/register', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    email: email,
                                    username: username,
                                    passwd: passwd,
                                    repasswd: repasswd
                                })
                            }).then(response => response.json()).then(data => {
                                console.log(data)
                                const messageBox = document.getElementById('message');
                                messageBox.innerText = data['message'];
                                messageBox.removeAttribute("hidden");
                                if(data['error']) {
                                    messageBox.setAttribute("class","m-3 alert alert-danger");
                                } else {
                                    messageBox.setAttribute("class","m-3 alert alert-success");
                                    setTimeout(function(){
                                        window.location.href = '/login';
                                    }, 5000);
                                    registerForm.innerHTML = "Web page redirects after 5 seconds.";
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