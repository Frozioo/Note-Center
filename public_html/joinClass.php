<?php
    require '/home/group9/connections/connect.php';

    session_start();
    //Checking if student is logged in properly
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $accessCode = $_POST['accessCode'];
        //Grabs the access code of all classes
        $classCheck = "select * from Class where accessCode = :accessCode";
        $stmt = $conn->prepare($classCheck);
        $stmt->bindParam(':accessCode', $accessCode);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $class = $stmt->fetch(PDO::FETCH_ASSOC);
            $classID = $class['ClassID'];

            //Getting the users information
            $email = $_SESSION['email'];
            $studentCheck = "select * from Student where StudentUserName = :email";
            $stmt = $conn->prepare($studentCheck);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            $studentID = $student['StudentID'];

            $checkIfInClass = "select * from StudentClass where ClassID=:classID and StudentID=:studentID";
            $checkstmt = $conn->prepare($checkIfInClass);
            $checkstmt->bindParam(':classID', $classID);
            $checkstmt->bindParam(':studentID', $studentID);
            $checkstmt->execute();

            if ($checkstmt->rowCount() > 0){
                header('Location: ClassHomePage.php');
                exit;
            } else {
    
            //Insert student into class if they input a valid access code
            $stmt = $conn->prepare("insert into StudentClass (ClassID, StudentID) values (:ClassID, :StudentID)");
            $stmt->bindParam(':ClassID', $classID);
            $stmt->bindParam(':StudentID', $studentID);
            $stmt->execute();

            header('Location: ClassHomePage.php');
            exit;
            }
        } else {
            $_SESSION['error'] = "Invalid access code";
            header('Location: ClassHomePage.php');
            exit;
        }
    }

?>