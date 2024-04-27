<?php
    session_start();
    require "/home/group9/connections/connect.php";
    $emailID = $_GET['Email'];

    
    $query = "delete from ContactForms where Email=?";
    $qr = $conn->prepare($query);
    $qr->execute([$emailID]);

    header("Location: ContactForms.php");
    exit;


?>