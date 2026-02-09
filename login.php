<?php
require 'config.php';

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM stations WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if($user && password_verify($pass, $user['password'])){
        $_SESSION['station_id'] = $user['id'];
        $_SESSION['station_name'] = $user['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $errors[] = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="card mx-auto shadow" style="max-width:400px;">
<div class="card-header bg-dark text-white">Login</div>
<div class="card-body">

<?php if($errors): ?>
    <div class="alert alert-danger">
        <?php foreach($errors as $err) echo $err."<br>"; ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100">Login</button>
</form>

<div class="mt-3 text-center">
    Don't have an account? <a href="signup.php">Sign Up</a>
</div>

</div>
</div>
</div>

</body>
</html>
