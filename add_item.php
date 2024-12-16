<?php
// Include necessary files and initialize database connection
include 'config.php';
$conn = connectDB();
include 'header.php';


// Handle form submission and add item to the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pavadinimas = $_POST['pavadinimas'];
    $kaina = $_POST['kaina'];
    $nuoroda = $_POST['nuoroda'];
    $nuolaida = $_POST['nuolaida'];
    $spalva = $_POST['spalva'];
    $kvapas = $_POST['kvapas'];
    $tekstura = $_POST['tekstura'];
    $salis = $_POST['salis'];
    $aprasymas = $_POST['aprasymas'];
    $kiekis = $_POST['kiekis'];
    $turis = $_POST['turis'];
    

    // Insert the new item into the database
    $sql = "INSERT INTO Preke (Pavadinimas, Kaina, Paveiksliuko_url, Nuolaida, Spalva, Kvapas, Tekstura, 
    Pagaminimo_salis, Aprasymas, Kiekis_sandelyje, Ivertinimas, Turis) 
            VALUES ('$pavadinimas', '$kaina', '$nuoroda', '$nuolaida', '$spalva', '$kvapas', '$tekstura',
            '$salis', '$aprasymas', '$kiekis', 0, '$turis')";

    if ($conn->query($sql) === TRUE) {
        header("Location: items_controller.php?status=success&item=$name");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pridėti prekę</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Pridėti naują prekę</h1>
    </header>

    <main>
        <!-- Add New Item Form -->
        <div class="form-container">
            <form method="POST">
                <label for="pavadinimas">Prekės pavadinimas:</label>
                <input type="text" id="pavadinimas" name="pavadinimas" required>

                <label for="kaina">Kaina (€):</label>
                <input type="number" id="kaina" name="kaina" step="0.01" required>

                <label for="nuolaida">Nuolaida (%):</label>
                <input type="number" id="nuolaida" name="nuolaida" min="0" max="100" required>

                <label for="nuoroda">Paveiksliuko URL:</label>
                <input type="text" id="nuoroda" name="nuoroda" required>

                <label for="spalva">Spalva:</label>
                <input type="text" id="spalva" name="spalva" required>

                <label for="kvapas">Kvapas:</label>
                <input type="text" id="kvapas" name="kvapas" required>

                <label for="tekstura">Tekstūra:</label>
                <input type="text" id="tekstura" name="tekstura" required>

                <label for="turis">Tūris:</label>
                <input type="number" id="turis" name="turis" min="0" max="1000" required>

                <label for="salis">Pagaminimo šalis:</label>
                <input type="text" id="salis" name="salis" required>

                <label for="aprasymas">Aprašymas:</label>
                <textarea id="description" name="aprasymas" rows="4" required></textarea>

                <label for="kiekis">Kiekis sandėlyje:</label>
                <input type="number" id="kiekis" name="kiekis" min="0" required>

                <button type="submit">Pridėti prekę</button>
            </form>
        </div>
    </main>
</body>
</html>
