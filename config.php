<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "percetakan";

$conn = mysqli_connect("localhost", "root", "", "percetakan");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
