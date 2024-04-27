<?php
require '/home/group9/connections/connect.php';

session_start();
if(!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit;
}

try {
if ($_SERVER["REQUEST_METHOD"]=="POST")
{
    if(isset($_POST['StudentID']))
    {
        $sID = $_POST['StudentID'];
        $query2 = ("update Student set CanUploadNotes = 0 where StudentID=?");
        $qr = $conn->prepare($query2);
        $qr->execute([$sID]);
    } 
}
} catch (Exception $e) {
    echo "Database Error: ", $e->getMessage();
}
?>