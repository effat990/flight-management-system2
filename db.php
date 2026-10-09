<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "flyair";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Migration safety for existing XAMPP installations. Older copies of the
// database may not have the newer dynamic flight fields yet. Add them once
// automatically so flights, booking, and payment pages never show
// "Undefined array key" warnings.
$travelTimeColumn = $conn->query("SHOW COLUMNS FROM flights LIKE 'travel_time'");
if ($travelTimeColumn && $travelTimeColumn->num_rows === 0) {
    $conn->query("ALTER TABLE flights ADD COLUMN travel_time VARCHAR(40) NOT NULL DEFAULT '1h 00m' AFTER seats_available");
}

$departureTimeColumn = $conn->query("SHOW COLUMNS FROM flights LIKE 'departure_time'");
if ($departureTimeColumn && $departureTimeColumn->num_rows === 0) {
    $conn->query("ALTER TABLE flights ADD COLUMN departure_time TIME NOT NULL DEFAULT '09:00:00' AFTER departure_date");
}

$boardingTimeColumn = $conn->query("SHOW COLUMNS FROM flights LIKE 'boarding_time'");
if ($boardingTimeColumn && $boardingTimeColumn->num_rows === 0) {
    $conn->query("ALTER TABLE flights ADD COLUMN boarding_time TIME NOT NULL DEFAULT '08:15:00' AFTER departure_time");
}

$gateColumn = $conn->query("SHOW COLUMNS FROM flights LIKE 'gate_no'");
if ($gateColumn && $gateColumn->num_rows === 0) {
    $conn->query("ALTER TABLE flights ADD COLUMN gate_no VARCHAR(20) NOT NULL DEFAULT 'A01' AFTER boarding_time");
}

$conn->query("UPDATE flights SET departure_time = '09:00:00' WHERE departure_time IS NULL OR departure_time = '00:00:00'");
$conn->query("UPDATE flights SET boarding_time = '08:15:00' WHERE boarding_time IS NULL OR boarding_time = '00:00:00'");
$conn->query("UPDATE flights SET gate_no = 'A01' WHERE gate_no IS NULL OR TRIM(gate_no) = ''");

$baseFareColumn = $conn->query("SHOW COLUMNS FROM flights LIKE 'base_fare'");
if ($baseFareColumn && $baseFareColumn->num_rows === 0) {
    $conn->query("ALTER TABLE flights ADD COLUMN base_fare DECIMAL(10,2) NOT NULL DEFAULT 5000 AFTER travel_time");
}

// Give older flight records sensible values if they were created before the
// dynamic fields were introduced.
$conn->query("UPDATE flights SET travel_time = '1h 00m' WHERE travel_time IS NULL OR TRIM(travel_time) = ''");
$conn->query("UPDATE flights SET base_fare = 5000 WHERE base_fare IS NULL");

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>