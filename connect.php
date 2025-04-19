<?php
// $servername = "localhost";
// $username = "arbeit_vcard";
// $password = "arbeit1234@";
// $database = "arbeitte_cdesign";
$username = "root";
$password = "";
$database = "cdesign";

$con = mysqli_connect($servername, $username, $password, $database);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (!$con->ping()) {
    $con = new mysqli($servername, $username, $password, $database);
} else {
    echo "";
}
?>