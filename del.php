<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $servername = "127.0.0.1:3308"; 
    $usernameDB = "root"; 
    $passwordDB = ""; 
    $dbname = "db"; 

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    $sql = "DELETE FROM ve WHERE id=$id";
    $conn->query($sql);
}

header("location: /TNVS_FINANCE/view_employee.php");
exit;
?>