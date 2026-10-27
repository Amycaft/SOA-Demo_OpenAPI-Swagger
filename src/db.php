<?php
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$database = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database,
    (int)$port
);

$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Kết nối MySQL thất bại!");
}
?>