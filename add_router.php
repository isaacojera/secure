<?php 
//add_router.php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Router</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white">Add MikroTik Router</div>
        <div class="card-body">

            <form id="routerForm">
                <div class="mb-3">
                    <label>Hotspot Name</label>
                    <input type="text" name="router_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>VPN IP</label>
                    <input type="text" name="vpn_ip" class="form-control" placeholder="10.0.1.2"required>
                </div>

                <div class="mb-3">
                    <label>API Username</label>
                    <input type="text" name="api_username" class="form-control" placeholder="admin"required>
                </div>

                <div class="mb-3">
                    <label>API Password</label>
                    <input type="password" name="api_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" placeholder="Eg Kireka" required>
                </div>

                <button type="button" onclick="testRouter()" class="btn btn-warning">Test Connection</button>
                <button type="submit" class="btn btn-success">Save Router</button>
            </form>

            <div id="result" class="mt-3"></div>

        </div>
    </div>
</div>

<script>
function testRouter(){
    let formData = new FormData(document.getElementById('routerForm'));
    fetch('test_router.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json())
      .then(data => {
        document.getElementById('result').innerHTML =
            data.status === 'online'
            ? '<div class="alert alert-success">Router Online</div>'
            : '<div class="alert alert-danger">Router Offline</div>';
      });
}

document.getElementById('routerForm').addEventListener('submit', function(e){
    e.preventDefault();
    let formData = new FormData(this);

    fetch('save_router.php', {
        method: 'POST',
        body: formData
    }).then(res => res.text())
      .then(data => {
        document.getElementById('result').innerHTML =
            '<div class="alert alert-info">'+data+'</div>';
      });
});
</script>

</body>
</html>
