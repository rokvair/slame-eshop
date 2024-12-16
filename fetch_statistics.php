<?php
include('config.php');
$conn = connectDB();

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query to fetch the statistics details by ID
	
	$query = "SELECT statistika.Tipas FROM statistika WHERE statistika.id = ?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
	
	$type = $result->fetch_assoc();
	
	if ($type['Tipas'] === 'Nauji vartotojai' || $type['Tipas'] === 'Užsakymai') {
		$query = "SELECT statistika.Tipas, statistika.Data_nuo, statistika.Data_iki, sarasas.Reiksme AS Reiksme
					FROM statistika 
					JOIN sarasas ON sarasas.fk_Statistika = statistika.id
					WHERE statistika.id = ?";
				
		$stmt = $conn->prepare($query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
    
		if ($result->num_rows > 0) {
			// Fetch the main statistics information
			$statistics = $result->fetch_assoc();
        
			// Initialize an empty string for accumulating values
			$values = $statistics['Reiksme'];

			// Loop through the remaining rows to accumulate the "Reiksme" values
			while ($row = $result->fetch_assoc()) {
				if ($values) {
					$values .= ", ";  // Add a separator between values
				}
				$values .= $row['Reiksme'];
			}

			// Output the data as JSON
			echo json_encode([
				'type' => $statistics['Tipas'],
				'date_from' => $statistics['Data_nuo'],
				'date_to' => $statistics['Data_iki'],
				'value' => $values
			]);
		} else {
			echo json_encode(['error' => 'Data not found']);
		}
	}

	elseif ($type['Tipas'] === 'Apyvarta' || $type['Tipas'] === 'Vidutinis krepšelio dydis') {
		$query = "SELECT statistika.Tipas, statistika.Data_nuo, statistika.Data_iki, skaicius.Reiksme AS Reiksme
					FROM statistika 
					JOIN skaicius ON skaicius.fk_Statistika = statistika.id
					WHERE statistika.id = ?";
				
		$stmt = $conn->prepare($query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
    
		if ($result->num_rows > 0) {
			// Fetch the main statistics information
			$statistics = $result->fetch_assoc();

			// Output the data as JSON
			echo json_encode([
				'type' => $statistics['Tipas'],
				'date_from' => $statistics['Data_nuo'],
				'date_to' => $statistics['Data_iki'],
				'value' => $statistics['Reiksme']
			]);
		} else {
			echo json_encode(['error' => 'Data not found']);
		}
	}
}
?>
