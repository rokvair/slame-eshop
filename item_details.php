<?php
include 'config.php';
$conn = connectDB();
session_start(); // Start the session
include 'functions.php';
include 'header.php';

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$user_id = $_SESSION['user_id'] ;  // Simulated user ID, replace with actual logic

// Fetch item details
$sql = "SELECT * FROM Preke WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

$sql_avg = "SELECT AVG(Ivertinimas) AS AvgRating 
            FROM Atsiliepimas 
            WHERE fk_Preke = ?";
$stmt_avg = $conn->prepare($sql_avg);
$stmt_avg->bind_param("i", $item_id);
$stmt_avg->execute();
$avg_result = $stmt_avg->get_result();
$average_rating = $avg_result->fetch_assoc()['AvgRating'];

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
    echo "<div class='item-details-container'>";
    echo "<h1>{$item['Pavadinimas']}</h1>";
    echo "<img src='{$item['Paveiksliuko_url']}' alt='{$item['Pavadinimas']}' style='width:300px; height:300px;'><br>";
    echo "<p><b>Kaina: </b>{$item['Kaina']} €</p>";
     // Check for discount and calculate discounted price
    if (!is_null($item['Nuolaida']) && $item['Nuolaida'] > 0) {
        $discounted_price = $item['Kaina'] * (1 - $item['Nuolaida'] / 100);
        echo "<p><b>Nuolaida: </b>{$item['Nuolaida']}%</p>";
        echo "<p><b>Kaina su nuolaida: </b>" . number_format($discounted_price, 2) . " €</p>";
    }
    echo "<p><b>Vidutinis šios prekės įvertinimas: </b>" . number_format($average_rating, 2) . " / 5</p>";
    echo "<p><b>Aprašymas: </b>{$item['Aprasymas']}</p>";
    echo "<p><b>Spalva: </b>{$item['Spalva']}</p>";
    echo "<p><b>Tekstūra: </b>{$item['Tekstura']}</p>";
    echo "<p><b>Kvapas: </b>{$item['Kvapas']}</p>";
    echo "<p><b>Kiekis sandėlyje: </b>{$item['Kiekis_sandelyje']}</p>";

    $out_of_stock = $item['Kiekis_sandelyje'] <= 0;

    if ($out_of_stock) {
        echo "<p><strong>Prekių nėra sandėlyje</strong></p>";
    }
    else if($user_id == null)
    {
        echo  "<p><strong>Prašome prisijungti.<p><strong>";
    }
     else {

    // Add to Cart and Buy Now buttons
        echo "<form method='POST' action='cart.php'>";
        echo "<input type='hidden' name='item_id' value='{$item['id']}'>";
        echo "<input type='hidden' name='Pavadinimas' value='{$item['Pavadinimas']}'>";
        echo "<input type='hidden' name='Kaina' value='{$item['Kaina']}'>";

        echo "<button type='submit' name='add_to_cart' " . ($out_of_stock ? "disabled" : "") . ">Pridėti į krepšelį</button>";
        echo "</form>";
    }
    echo "</div>";
}
else {
    echo "Tokios prekės nėra.";
exit;
}

// Display all comments for the item
echo "<div class='comments-container'>";
echo "<h3>Atsiliepimai</h3>";

$sql = "SELECT a.Atsiliepimas, a.Ivertinimas, u.Slapyvardis 
        FROM Atsiliepimas a 
        INNER JOIN Naudotojas u ON a.fk_Naudotojas = u.id 
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



// Check if the user is logged in
$user_id = $_SESSION['user_id']; // Assuming `user_id` is set during login

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
        echo "<textarea name='comment_name' placeholder='Atsiliepimo pavadinimas' required></textarea><br>";
        echo "<textarea name='comment_text' placeholder='Jūsų atsiliepimas' required></textarea><br>";
        echo "<input type='number' name='rating' min='1' max='5' placeholder='Įvertinimas (1-5)' required><br>";
        echo "<input type='hidden' name='item_id' value='$item_id'>";
        echo "<button type='submit'>Pateikti atsiliepimą</button>";
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
