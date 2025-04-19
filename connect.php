<?php
$servername = "localhost";
$username = "arbeit_admin";
$password = "eZ@3D*db9p9j";
$database = "arbeitte_cdesign";

$con = mysqli_connect($servername, $username, $password, $database);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (!$con->ping()) {
    $con = new mysqli($servername, $username, $password, $database);
} else {
    echo "";
}
?>