<?php
include 'config.php';
$conn = connectDB();
include 'header.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

    if (isset($_POST['delete_item'])) {
        // Delete the item
        $sql = "DELETE FROM Preke WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $item_id);

        if ($stmt->execute()) {
            // Redirect with success message
            header("Location: items_controller.php?status=success&item=" . urlencode($item['Pavadinimas']));
            exit; // Always call exit after header redirection
        } else {
            // Redirect with failure message
            header("Location: items_controller.php?status=error");
            exit; // Always call exit after header redirection
        }
        $stmt->close();
    } elseif (isset($_POST['edit_item'])) {
        // Redirect to edit page
        header("Location: item_edit.php?id=$item_id");
        exit;
    }
}

$conn->close();
?>
