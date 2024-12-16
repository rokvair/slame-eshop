<?php
// Include necessary files and initialize database connection
include 'config.php';
include 'header.php';

$conn = connectDB();

// Handle search term
$search_term = '';
$min_price = '';
$max_price = '';
if (isset($_GET['search'])) {
    $search_term = htmlspecialchars($_GET['search']);
    
}
if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $min_price = htmlspecialchars($_GET['min_price']);
}
if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $max_price = htmlspecialchars($_GET['max_price']);
}


// Fetch all items from the database
$sql = "SELECT id, Pavadinimas, Kaina, Paveiksliuko_url, Nuolaida FROM Preke WHERE 1=1";
if (!empty($search_term)) {
    $sql .= " AND (Pavadinimas LIKE '%" . $conn->real_escape_string($search_term) . "%'"
        . " OR Spalva LIKE '%" . $conn->real_escape_string($search_term) . "%'"
        . " OR Tekstura LIKE '%" . $conn->real_escape_string($search_term) . "%'"
        . " OR Kvapas LIKE '%" . $conn->real_escape_string($search_term) . "%'"
        . " OR Pagaminimo_salis LIKE '%" . $conn->real_escape_string($search_term) . "%'"
        . " OR Aprasymas LIKE '%" . $conn->real_escape_string($search_term) . "%')";
}
if (!empty($min_price)) {
    $sql .= " AND (CASE WHEN Nuolaida != 0 THEN Kaina * (1 - Nuolaida / 100) ELSE Kaina END) >= " . $conn->real_escape_string($min_price);
}
if (!empty($max_price)) {
    $sql .= " AND (CASE WHEN Nuolaida != 0 THEN Kaina * (1 - Nuolaida / 100) ELSE Kaina END) <= " . $conn->real_escape_string($max_price);
}

$result = $conn->query($sql);
$items_count= $result->num_rows;
// Start HTML output
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visos prekės</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add a stylesheet if needed -->
</head>
<body>
    <!-- Header -->
    <header>
        <h1>Visos prekės</h1>
    </header>
    <h3>Paieška</h3>
     <!-- Search Bar -->
    <form method="GET" action="" class="search-bar">
        <input type="text" name="search" placeholder="Įveskite raktažodį..." value="<?= htmlspecialchars($search_term) ?>">
        <input type="number" name="min_price" placeholder="Min kaina (€)" value="<?= htmlspecialchars($min_price) ?>" step="0.01">
        <input type="number" name="max_price" placeholder="Max kaina (€)" value="<?= htmlspecialchars($max_price) ?>" step="0.01">
        <div class="button-container">
            <button type="submit">Ieškoti</button>
            <button type="button" onclick="window.location.href = window.location.pathname;">Išvalyti filtrus</button>
    </div>
    </form>

    <!-- Main Content -->
    <main>
        <?php
        echo "<p>Rasta prekių: " . htmlspecialchars($items_count) . "</p>";
        ?>
        <div class="items-container">
            <?php
            // Check if any items exist and display them
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $original_price = $row['Kaina'];
                    $discount = $row['Nuolaida'];
                    $discounted_price = 0;

                    if ($discount != 0) {
                        $discounted_price = $original_price * (1 - $discount / 100);
                    }
                    ?>
                    <div class="item">
                        <img src="<?= htmlspecialchars($row['Paveiksliuko_url']) ?>" 
                             alt="<?= htmlspecialchars($row['Pavadinimas']) ?>" 
                             style="width:200px; height:200px;"><br>
                        <strong><?= htmlspecialchars($row['Pavadinimas']) ?></strong><br>
                        <?php if ($discount != 0): ?>
                            <p>
                                Kaina: 
                                <span style="text-decoration: line-through;"><?= number_format($original_price, 2) ?> €</span> 
                                <span style="color:red;">-<?= number_format($discount, 0) ?>% <?= number_format($discounted_price, 2) ?> €</span>
                            </p>
                        <?php else: ?>
                            <p>Kaina: <?= number_format($original_price, 2) ?> €</p>
                        <?php endif; ?>
                        <a href="item_details.php?id=<?= htmlspecialchars($row['id']) ?>">Peržiūrėti</a>
                    </div>
                    <hr>
                    <?php
                }
            }
            ?>
        </div>
    </main>
    <script>
        function clearFilters() {
            // Clear form fields using JavaScript
            const form = document.querySelector('form');
            form.reset();  // Resets all form fields to their default values
        }
    </script>

</body>
</html>
<?php
// Close database connection
$conn->close();
?>
