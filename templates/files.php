<?php
require_once 'utilities/Exceptions.php';
require_once 'utilities/Session.php';
$loggedIn = Session::getInstance()->isAuthorized();
if ($loggedIn) {
    ?>
    <div class="container mt-5 mb-5">
        <div class="row d-flex align-items-center justify-content-center">
            <div class="col-md-6">
                <div class="card px-5 py-5">
                    <h1 class="h3 mb-3 fw-normal">File upload</h1>
                    <form method="post" action="/api/files" id="file-form">
                        <div class="mb-3">
                            <label for="file-name" class="form-label">File name:</label>
                            <input class="form-control" name="filename" type="text" id="file-name">
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">Select file to upload:</label>
                            <input class="form-control" name="file" type="file" id="file">
                        </div>
                        <button class="btn btn-primary signup" id="file-submit">Upload file</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">No.</th>
                <th scope="col">Name</th>
                <th scope="col">Owner</th>
                <th scope="col">Type</th>
                <th scope="col">Size</th>
                <th scope="col">Uploaded</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody id="files-list">
            </tbody>
        </table>
    </div>
    <script>
        const fileForm = document.getElementById('file-form');
        const filesTable = document.getElementById('files-list');
        const uploadButton = document.getElementById('file-submit');
        const messageBox = document.getElementById('message');
        let status;

        uploadButton.addEventListener('click', e => {
            e.preventDefault();

            const filename = fileForm.filename.value;
            const file = fileForm.file.files[0];
            let formData = new FormData();

            formData.append("filename", filename);
            formData.append("file", file);

            fetch('/api/files', {
                method: 'POST',
                body: formData
            }).then(response => {
                status = response.status;
                return response.json();
            }).then(data => {
                messageBox.removeAttribute("hidden");
                messageBox.innerText = data['message'];
                if (status !== 200) {
                    messageBox.setAttribute("class", "m-3 alert alert-danger");
                } else {
                    messageBox.setAttribute("class", "m-3 alert alert-success");
                }
                fileForm.filename.value = null;
                fileForm.file.value = null;
            })
        });

        const removeFile = (id) => {
            fetch(`/api/files/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                status = response.status;
                return response.json()
            }).then(data => {
                messageBox.removeAttribute("hidden");
                messageBox.innerText = data['message'];
                if (status !== 200) {
                    messageBox.setAttribute("class", "m-3 alert alert-danger");
                } else {
                    messageBox.setAttribute("class", "m-3 alert alert-success");
                }
            })
        }

        fetch('/api/files').then(response => {
            status = response.status;
            return response.json();
        }).then(data => {
            data = data['files'];
            const test = 'else';
            if (status === 200) {
                for (let i = 0; i < data.length; i++) {
                    filesTable.innerHTML += `<tr><td>${i + 1}</td><td><a href="/api/files/${data[i]['id']}" class="link-info">${data[i]['filename']}</a> </td><td>${data[i]['username']}</td><td>${data[i]['mimetype']}</td><td>${data[i]['size']} kB</td><td>${data[i]['created_at']}</td><td><button class="btn btn-danger" onclick="removeFile(` + data[i]['id'] + `)"><i class="fa fa-remove"></td></tr>`;
                }
            }
        })
    </script>
<?php } else {
    throw new UnauthorizedException();
} ?>