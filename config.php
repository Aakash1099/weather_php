<?php
$conn = new mysqli("localhost", "root", "", "weather_app");
if ($conn->connect_error) {
    die("Database connection failed");
}
?>
