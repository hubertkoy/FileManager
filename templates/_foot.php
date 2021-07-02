<?php
require_once 'utilities/Session.php';
$loggedIn = Session::getInstance()->isAuthorized();
?>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
<?php if ($loggedIn) { ?>
<script>
const logoutButton = document.getElementById('logout-button');
let responseStatus;
logoutButton.addEventListener('click', e => {
    e.preventDefault();
    fetch('/api/logout', {
        method: 'POST'
    }).then(response => {
        responseStatus = response.status;
        return response.json();
    }).then(data => {
        console.log(data, responseStatus);
        if (responseStatus === 200) {
            const container = document.getElementById('main');
            const messageBox = document.getElementById('message');
            messageBox.setAttribute("class", "m-3 alert alert-success");
            messageBox.innerText = data['message'];
            messageBox.removeAttribute("hidden");
            setTimeout(function(){
                window.location.href = '/';
            }, 5000);
            container.innerHTML = "Web page redirects after 5 seconds.";
        }
    });
})
</script>
<?php } ?>
</body>
</html>