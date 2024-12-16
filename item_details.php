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
    echo "<div class='item-details-container'>";
    echo "<h1>{$item['Pavadinimas']}</h1>";
    echo "<img src='{$item['Paveiksliuko_url']}' alt='{$item['Pavadinimas']}' style='width:300px; height:300px;'><br>";
    echo "<p>Kaina: {$item['Kaina']} €</p>";
    echo "<p>Aprašymas: {$item['Aprasymas']}</p>";
    echo "<p>Spalva: {$item['Spalva']}</p>";
    echo "<p>Tekstūra: {$item['Tekstura']}</p>";
    echo "<p>Kvapas: {$item['Kvapas']}</p>";
    echo "<p>Kiekis sandėlyje: {$item['Kiekis_sandelyje']}</p>";

    // Add to Cart and Buy Now buttons
    echo "<form method='POST' action='cart.php'>";
    echo "<input type='hidden' name='item_id' value='{$item['id']}'>";
    echo "<input type='hidden' name='Pavadinimas' value='{$item['Pavadinimas']}'>";
    echo "<input type='hidden' name='Kaina' value='{$item['Kaina']}'>";
    echo "<button type='submit' name='add_to_cart'>Add to Cart</button>";
    echo "<button type='submit' name='buy_now'>Buy Now</button>";
    echo "</form>";
    echo "</div>";
} else {
echo "Tokios prekės nėra.";
exit;
}

// Display all comments for the item
echo "<div class='comments-container'>";
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
    echo "<p>Komentarų dar nėra. Būk pirmasis jį palikęs!</p>";
}

session_start(); // Start the session

// Check if the user is logged in
$user_id = $_SESSION['user_id'] ?? 0; // Assuming `user_id` is set during login

if ($user_id !== 0) {
    // Verify if the user has purchased the item
    $sql = "SELECT 1 
            FROM Uzsakymo_preke up
            INNER JOIN Uzsakymas u ON up.fk_Uzsakymas = u.id
            WHERE u.fk_Naudotojas = ? AND up.fk_Preke = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $item_id);
    $stmt->execute();
    $purchased = $stmt->get_result()->num_rows > 0;

    if ($purchased) {
        // Allow the user to leave a comment
        echo "<h3>Palikite atsiliepimą</h3>";
        echo "<form method='POST' action='submit_comment.php'>";
        echo "<textarea name='comment_text' placeholder='Jūsų atsiliepimas' required></textarea><br>";
        echo "<input type='number' name='rating' min='1' max='5' placeholder='Įvertinimas (1-5)' required><br>";
        echo "<input type='hidden' name='item_id' value='$item_id'>";
        echo "<button type='submit'>Pateikti Atsiliepimą</button>";
        echo "</form>";
    } else {
        // If the user has not purchased the item, show a message
        echo "<p>Norėdami palikti atsiliepimą, pirmiausia turite įsigyti šią prekę.</p>";
    }
} else {
    // If the user is not logged in, show a message
    echo "<p>Norėdami palikti atsiliepimą, turite <a href='login.php'>prisijungti</a>.</p>";
}

echo "</div>";
$conn->close();
?>
