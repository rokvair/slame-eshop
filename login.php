<?php
session_start();
include 'config.php';
include 'functions.php';
$conn = connectDB();
include 'header.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve input from form
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Hash the input password (use the same hashing algorithm as during registration)
    $hashed_password = hash('sha256', $password);

    // Query to check username and hashed password
    $sql = "SELECT id, Slapyvardis, `Role` FROM naudotojas WHERE Slapyvardis = ? AND Slaptazodis = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Set user ID and username in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['Slapyvardis'];
        $_SESSION['role'] = $user['Role'];

        // Redirect to the homepage or another appropriate page
        header("Location: index.php");
        exit();
    } else {
        // Display error message for invalid login
        $error = "Neteisingas slapyvardis arba slaptažodis!";
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
