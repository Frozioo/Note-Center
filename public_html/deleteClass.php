<?php
    session_start();
    require "/home/group9/connections/connect.php";
    $classID = $_POST['ClassID'];

    // Delete the record from the database
    try {
    $directory = '/home/group9/public_html/Notes/' . $classID;
    
    // Select NotesID from Notes where ClassID matches
    $temp = ("select NotesID from Notes where ClassID=?");
    $tempVar = $conn->prepare($temp);
    $tempVar->execute([$classID]);
    $notes = $tempVar->fetchAll(PDO::FETCH_COLUMN);
        //Delete all of the notes from the current class
     foreach ($notes as $notesID) {
        $temp2 = "select * from Notes where NotesID=?";
        $tempVar2 = $conn->prepare($temp2);
        $tempVar2->execute([$notesID]);
        $note = $tempVar2->fetch(PDO::FETCH_ASSOC);

        if (file_exists($note['NotePath'])) {
            unlink($note['NotePath']);
        }

        $temp4 = "delete from Notes where NotesID=?";
        $tempVar4 = $conn->prepare($temp4);
        $tempVar4->execute([$notesID]);
        }
    //Delete every student from the class
    $temp3 = "delete from StudentClass where ClassID=?";
    $tempVar3 = $conn->prepare($temp3);
    $tempVar3->execute([$classID]);
    //Delete all help requests from the current class
    $query2 = ("delete from ContactForms where ClassID=?");
    $qr2 = $conn->prepare($query2);
    $qr2->execute([$classID]);
    //Delete the class
    $query3 = ("delete from Class where ClassID=?");
    $qr = $conn->prepare($query3);
    $qr->execute([$classID]);
    $files = glob($directory . '/*');
    foreach($files as $file){
      if(is_file($file))
        unlink($file);
    }
    rmdir($directory);


    } catch (Exception $e) {
        echo "Database Error: ", $e->getMessage();
    }

    header("Location: ClassHomePage.php");
    exit;
?>