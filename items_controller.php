<?php
// Include necessary files and initialize database connection
include 'config.php';
include 'header.php';

$conn = connectDB();

// Fetch all items from the database
$sql = "SELECT * FROM Preke";

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
</head>
<body>
    <!-- Header -->
    <!-- Main Content -->
    <main>
        <div class="button-container">
            <a href="add_item.php">
                <button class="btn-add-item">Pridėti prekę</button>
            </a>
        </div>
        <?php
        echo "<p>Viso prekių: " . htmlspecialchars($items_count) . "</p>";
        // Check for success or error message in URL
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                echo "<p style='color: green;'>Prekė sėkmingai atnaujinta: " . htmlspecialchars($_GET['item']) . ".</p>";
            } elseif ($_GET['status'] == 'error') {
                echo "<p style='color: red;'>Įvyko klaida atnaujinant prekę. Bandykite dar kartą.</p>";
            }
        }
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
                        <!-- Add Edit and Delete Buttons -->
                        <div class="button-container">
                        <form method="POST" action="item_edit_delete.php" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?= htmlspecialchars($row['id']) ?>">
                            <button type="submit" name="edit_item" class="edit-button">Redaguoti</button>
                        </form>
                        <form method="POST" action="item_edit_delete.php" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?= htmlspecialchars($row['id']) ?>">
                            <button type="submit" name="delete_item" class="delete-button" onclick="return confirm('Ar tikrai norite ištrinti šią prekę?');">Ištrinti</button>
                        </form>
                        </div>
                    </div>
                    <hr>
                    <?php
                }
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
