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
    echo "<p>Price: {$item['Kaina']} €</p>";
    echo "<p>Description: {$item['Aprasymas']}</p>";
    echo "<p>Color: {$item['Spalva']}</p>";
    echo "<p>Texture: {$item['Tekstura']}</p>";
    echo "<p>Scent: {$item['Kvapas']}</p>";
} else {
    echo "Item not found.";
    exit;
}

// Display all comments for the item
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
        echo "<p><strong>Naudotojas: {$comment['Slapyvardis']}</strong></p>";
        echo "<p>Rating: {$comment['Ivertinimas']} / 5</p>";
        echo "<p>{$comment['Atsiliepimas']}</p>";
        echo "<hr>";
        echo "</div>";
    }
} else {
    echo "<p>No comments yet. Be the first to leave a comment!</p>";
}



$conn->close();
?>
