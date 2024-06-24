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
$state_code = $_POST['state_code'] ?? null;
$district_code = $_POST['district_code'] ?? null;

// Check if all required fields are filled
if ($first_name && $last_name && $dl_number && $phone && $email && $vehicle_make && $vehicle_model && $chassis_number && $vehicle_color && $state_code && $district_code) {
    // Insert user data
    $query_user = "INSERT INTO users (first_name, last_name, dl_number, phone, email) VALUES ($1, $2, $3, $4, $5) RETURNING user_id";
    $result_user = pg_query_params($conn, $query_user, array($first_name, $last_name, $dl_number, $phone, $email));
    if (!$result_user) {
        die("Error in SQL query: " . pg_last_error());
    }

    $user_id = pg_fetch_result($result_user, 0, 'user_id');

    // Generate unique number plate
    $query_count = "SELECT COUNT(*) FROM vehicles WHERE state_code = $1 AND district_code = $2";
    $result_count = pg_query_params($conn, $query_count, array($state_code, $district_code));
    if (!$result_count) {
        die("Error in SQL query: " . pg_last_error());
    }

    $count = pg_fetch_result($result_count, 0, 0);
      $counter = $count + 1;

      // Generate the DDDD part of the number plate (0001 to 9999)
      $dddd = str_pad(($counter % 10000), 4, '0', STR_PAD_LEFT);

      // Generate the CC part of the number plate (AA to ZZ)
      $letters = range('A', 'Z');
      $cc_index = floor(($counter - 1) / 10000);
      $cc = $letters[floor($cc_index / 26)] . $letters[$cc_index % 26];

      $number_plate = $state_code . ' ' . $district_code . ' ' . $cc . ' ' . $dddd;

    // Insert vehicle data with number plate
    $query_vehicle = "INSERT INTO vehicles (user_id, vehicle_make, vehicle_model, chassis_number, vehicle_color, state_code, district_code, number_plate) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
    $result_vehicle = pg_query_params($conn, $query_vehicle, array($user_id, $vehicle_make, $vehicle_model, $chassis_number, $vehicle_color, $state_code, $district_code, $number_plate));
    if (!$result_vehicle) {
        die("Error in SQL query: " . pg_last_error());
    }

    // Close the connection
    pg_close($conn);

    // Display success message
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Vehicle Registered</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="success-container">
            <h2>Vehicle Registered Successfully!</h2>
            <h3>Your vehicle has been registered successfully with the following details:</h3>
            <p><strong>Registration Number:</strong> ' . htmlspecialchars($number_plate) . '</p>
            <p><a href="index.html">Register Another Vehicle</a></p>
        </div>
    </body>
    </html>';
} else {
    echo "Error: All fields are required.";
}
?>
