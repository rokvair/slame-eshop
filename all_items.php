<?php
// Include necessary files and initialize database connection
include 'config.php';
include 'header.php';
$conn = connectDB();

// Fetch all items from the database
$sql = "SELECT id, Pavadinimas, Kaina, Paveiksliuko_url FROM Preke";
$result = $conn->query($sql);

// Start HTML output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Items</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add a stylesheet if needed -->
</head>
<body>
    <!-- Header -->
    <header>
        <h1>All Items</h1>
    </header>

    <!-- Main Content -->
    <main>
        <div class="items-container">
            <?php
            // Check if any items exist and display them
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="item">
                        <img src="<?= htmlspecialchars($row['Paveiksliuko_url']) ?>" 
                             alt="<?= htmlspecialchars($row['Pavadinimas']) ?>" 
                             style="width:100px; height:100px;"><br>
                        <strong><?= htmlspecialchars($row['Pavadinimas']) ?></strong><br>
                        <p>Price: <?= htmlspecialchars($row['Kaina']) ?> €</p>
                        <a href="item_details.php?id=<?= htmlspecialchars($row['id']) ?>">View Details</a>
                    </div>
                    <hr>
                    <?php
                }
            } else {
                echo "<p>No items found.</p>";
            }
            ?>
        </div>
    </main>


</body>
</html>
<?php
// Close database connection
$conn->close();
?>
