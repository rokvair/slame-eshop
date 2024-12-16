<?php
include 'config.php';
include 'functions.php';
include 'header.php';

$conn = connectDB();
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch item details
$sql = "SELECT * FROM Preke WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
    echo "<h1>{$item['Pavadinimas']}</h1>";
    echo "<img src='{$item['Paveiksliuko_url']}' alt='{$item['Pavadinimas']}' style='width:300px; height:300px;'><br>";
    echo "<p>Kaina: {$item['Kaina']} €</p>";
    echo "<p>Aprašymas: {$item['Aprasymas']}</p>";
    echo "<p>Spalva: {$item['Spalva']}</p>";
    echo "<p>Tekstūra: {$item['Tekstura']}</p>";
    echo "<p>Kvapas: {$item['Kvapas']}</p>";
    echo "<p>Kiekis sandėlyje: {$item['Kiekis_sandelyje']}</p>";

    $out_of_stock = $item['Kiekis_sandelyje'] <= 0;

    if ($out_of_stock) {
        echo "<p><strong>Prekių nėra sandėlyje</strong></p>";
    } else {

    // Add to Cart and Buy Now buttons
echo "<form method='POST' action='cart.php'>";
echo "<input type='hidden' name='item_id' value='{$item['id']}'>";
echo "<input type='hidden' name='Pavadinimas' value='{$item['Pavadinimas']}'>";
echo "<input type='hidden' name='Kaina' value='{$item['Kaina']}'>";


echo "<button type='submit' name='add_to_cart' " . ($out_of_stock ? "disabled" : "") . ">Add to Cart</button>";


    }
echo "</form>";
} else {
echo "Tokios prekės nėra.";
exit;
}

// Display all comments for the item
echo "<h3>Atsiliepimai</h3>";

$sql = "SELECT a.Atsiliepimas, a.Ivertinimas, u.Slapyvardis 
        FROM Atsiliepimas a 
        INNER JOIN Naudotojas u ON a.id = u.id 
        WHERE a.fk_Preke = ? 
        ORDER BY a.Data DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$comments = $stmt->get_result();



if ($comments->num_rows > 0) {
    while ($comment = $comments->fetch_assoc()) {
        echo "<div class='comment'>";
        echo "<p>Naudotojas: {$comment['Slapyvardis']}</p>";
        echo "<p>Įvertinimas: {$comment['Ivertinimas']} / 5</p>";
        echo "<p>Atsiliepimas: {$comment['Atsiliepimas']}</p>";
        echo "<hr>";
        echo "</div>";
    }
} else {
    echo "<p>No comments yet. Be the first to leave a comment!</p>";
}
/*
// Check if the user has purchased the item
session_start();
$user_id = $_SESSION['user_id'] ?? 0; // Replace with actual user session logic

$sql = "SELECT * 
        FROM Uzsakymo_preke up
        INNER JOIN Uzsakymas u ON up.fk_Uzsakymas = u.id
        WHERE u.fk_Naudotojas = ? AND up.fk_Preke = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $item_id);
$stmt->execute();
$purchased = $stmt->get_result()->num_rows > 0;

// Display comment form if the user has purchased the item
if ($purchased) {
    echo "<h3>Leave a Comment</h3>";
    echo "<form method='POST' action='submit_comment.php'>";
    echo "<textarea name='comment_text' placeholder='Your comment'></textarea><br>";
    echo "<input type='number' name='rating' min='1' max='5' placeholder='Rating (1-5)' required><br>";
    echo "<input type='hidden' name='item_id' value='$item_id'>";
    echo "<button type='submit'>Submit Comment</button>";
    echo "</form>";
} else {
    echo "<p>You need to purchase this item to leave a comment.</p>";
}

*/

$conn->close();
?>
