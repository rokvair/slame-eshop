<?php
session_start();
include('config.php');
include 'header.php';

	$conn = connectDB();

    if (!$conn) {
        die("Database connection is not initialized.");
    }

function getStatisticsList() {
	global $conn;
	
    $query = "SELECT * FROM statistika ORDER BY Sukurimo_data DESC";
    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$type = $_POST['type'];
	$dateFrom = $_POST['dateFrom'];
	$dateTo = $_POST['dateTo'];
	
	if ($type === 'Apyvarta') {
		$query = "SELECT SUM(preke.Kaina*uzsakymo_preke.Kiekis) as Suma
					FROM uzsakymo_preke
					JOIN uzsakymas ON uzsakymo_preke.fk_Uzsakymas=uzsakymas.id
					JOIN preke ON uzsakymo_preke.fk_Preke=preke.id
					WHERE uzsakymas.Data BETWEEN '$dateFrom' AND '$dateTo'";
		
		$result = $conn->query($query);
		
		if (!$result) {
			die("Query failed: " . $conn->error);
		}
		$row = $result->fetch_assoc();
		
		$query = $conn->prepare("INSERT INTO statistika(Data_nuo, Data_iki, Sukurimo_data, Tipas) 
					VALUES (?, ?, ?, ?)");
					
		$query->execute([$dateFrom, $dateTo, date("Y-m-d"), $type]);
					
		$lastInsertId = $conn->insert_id;

		$query = $conn->prepare("INSERT INTO skaicius(Reiksme, fk_Statistika) 
					VALUES (?, ?)");
		
		$query->execute([$row['Suma'], $lastInsertId]);
	}
	
	elseif ($type === 'Užsakymai') {
		$query = "SELECT CONCAT_WS(';', uzsakymas.Data, uzsakymas.Statusas, naudotojas.Slapyvardis, GROUP_CONCAT(uzsakymo_preke.Kiekis, 'x ', preke.Pavadinimas SEPARATOR ' & ')) AS Eilute
					FROM uzsakymas
					JOIN naudotojas ON uzsakymas.fk_Naudotojas=naudotojas.id
					JOIN uzsakymo_preke ON uzsakymo_preke.fk_Uzsakymas=uzsakymas.id
					JOIN preke ON uzsakymo_preke.fk_Preke=preke.id
					WHERE uzsakymas.Data BETWEEN '$dateFrom' AND '$dateTo'
					GROUP BY naudotojas.Slapyvardis";
		
		$result = $conn->query($query);
		
		if (!$result) {
			die("Query failed: " . $conn->error);
		}
		
		$query = $conn->prepare("INSERT INTO statistika(Data_nuo, Data_iki, Sukurimo_data, Tipas) 
					VALUES (?, ?, ?, ?)");
					
		$query->execute([$dateFrom, $dateTo, date("Y-m-d"), $type]);
					
		$lastInsertId = $conn->insert_id;

		$query = $conn->prepare("INSERT INTO sarasas(Reiksme, fk_Statistika) 
					VALUES (?, ?)");
					
		while ($row = $result->fetch_assoc())
			$query->execute([$row['Eilute'], $lastInsertId]);
	}
	
	elseif ($type === 'Vidutinis krepšelio dydis') {
		$query = "SELECT AVG(Suma) FROM(
					SELECT SUM(preke.Kaina*uzsakymo_preke.Kiekis) as Suma
					FROM uzsakymo_preke
					JOIN uzsakymas ON uzsakymo_preke.fk_Uzsakymas=uzsakymas.id
					JOIN preke ON uzsakymo_preke.fk_Preke=preke.id
					WHERE uzsakymas.Data BETWEEN '$dateFrom' AND '$dateTo'
					GROUP BY uzsakymas.fk_Naudotojas) AS Vidurkis";
		
		$result = $conn->query($query);
		
		if (!$result) {
			die("Query failed: " . $conn->error);
		}
		$row = $result->fetch_assoc();
		
		$query = $conn->prepare("INSERT INTO statistika(Data_nuo, Data_iki, Sukurimo_data, Tipas) 
					VALUES (?, ?, ?, ?)");
					
		$query->execute([$dateFrom, $dateTo, date("Y-m-d"), $type]);
					
		$lastInsertId = $conn->insert_id;

		$query = $conn->prepare("INSERT INTO skaicius(Reiksme, fk_Statistika) 
					VALUES (?, ?)");
		
		$query->execute([$row['AVG(Suma)'], $lastInsertId]);
	}
	
	elseif ($type === 'Nauji vartotojai') {
		$query = "SELECT CONCAT_WS(';', naudotojas.Slapyvardis, naudotojas.Registracijos_data) as Eilute
					FROM naudotojas
					WHERE naudotojas.Registracijos_data BETWEEN '$dateFrom' AND '$dateTo'";
		
		$result = $conn->query($query);
		
		if (!$result) {
			die("Query failed: " . $conn->error);
		}
		
		$query = $conn->prepare("INSERT INTO statistika(Data_nuo, Data_iki, Sukurimo_data, Tipas) 
					VALUES (?, ?, ?, ?)");
					
		$query->execute([$dateFrom, $dateTo, date("Y-m-d"), $type]);
					
		$lastInsertId = $conn->insert_id;

		$query = $conn->prepare("INSERT INTO sarasas(Reiksme, fk_Statistika) 
					VALUES (?, ?)");
					
		while ($row = $result->fetch_assoc())
			$query->execute([$row['Eilute'], $lastInsertId]);
	}
}
?>

<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistika - Slime parduotuvė</title>
    <link rel="stylesheet" href="styles.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script>
	$(document).on('click', '.view-statistics', function () {
		var statisticsId = $(this).data('statistics-id'); 
		$.ajax({
			url: 'fetch_statistics.php', 
			type: 'POST',
			data: { id: statisticsId },
			success: function (data) {
				var statistics = JSON.parse(data);

				$('#modal-type').text(statistics.type);
				$('#modal-date-from').text(statistics.date_from);
				$('#modal-date-to').text(statistics.date_to);
				
				if (statistics.type == 'Nauji vartotojai') {
					var valuesArray = statistics.value.split(', ');

					var tableRows = '';
					valuesArray.forEach(function (value) {
				
					var valueParts = value.split(';');
					var username = valueParts[0];
					var date = valueParts[1];

					tableRows += '<tr><td>' + username + '</td><td>' + date + '</td></tr>';
            
					});

					$('#modal-value').html('<table class="table table-striped"><thead><tr><th>Slapyvardis</th><th>Registracijos data</th></tr></thead><tbody>' + tableRows + '</tbody></table>');
				}
				
				else if (statistics.type == 'Užsakymai') {
					var valuesArray = statistics.value.split(', ');

					var tableRows = '';
					valuesArray.forEach(function (value) {
				
					var valueParts = value.split(';');
					var orderDate = valueParts[0];
					var status = valueParts[1];
					var buyer = valueParts[2];
					var items = valueParts[3];

					tableRows += '<tr><td>' + orderDate + '</td><td>' + status + '</td><td>' + buyer + '</td><td>' + items + '</td></tr>';
            
					});

					$('#modal-value').html('<table class="table table-striped"><thead><tr><th>Užsakymo data</th><th>Statusas</th><th>Pirkėjas</th><th>Prekės</th></tr></thead><tbody>' + tableRows + '</tbody></table>');
				}
				
				else if (statistics.type == 'Apyvarta') {
					value = statistics.value;
					$('#modal-value').html('<p><strong>Parduotuvės apyvarta šiuo laikotarpiu: </strong>' + value + ' eur.');
				}
				
				else if (statistics.type == 'Vidutinis krepšelio dydis') {
					value = statistics.value;
					$('#modal-value').html('<p><strong>Vidutinis krepšelio dydis šiuo laikotarpiu: </strong>' + value + ' eur.');
				}
			},
			error: function () {
				alert('Klaida įkeliant statistiką!');
			}
		});
	});
	</script>
</head>
<body>
    <div class="page-container">

        <main class="content-wrapper">

            <section class="statistics">
			<?php if ($role === 'Vadybininkas'): ?>
				<br>
				<h1>Statistika</h1>
				<br>
				<button class="btn btn-success" data-toggle="modal" data-target="#gen-modal">Generuoti statistiką</button>
				<br><br>
            <?php endif; ?>
			
			<table class="table">
				<thead>
					<tr>
						<th scope="col">Pavadinimas</th>
						<th scope="col">Sukūrimo data</th>
						<th scope="col">Veiksmai</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$statistics = getStatisticsList();
					foreach ($statistics as $s) {
						echo '<tr>';
						echo '<td>' . $s['Tipas'] . ' (' . $s['Data_nuo'] . ' - ' . $s['Data_iki'] . ')</td>';
						echo '<td>' . $s['Sukurimo_data'] . '</td>';
						echo '<td><button class="btn btn-success view-statistics" data-toggle="modal" data-target="#show-modal" data-statistics-id=' . $s['id'] . '>Peržiūrėti</button></td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
            </section>
			
			<!-- modalinis langas statistikos generavimui -->
			<div class="modal fade" id="gen-modal" aria-labelledby="modal-title" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<div class="modal-title">Generuoti statistiką</div>
						</div>
						<div class="modal-body">
							<form method="POST">
								<select id="type" name="type" class="form-select">
									<option selected>Pasirinkite statistikos tipą</option>
									<option value="Apyvarta">Apyvarta</option>
									<option value="Nauji vartotojai">Nauji vartotojai</option>
									<option value="Užsakymai">Užsakymai</option>
									<option value="Vidutinis krepšelio dydis">Vidutinis krepšelio dydis</option>
								</select><br><br>
								<label for="modal-date-from" class="form-label">Pasirinkite datą nuo:</label>
								<input id="dateFrom" name="dateFrom" type="date" class="form-control"><br>
								<label for="modal-date-from" class="form-label">Pasirinkite datą iki:</label>
								<input id="dateTo" name="dateTo" type="date" class="form-control"><br>
								<button type="submit" class="btn btn-primary">Generuoti</button>
							</form>
						</div>
					</div>
				</div>
			</div>
			
			<!-- modalinis langas statistikos perziurai -->
			<div class="modal fade" id="show-modal" aria-labelledby="showModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="showModalLabel"><span id="modal-type"></span></h5>
						</div>
						<div class="modal-body">
							<p><strong>Data nuo:</strong> <span id="modal-date-from"></span></p>
							<p><strong>Data iki:</strong> <span id="modal-date-to"></span></p>
							<p><span id="modal-value"></span></p>
						</div>
					</div>
				</div>
			</div>

        </main>

        <footer>
            <p>© 2024 Slime E-Shop. imagine slame loolololololol.</p>
            <p>Follow us on:
                <a href="#">Instagram</a> | 
                <a href="#">Facebook</a> | 
                <a href="#">Twitter</a>
            </p>
        </footer>
    </div>
</body>
</html>
