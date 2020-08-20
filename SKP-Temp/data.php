<?php

$conn = mysqli_connect("localhost", "root", "", "test");

$result = mysqli_query($conn, "SELECT humidity, zone, temperature, updated FROM climatesensor WHERE zone = 5 and ((updated > '2020-08-12 08:00:00' and updated < '2020-08-12 08:05:00') or (updated > '2020-08-12 12:00:00' and updated < '2020-08-12 12:05:00') or (updated > '2020-08-12 15:00:00' AND updated < '2020-08-12 15:05:00'))");

$data =  array();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

print_r( json_encode($data));
$conn->close();
exit();