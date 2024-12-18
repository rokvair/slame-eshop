<?php

$user_id = isset($_SESSION['user_id']) ? htmlspecialchars($_SESSION['user_id']) : null;
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
$role = isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : null;
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slime E-Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="navbar1">
        <nav>
            <div class="logo">Slime E-Shop</div>
            <ul class="nav-links">

                <!-- Greeting based on login status -->
                <?php if ($user_id): ?>
                    <p class="ml-3 font-weight-bold" style="margin: 0px">Sveiki, <?php echo $username; ?>!</p>
                <?php else: ?>
                    <p class="ml-3 font-weight-bold" style="margin: 0px">Sveiki, svečias!</p>
                <?php endif; ?>

                <!-- Navbar links -->
                <li><a href="index.php">Namai</a></li>
                <li><a href="all_items.php">Prekės</a></li>

                <?php if (!$user_id): ?>
                    <!-- Links for guests -->
                    <li><a href="login.php" class="btn-login">Prisijungti</a></li>
                    <li><a href="register.php" class="btn-signup">Registruotis</a></li>
                <?php else: ?>
                    <li><a href="my_profile.php">Mano profilis</a></li>
                    
                    <!-- Links for all logged-in users -->
                    <li><a href="cart.php">Krepšelis</a></li>
                    <li><a href="my_orders.php">Mano Užsakymai</a></li>
                    <li><a href="password_reset_request.php">Keisti slaptažodį</a></li>

                    <!-- Links for admins -->
                    <?php if ($role === 'Administratorius'): ?>
                        <li><a href="admin_dashboard.php">Naudotojų valdymas</a></li>
                        <li><a href="admin_orders.php">Visi užsakymai</a></li>
                        <li><a href="items_controller.php">Prekių valdymas</a></li>
                        <li><a href="statistics.php">Statistika</a></li>
                    <?php endif; ?>

                    <!-- Links for managers -->
                    <?php if ($role === 'Vadybininkas'): ?>
                        <li><a href="statistics.php">Statistika</a></li>
                    <?php endif; ?>

                    <!-- Logout -->
                    <li><a href="logout.php" class="btn btn-danger">Atsijungti</a></li>
                <?php endif; ?>

            </ul>
        </nav>
    </header>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
