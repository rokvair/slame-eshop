<?php
include 'config.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $volume = $_POST['volume'];
    $description = $_POST['description'];
    $color = $_POST['color'];
    $texture = $_POST['texture'];
    $scent = $_POST['scent'];
    $in_stock = $_POST['stock'];
    $country = $_POST['country'];
    $image_url = $_POST['image_url'];

    $conn = connectDB();

    $sql = "INSERT INTO Preke (Pavadinimas, Kaina, Nuolaida, Turis, Aprasymas, Spalva, Tekstura, Kvapas, Kiekis_sandelyje, Pagaminimo_salis, Paveiksliuko_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sdidssssiss', $name, $price, $discount, $volume, $description, $color, $texture, $scent, $in_stock, $country, $image_url);

    if ($stmt->execute()) {
        echo "Item added successfully.";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<form method="POST">
    <input type="text" name="name" placeholder="Pavadinimas" required><br>
    <input type="number" name="price" step="0.01" placeholder="Kaina" required><br>
    <input type="number" name="discount" step="1" placeholder="Nuolaida %" required><br>
    <input type="number" name="volume" step="0.1" placeholder="Tūris" required><br>
    <textarea name="description" placeholder="Aprašymas"></textarea><br>
    <input type="text" name="color" placeholder="Spalva"><br>
    <input type="text" name="texture" placeholder="Tekstūra"><br>
    <input type="text" name="scent" placeholder="Kvapas"><br>
    <input type="number" name="stock" placeholder="Kiekis sandėlyje" required><br>
    <input type="text" name="country" placeholder="Pagaminimo šalis"><br>
    <input type="text" name="image_url" placeholder="Paveikslėlio URL"><br>
    <button type="submit">Add Item</button>
</form>
