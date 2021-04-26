<?php
$conn = mysqli_connect("localhost", "root", "", "skptemp");
$arr = $_GET['week'];
 //$arr = ['2019-12-16', '2019-12-17', '2019-12-18', '2019-12-19', '2019-12-20'];

$result = mysqli_query($conn, "SELECT humidity, zone, temperature, updated FROM climatesensor WHERE zone = 5 AND (date(updated) = '$arr[0]' OR date(updated) = '$arr[1]' OR date(updated) = '$arr[2]' OR date(updated) = '$arr[3]' OR date(updated) = '$arr[4]') AND ((time(updated) > time('08:00') and time(updated) < '08:05') or (time(updated) > '12:00' and time(updated) < '12:05') or (time(updated) > '15:00' AND time(updated) < '15:05'))");

$data = array();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

print_r(json_encode($data));
$conn->close();
exit();