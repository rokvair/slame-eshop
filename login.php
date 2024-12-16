<?php
include 'config.php';
include 'functions.php';
include 'header.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = connectDB();

    // Retrieve input from form
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Hash the input password (use the same hashing algorithm as during registration)
    $hashed_password = hash('sha256', $password);

    // Query to check username and hashed password
    $sql = "SELECT * FROM naudotojas WHERE Slapyvardis = '$username' AND Slaptazodis = '$hashed_password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        // If valid, set session and redirect
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        // Display error message for invalid login
        $error = "Invalid username or password!";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prisijungimas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container form-container">
        <div class="form-header">
            <h1>Prisijungimas</h1>
        </div>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Slapyvardis</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Slaptažodis</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Prisijungti</button>
            <?php if ($error): ?>
                <p class="text-danger mt-3"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
