<!DOCTYPE html>
<body>

</body>
</html>
<?php
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slime Shop - Home</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">SlimeShop</a>
            <ul class="nav-links">
                <li><a href="shop.php">Shop</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Welcome to SlimeShop!</h1>
            <p>Discover the best handcrafted slimes for all ages. Perfectly squishy, stretchy, and fun!</p>
            <a href="all_items.php" class="btn-primary">Shop Now</a>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <div class="container">
            <h2>Featured Slimes</h2>
            <div class="products-grid">
                <?php
                $servername = "localhost";
                $username = "root"; // replace with your username
                $password = "";     // replace with your password
                $dbname = "slame"; // replace with your database name

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch products
                $sql = "SELECT * FROM preke ORDER BY Ivertinimas DESC LIMIT 3";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='product-card'>";
                        echo "<img src='{$row['Paveiksliuko_url']}' alt='{$row['Pavadinimas']}'>";
                        echo "<h3>{$row['Pavadinimas']}</h3>";
                        echo "<p>\${$row['Kaina']}</p>";
                        echo "<p>Rating: {$row['Ivertinimas']} / 5</p>";
                        echo "<a href='item_details.php?id={$row['id']}' class='btn-primary'>View Details</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No products found.</p>";
                }
                $conn->close();
                ?>
            </div>
        </div>
    </section>

    <!-- Promotions Section -->
    <section class="promotions">
        <div class="container">
            <h2>Special Offers</h2>
            <p>Sign up for our newsletter to get 20% off your first order!</p>
            <a href="signup.php" class="btn-primary">Sign Up</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 SlimeShop. All rights reserved.</p>
            <ul class="footer-links">
                <li><a href="terms.php">Terms of Service</a></li>
                <li><a href="privacy.php">Privacy Policy</a></li>
            </ul>
        </div>
    </footer>
</body>
</html>
