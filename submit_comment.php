<?php
include 'config.php';
include 'functions.php';

session_start();
$user_id = $_SESSION['user_id'] ?? 0; // Replace with actual user session logic

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_text = sanitizeInput($_POST['comment_text']);
    $rating = intval($_POST['rating']);
    $item_id = intval($_POST['item_id']);

    if ($user_id === 0) {
        redirect('login.php'); // Redirect to login if the user is not logged in
    }

    $conn = connectDB();
    $sql = "INSERT INTO Atsiliepimas (gavejo_id, prekes_id, Komentaras, Ivertinimas, data) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisi", $user_id, $item_id, $comment_text, $rating);

    if ($stmt->execute()) {
        echo "Comment submitted successfully.";
        redirect("item_detail.php?id=$item_id");
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
