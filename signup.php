<?php
require 'config.php';

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);
    $cpass = trim($_POST['confirm_password']);

    if(empty($name) || empty($email) || empty($pass) || empty($cpass)) $errors[] = "All fields required";
    if($pass !== $cpass) $errors[] = "Passwords do not match";

    if(empty($errors)) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO stations (name,email,password) VALUES (?,?,?)");
        $stmt->bind_param("sss", $name, $email, $hash);
        if($stmt->execute()){
            $_SESSION['station_id'] = $stmt->insert_id;
            $_SESSION['station_name'] = $name;
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Email already registered";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="card mx-auto shadow" style="max-width:400px;">
<div class="card-header bg-dark text-white">Sign Up</div>
<div class="card-body">

<?php if($errors): ?>
    <div class="alert alert-danger">
        <?php foreach($errors as $err) echo $err."<br>"; ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button class="btn btn-success w-100">Sign Up</button>
</form>

<div class="mt-3 text-center">
    Already have an account? <a href="login.php">Login</a>
</div>

</div>
</div>
</div>

</body>
</html>
