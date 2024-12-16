<?php
session_start();
include 'config.php';
$conn = connectDB();
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Fetch user details
$id = $_SESSION['user_id'];
$sql = "SELECT Vardas, Pavarde, Tel_nr, El_pastas, Registracijos_data, Slapyvardis, Gimimo_data FROM naudotojas WHERE id = '$id'";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    $vardas = $row['Vardas'];
    $pavarde = $row['Pavarde'];
    $tel_nr = $row['Tel_nr'];
    $el_pastas = $row['El_pastas'];
    $registracijos_data = $row['Registracijos_data'];
    $slapyvardis = $row['Slapyvardis'];
    $gimimo_data = $row['Gimimo_data'];
} else {
    $message = "User not found.";
}

// Update user data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_vardas = $conn->real_escape_string($_POST['vardas']);
    $new_pavarde = $conn->real_escape_string($_POST['pavarde']);
    $new_tel_nr = $conn->real_escape_string($_POST['tel_nr']);
    $new_el_pastas = $conn->real_escape_string($_POST['el_pastas']);
    $new_gimimo_data = $conn->real_escape_string($_POST['gimimo_data']);
    $new_slapyvardis = $conn->real_escape_string($_POST['slapyvardis']);

    $update_sql = "UPDATE naudotojas 
                   SET Vardas = '$new_vardas', 
                       Pavarde = '$new_pavarde', 
                       Tel_nr = '$new_tel_nr', 
                       El_pastas = '$new_el_pastas', 
                       Gimimo_data = '$new_gimimo_data',
                       Slapyvardis = '$new_slapyvardis'
                   WHERE id = '$id'";

    if ($conn->query($update_sql) === TRUE) {
        $message = "Profile updated successfully!";
        $_SESSION['user_id'] = $new_id; // Update session id if it changes
        header("Refresh:0");
    } else {
        $message = "Error updating profile: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Redaguoti Profilį</h1>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="POST" action="my_profile.php" class="mb-4">
        <div class="mb-3">
            <label for="vardas" class="form-label">Vardas</label>
            <input type="text" id="vardas" name="vardas" class="form-control" value="<?php echo htmlspecialchars($vardas); ?>" required>
        </div>
        <div class="mb-3">
            <label for="pavarde" class="form-label">Pavardė</label>
            <input type="text" id="pavarde" name="pavarde" class="form-control" value="<?php echo htmlspecialchars($pavarde); ?>" required>
        </div>
        <div class="mb-3">
            <label for="tel_nr" class="form-label">Tel. numeris</label>
            <input type="text" id="tel_nr" name="tel_nr" class="form-control" value="<?php echo htmlspecialchars($tel_nr); ?>" required>
        </div>
        <div class="mb-3">
            <label for="el_pastas" class="form-label">El. paštas</label>
            <input type="email" id="el_pastas" name="el_pastas" class="form-control" value="<?php echo htmlspecialchars($el_pastas); ?>" required>
        </div>
        <div class="mb-3">
            <label for="gimimo_data" class="form-label">Gimimo data</label>
            <input type="date" id="gimimo_data" name="gimimo_data" class="form-control" value="<?php echo htmlspecialchars($gimimo_data); ?>" required>
        </div>
        <div class="mb-3">
            <label for="slapyvardis" class="form-label">Slapyvardis</label>
            <input type="text" id="slapyvardis" name="slapyvardis" class="form-control" value="<?php echo htmlspecialchars($slapyvardis); ?>" required>
        </div>
        <button type="submit" color="green">Atnaujinti Profilį</button>
    </form>
</div>
</body>
</html>
