<?php
require_once '../classes/User.php';
session_start();

// Auto-logout after 30 minutes of inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset(); session_destroy();
}
$_SESSION['last_activity'] = time();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = User::findByUsername($_POST['username']);
    if ($user && password_verify($_POST['password'], $user->password)) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['last_activity'] = time();
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login </title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="../assets/style.css">

</head>
<body class="login-bg">
    <div class="login-container">
        <h2>Task Manager Login</h2>
        <form method="POST" class="login-form">
            <div class="field">
                <label>Username</label>
                <input name="username" required autofocus>
            </div>
            <div class="field">
                <label>Password</label>
                <input name="password" type="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        </form>
    </div>
</body>
</html>