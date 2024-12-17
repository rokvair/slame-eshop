<?php
session_start();
include 'config.php';
$conn = connectDB();
include 'header.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form inputs
    $vardas = $conn->real_escape_string($_POST['vardas']);
    $pavarde = $conn->real_escape_string($_POST['pavarde']);
    $tel_nr = $conn->real_escape_string($_POST['tel_nr']);
    $el_pastas = $conn->real_escape_string($_POST['el_pastas']);
    $gimimo_data = $conn->real_escape_string($_POST['gimimo_data']);
    $slapyvardis = $conn->real_escape_string($_POST['slapyvardis']);
    $registracijos_data = date('Y-m-d'); // Today's date
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Address details
    $miestas = $conn->real_escape_string($_POST['miestas']);
    $gatve = $conn->real_escape_string($_POST['gatve']);
    $namo_nr = $conn->real_escape_string($_POST['namo_nr']);
    $buto_nr = $conn->real_escape_string($_POST['buto_nr']);
    $pasto_kodas = $conn->real_escape_string($_POST['pasto_kodas']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $message = "Slaptažodžiai nesutampa!";
    } else {
        // Hash the password
        $hashed_password = hash('sha256', $password);

        // Check if username or email already exists
        $check_sql = "SELECT * FROM naudotojas WHERE Slapyvardis = '$slapyvardis' OR El_pastas = '$el_pastas'";
        $check_result = $conn->query($check_sql);

        if ($check_result && $check_result->num_rows > 0) {
            $message = "Slapyvardis arba el. paštas jau užregistruotas!";
        } else {
            // Insert the user into naudotojas table
            $sql = "INSERT INTO naudotojas (Vardas, Pavarde, Tel_nr, El_pastas, Slaptazodis, Registracijos_data, Gimimo_data, Slapyvardis, `Role`)
                    VALUES ('$vardas', '$pavarde', '$tel_nr', '$el_pastas', '$hashed_password', '$registracijos_data', '$gimimo_data', '$slapyvardis', 'Naudotojas')";

            if ($conn->query($sql) === TRUE) {
                // Get the ID of the newly inserted user
                $user_id = $conn->insert_id;

                // Insert into the user status table with default status 'Aktyvus'
                $status_sql = "INSERT INTO pirkejas (id, Paskyros_busena) VALUES ('$user_id', 'Aktyvus')";

                // Insert address into the adresas table
                $address_sql = "INSERT INTO adresas (Miestas, Gatve, Namo_nr, Buto_nr, Pasto_kodas, fk_Naudotojas)
                                VALUES ('$miestas', '$gatve', '$namo_nr', '$buto_nr', '$pasto_kodas', '$user_id')";

                if ($conn->query($status_sql) === TRUE && $conn->query($address_sql) === TRUE) {
                    $message = "Registracija sėkminga.";
                    header("Location: login.php");
                    exit();
                } else {
                    $message = "Klaida atnaujinant duomenis: " . $conn->error;
                }
            } else {
                $message = "Klaida: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registruotis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container form-container">
        <div class="form-header">
            <h1>Registruotis</h1>
        </div>
        <form method="POST" action="">
            <!-- User Details -->
            <div class="mb-3">
                <label for="vardas" class="form-label">Vardas</label>
                <input type="text" id="vardas" name="vardas" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="pavarde" class="form-label">Pavardė</label>
                <input type="text" id="pavarde" name="pavarde" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="tel_nr" class="form-label">Tel. numeris</label>
                <input type="text" id="tel_nr" name="tel_nr" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="el_pastas" class="form-label">El. paštas</label>
                <input type="email" id="el_pastas" name="el_pastas" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="gimimo_data" class="form-label">Gimimo data</label>
                <input type="date" id="gimimo_data" name="gimimo_data" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="slapyvardis" class="form-label">Slapyvardis</label>
                <input type="text" id="slapyvardis" name="slapyvardis" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Slaptažodis</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Pakartokite slaptažodį</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>

            <!-- Address Details -->
            <h5>Adresas</h5>
            <div class="mb-3">
                <label for="miestas" class="form-label">Miestas</label>
                <input type="text" id="miestas" name="miestas" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="gatve" class="form-label">Gatvė</label>
                <input type="text" id="gatve" name="gatve" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="namo_nr" class="form-label">Namo Nr.</label>
                <input type="number" id="namo_nr" name="namo_nr" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="buto_nr" class="form-label">Buto Nr.</label>
                <input type="number" id="buto_nr" name="buto_nr" class="form-control">
            </div>
            <div class="mb-3">
                <label for="pasto_kodas" class="form-label">Pašto kodas</label>
                <input type="text" id="pasto_kodas" name="pasto_kodas" class="form-control" required>
            </div>

            <!-- Submit -->
            <button type="submit" color="green">Registruotis</button>
            <?php if ($message): ?>
                <p class="text-danger mt-3"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
