<?php
include 'config.php';
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
} else {
    echo "<script>alert('Prekė nerasta!'); window.location.href = 'items_controller.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pavadinimas = $_POST['Pavadinimas'];
    $kaina = $_POST['Kaina'];
    $nuolaida = $_POST['Nuolaida'];
    $turis = $_POST['Turis'];
    $aprasymas = $_POST['Aprasymas'];
    $spalva = $_POST['Spalva'];
    $tekstura = $_POST['Tekstura'];
    $kvapas = $_POST['Kvapas'];
    $kiekis_sandelyje = $_POST['Kiekis_sandelyje'];
    $pagaminimo_salis = $_POST['Pagaminimo_salis'];
    $paveiksliuko_url = $_POST['Paveiksliuko_url'];

    $sql = "UPDATE Preke SET 
        Pavadinimas = ?, 
        Kaina = ?, 
        Nuolaida = ?, 
        Turis = ?, 
        Aprasymas = ?, 
        Spalva = ?, 
        Tekstura = ?, 
        Kvapas = ?, 
        Kiekis_sandelyje = ?, 
        Pagaminimo_salis = ?, 
        Paveiksliuko_url = ? 
        WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sddssssssssi", 
        $pavadinimas, $kaina, $nuolaida, $turis, $aprasymas, $spalva, 
        $tekstura, $kvapas, $kiekis_sandelyje, 
        $pagaminimo_salis, $paveiksliuko_url, $item_id
    );


    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: items_controller.php?status=success&item=" . urlencode($item['Pavadinimas']));
        exit; // Always call exit after header redirection
    } else {
        // Redirect with failure message
        header("Location: items_controller.php?status=error");
        exit; // Always call exit after header redirection
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redaguoti prekę</title>
</head>
<body>
    <h1>Redaguoti prekę</h1>
    <form method="POST">
        <label for="Pavadinimas">Pavadinimas:</label><br>
        <input type="text" id="Pavadinimas" name="Pavadinimas" value="<?= htmlspecialchars($item['Pavadinimas']) ?>" required><br>

        <label for="Kaina">Kaina (€):</label><br>
        <input type="number" id="Kaina" name="Kaina" step="0.01" value="<?= htmlspecialchars($item['Kaina']) ?>" required><br>

        <label for="Nuolaida">Nuolaida (%):</label><br>
        <input type="number" id="Nuolaida" name="Nuolaida" value="<?= htmlspecialchars($item['Nuolaida']) ?>" min="0" max="100"><br>

        <label for="Turis">Tūris (ml):</label><br>
        <input type="number" id="Turis" name="Turis" step="0.01" value="<?= htmlspecialchars($item['Turis']) ?>" required><br>

        <label for="Aprasymas">Aprašymas:</label><br>
        <textarea id="Aprasymas" name="Aprasymas" required><?= htmlspecialchars($item['Aprasymas']) ?></textarea><br>

        <label for="Spalva">Spalva:</label><br>
        <input type="text" id="Spalva" name="Spalva" value="<?= htmlspecialchars($item['Spalva']) ?>" required><br>

        <label for="Tekstura">Tekstūra:</label><br>
        <input type="text" id="Tekstura" name="Tekstura" value="<?= htmlspecialchars($item['Tekstura']) ?>" required><br>

        <label for="Kvapas">Kvapas:</label><br>
        <input type="text" id="Kvapas" name="Kvapas" value="<?= htmlspecialchars($item['Kvapas']) ?>" required><br>

        <label for="Kiekis_sandelyje">Kiekis sandėlyje:</label><br>
        <input type="number" id="Kiekis_sandelyje" name="Kiekis_sandelyje" value="<?= htmlspecialchars($item['Kiekis_sandelyje']) ?>" required><br>

        <label for="Pagaminimo_salis">Pagaminimo šalis:</label><br>
        <input type="text" id="Pagaminimo_salis" name="Pagaminimo_salis" value="<?= htmlspecialchars($item['Pagaminimo_salis']) ?>" required><br>

        <label for="Paveiksliuko_url">Paveiksliuko URL:</label><br>
        <input type="text" id="Paveiksliuko_url" name="Paveiksliuko_url" value="<?= htmlspecialchars($item['Paveiksliuko_url']) ?>" required><br>

        <button type="submit">Atnaujinti</button>
    </form>
</body>
</html>