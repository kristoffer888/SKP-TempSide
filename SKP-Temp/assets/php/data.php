<?php
$conn = mysqli_connect();
$arr = $_GET['week'];
$zoneNum = $_GET['zone'];


$result = mysqli_query($conn, "SELECT humidity, zone, temperature, updated FROM climateSensor WHERE zone = '$zoneNum' AND (date(updated) = '$arr[0]' OR date(updated) = '$arr[1]' OR date(updated) = '$arr[2]' OR date(updated) = '$arr[3]' OR date(updated) = '$arr[4]') AND ((time(updated) > time('08:00') and time(updated) < '08:05') or (time(updated) > '12:00' and time(updated) < '12:05') or (time(updated) > '15:00' AND time(updated) < '15:05'))");

$data = array();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

print_r(json_encode($data));
$conn->close();
exit();
