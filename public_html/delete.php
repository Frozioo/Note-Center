<?php
    session_start();
    require "/home/group9/connections/connect.php";
    $notePath = $_POST['notePath'];
    $classID = $_POST['ClassID'];

    // Delete the file
    if (file_exists($notePath)) {
        unlink($notePath);
    } else {
        echo "File does not exist: " . $notePath;
    }

    // Delete the record from the database
    $stmt = $conn->prepare("DELETE FROM Notes WHERE NotePath = ?");
    $stmt->bindValue(1, $notePath);
    $stmt->execute();
    header("Location: classPage.php?ClassID=$classID");
?>