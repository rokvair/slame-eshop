<?php
include 'config.php';
include 'functions.php';

session_start();
$user_id = $_SESSION['user_id'] ?? 0; // Replace with actual user session logic

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_name = sanitizeInput($_POST['comment_name']);
    $comment_text = sanitizeInput($_POST['comment_text']);
    $rating = intval($_POST['rating']);
    $item_id = intval($_POST['item_id']);

    if ($user_id === 0) {
        redirect('login.php'); // Redirect to login if the user is not logged in
    }

    $conn = connectDB();
    $sql = "INSERT INTO Atsiliepimas (Atsiliepimas, Ivertinimas, Data, Pavadinimas, fk_Preke, fk_Naudotojas) 
            VALUES (?, ?, NOW(), ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisii", $comment_text, $rating, $comment_name, $item_id, $user_id);

    if ($stmt->execute()) {
        echo "Atsiliepimas sėkmingai pateiktas.";
        redirect("item_details.php?id=$item_id");
    } else {
        echo "Klaida: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
