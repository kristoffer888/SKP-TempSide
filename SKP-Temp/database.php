<?php

$host="localhost";
$username="infotavle.itd-sk";
$password="OVSZY0Jt";
$db_name="infotavle_itd_skp_sde_dk";
$tbl_name="ClimateSensor";


$con= mysqli_connect($host, $username, $password, $db_name);

if (!$con)
{
    die("Connection failed:" . mysqli_connect_error());
}
