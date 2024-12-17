<?php
session_start();
include 'config.php'; // Database connection

$message = "";

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($new_password) || empty($confirm_password)) {
        $message = "Abu laukai privalomi.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Slaptažodžiai nesutampa.";
    } else {
        $conn = connectDB();
        $hashed_password = hash('sha256', $new_password);

        // Update the password in the database
        $sql = "UPDATE naudotojas SET Slaptazodis = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            $message = "Slaptažodis atnaujintas sėkmingai!";
        } else {
            $message = "Klaida atnaujinant slaptažodį: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keisti slaptažodį</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Keisti slaptažodį</h1>

    <!-- Success/Error Message -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Password Change Form -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="new_password" class="form-label">Naujas slaptažodis</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Pakartokite slaptažodį</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Keisti slaptažodį</button> <br><br>
        <button type="button" class="btn btn-success w-100" onclick="window.location.href='logout.php';">
            Grįžti
        </button>

    </form>
</div>
</body>
</html>
