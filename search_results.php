<?php
// Database connection
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=1234");
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Get search term and type from the form
$search_term = $_POST['search_term'];
$search_type = $_POST['search_type'];

// Sanitize input
$search_term = pg_escape_string($search_term);

// Prepare SQL query based on the search type
switch ($search_type) {
    case 'dl_no':
        $query = "SELECT * FROM vehicles v JOIN users u ON v.user_id = u.user_id WHERE u.dl_number = $1";
        break;
    case 'phone':
        $query = "SELECT * FROM vehicles v JOIN users u ON v.user_id = u.user_id WHERE u.phone = $1";
        break;
    case 'email':
        $query = "SELECT * FROM vehicles v JOIN users u ON v.user_id = u.user_id WHERE u.email = $1";
        break;
    default:
        die("Invalid search type");
}

$result = pg_query_params($conn, $query, array($search_term));
if (!$result) {
    die("Error in SQL query: " . pg_last_error());
}

// Check if any results were returned
if (pg_num_rows($result) > 0) {
    echo "<div class='container'>";
    echo "<h2>Search Results</h2>";
    echo "<table>";
    echo "<tr><th>Vehicle Make</th><th>Vehicle Model</th><th>Chassis Number</th><th>Vehicle Color</th><th>State Code</th><th>District Code</th><th>Number Plate</th></tr>";
    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['vehicle_make']) . "</td>";
        echo "<td>" . htmlspecialchars($row['vehicle_model']) . "</td>";
        echo "<td>" . htmlspecialchars($row['chassis_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['vehicle_color']) . "</td>";
        echo "<td>" . htmlspecialchars($row['state_code']) . "</td>";
        echo "<td>" . htmlspecialchars($row['district_code']) . "</td>";
        echo "<td>" . htmlspecialchars($row['number_plate']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
} else {
    echo "<div class='container'><h2>No results found</h2></div>";
}

// Close the database connection
pg_close($conn);
?>
