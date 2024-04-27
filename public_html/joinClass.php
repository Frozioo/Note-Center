<?php
    require '/home/group9/connections/connect.php';

    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $accessCode = $_POST['accessCode'];

        $classCheck = "select * from Class where accessCode = :accessCode";
        $stmt = $conn->prepare($classCheck);
        $stmt->bindParam(':accessCode', $accessCode);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $class = $stmt->fetch(PDO::FETCH_ASSOC);
            $classID = $class['ClassID'];


            $email = $_SESSION['email'];
            $studentCheck = "select * from Student where StudentUserName = :email";
            $stmt = $conn->prepare($studentCheck);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            $studentID = $student['StudentID'];


            // $defaultNotesID = 1; 
            $stmt = $conn->prepare("insert into StudentClass (ClassID, StudentID) values (:ClassID, :StudentID)");
            $stmt->bindParam(':ClassID', $classID);
            $stmt->bindParam(':StudentID', $studentID);
            // $stmt->bindParam(':NotesID', $defaultNotesID);
            $stmt->execute();

            header('Location: ClassHomePage.php');
            exit;
        } else {
            $_SESSION['error'] = "Invalid access code";
            header('Location: ClassHomePage.php');
            exit;
        }
    }

?>