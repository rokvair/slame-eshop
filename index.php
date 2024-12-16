<!DOCTYPE html>
<body>

</body>
</html>
<?php
include 'header.php';
// Function to display messages with filtering
function displayMessages($filter_sender, $filter_recipient) {
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

    // Build the SQL query with conditions if filtering is applied
   

    $sql .= " ORDER BY created ASC"; // Order by creation time (ascending)

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $class = $row['siuntejo_id'] == 1 ? 'sender' : 'recipient'; // Example: adjust based on actual logic
            echo "<div class='message $class'>";
            echo "<strong>User {$row['siuntejo_id']} to User {$row['gavejo_id']}</strong><br>";
            echo "<p>{$row['message']}</p>";
            echo "<small>Posted on {$row['created']}</small>";
            echo "</div>";
        }
    } else {
        echo "No messages found.";
    }

    $conn->close();
}
?>


