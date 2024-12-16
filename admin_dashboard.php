<?php
session_start();
include 'config.php';
include 'header.php';

// Ensure only admins can access this page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = connectDB();
$id = $_SESSION['user_id'];

// Check if the current user is an admin
$sql = "SELECT Role FROM naudotojas WHERE id = '$id'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    if ($row['Role'] !== 'Administratorius') {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

// Handle block/unblock actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = intval($_POST['user_id']);
    $newStatus = $conn->real_escape_string($_POST['status']);

    // Update the user's status in pirkejas table
    $update_sql = "UPDATE pirkejas SET Paskyros_busena = '$newStatus' WHERE id = $userId";
    if ($conn->query($update_sql) === TRUE) {
        $message = "User status updated successfully.";
    } else {
        $message = "Error updating status: " . $conn->error;
    }
}

// Fetch all users with their status
$sql = "SELECT naudotojas.id, naudotojas.Slapyvardis, pirkejas.Paskyros_busena 
        FROM naudotojas 
        LEFT JOIN pirkejas ON naudotojas.id = pirkejas.id
        WHERE naudotojas.Role = 'Naudotojas'";
$users = $conn->query($sql);

$sqlAllUsers = "
    SELECT naudotojas.id, naudotojas.Vardas, naudotojas.Pavarde, naudotojas.Tel_nr, naudotojas.El_pastas, 
           naudotojas.Slaptazodis, naudotojas.Registracijos_data, naudotojas.Slapyvardis, naudotojas.Gimimo_data, 
           naudotojas.Role
    FROM naudotojas
";
$allUsers = $conn->query($sqlAllUsers);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Blokuoti vartotojus</h1>
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Slapyvardis</th>
                    <th>Paskyros būsena</th>
                    <th>Veiksmas</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['Slapyvardis']); ?></td>
                        <td><?php echo htmlspecialchars($row['Paskyros_busena']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <?php if ($row['Paskyros_busena'] === 'Aktyvus'): ?>
                                    <button type="submit" name="status" value="Užblokuotas" class="btn btn-danger">Užblokuoti</button>
                                <?php else: ?>
                                    <button type="submit" name="status" value="Aktyvus" class="btn btn-success">Aktyvinti</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h1 class="mb-4">Vartotojų sąrašas</h1>

<?php if (isset($message)): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Vardas</th>
            <th>Pavardė</th>
            <th>Tel. numeris</th>
            <th>El. paštas</th>
            <th>Registracijos data</th>
            <th>Slapyvardis</th>
            <th>Gimimo data</th>
            <th>Rolė</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $allUsers->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['Vardas']); ?></td>
                <td><?php echo htmlspecialchars($row['Pavarde']); ?></td>
                <td><?php echo htmlspecialchars($row['Tel_nr']); ?></td>
                <td><?php echo htmlspecialchars($row['El_pastas']); ?></td>
                <td><?php echo htmlspecialchars($row['Registracijos_data']); ?></td>
                <td><?php echo htmlspecialchars($row['Slapyvardis']); ?></td>
                <td><?php echo htmlspecialchars($row['Gimimo_data']); ?></td>
                <td><?php echo htmlspecialchars($row['Role']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

    </div>
</body>
</html>
<?php $conn->close(); ?>
