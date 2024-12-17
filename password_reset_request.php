<?php
session_start();
require 'vendor/autoload.php'; // Load PHPMailer
include 'config.php'; // Database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$reset_link = ""; // To store the reset link

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    $conn = connectDB();

    // Check if the email exists
    $sql = "SELECT id FROM naudotojas WHERE El_pastas = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $user_id = $row['id'];

        // Generate the reset link
        $reset_link = "http://localhost/slame/slame-eshop/password_reset.php";

        // Send the email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Use your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'linksmasveiduks@gmail.com'; // Your email
            $mail->Password = 'naug jqta olbj achz'; // Your email password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email Content
            $mail->setFrom('linksmasveiduks@gmail.com', 'Slime E-Shop');
            $mail->addAddress($email); // Recipient's email
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Spauskite nuorodą žemiau, kad galėtumėte pakeisti slaptažodį:\n\n$reset_link";

            $mail->send();
            $message = "Slaptažodžio keitimo nuoroda nusiųsta į jūsų elektroninį paštą.";
        } catch (Exception $e) {
            $message = "Klaida siunčiant laišką: " . $mail->ErrorInfo;
        }
    } else {
        $message = "El. paštas nerastas.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slaptažodžio keitimas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Slaptažodžio keitimas</h1>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="email" class="form-label">Elektroninis paštas</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Siųsti slaptažodžio keitimo nuorodą</button>
    </form>
</div>
</body>
</html>
