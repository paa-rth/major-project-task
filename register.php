<?php
$host = 'localhost';
$db = 'postgres';
$user = 'postgres';
$pass = '1234';

// Connect to PostgreSQL database
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass");
if (!$conn) {
    die("Error in connection: " . pg_last_error());
}

// Get form data
$first_name = $_POST['first_name'] ?? null;
$last_name = $_POST['last_name'] ?? null;
$dl_number = $_POST['dl_no'] ?? null;
$phone = $_POST['phone'] ?? null;
$email = $_POST['email'] ?? null;
$vehicle_make = $_POST['vehicle_make'] ?? null;
$vehicle_model = $_POST['vehicle_model'] ?? null;
$chassis_number = $_POST['ch_no'] ?? null;
$vehicle_color = $_POST['vehicle_color'] ?? null;

// Check if all required fields are filled
if ($first_name && $last_name && $dl_number && $phone && $email && $vehicle_make && $vehicle_model && $chassis_number && $vehicle_color) {
    // Insert user data
    $query_user = "INSERT INTO users (first_name, last_name, dl_number, phone, email) VALUES ($1, $2, $3, $4, $5) RETURNING user_id";
    $result_user = pg_query_params($conn, $query_user, array($first_name, $last_name, $dl_number, $phone, $email));
    if (!$result_user) {
        die("Error in SQL query: " . pg_last_error());
    }

    $user_id = pg_fetch_result($result_user, 0, 'user_id');

    // Insert vehicle data
    $query_vehicle = "INSERT INTO vehicles (user_id, vehicle_make, vehicle_model, chassis_number, vehicle_color) VALUES ($1, $2, $3, $4, $5)";
    $result_vehicle = pg_query_params($conn, $query_vehicle, array($user_id, $vehicle_make, $vehicle_model, $chassis_number, $vehicle_color));
    if (!$result_vehicle) {
        die("Error in SQL query: " . pg_last_error());
    }

    // Close the connection
    pg_close($conn);

    // Generate the registration plate (for simplicity, just display a success message)
    echo "Vehicle registered successfully!<br>";
    echo "Registration Number: " . $chassis_number;
} else {
    echo "Error: All fields are required.";
}
?>
