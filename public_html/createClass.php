<?php
    session_start();

    require "/home/group9/connections/connect.php";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $classID = $_POST["classID"];
        $className = $_POST["className"];
        $accessCode = $_POST["accessCode"];
        $classDescription = $_POST["classDescription"];
        $empty = true;
        $email = $_SESSION["email"];

        $stmt = $conn->prepare("select TeacherID from Teacher where TeacherUserName = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$teacher) {
            // If the user is not a teacher, redirect them to the ClassHomePage
            header("Location: ClassHomePage.php");
            exit;
        }

        $teacherID = $teacher["TeacherID"];

        $stmt = $conn->prepare("select * from Class where ClassID = :classID");
        $stmt->bindParam(':classID', $classID);
        $stmt->execute();
        $class = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($class) {
            $empty = false;
            $_SESSION['error'] = "Class already exists";
            header("Location: ClassHomePage.php");
            exit;
        } else {
            // Check if a class with the same access code already exists
            $stmt = $conn->prepare("select * from Class where accessCode = :accessCode");
            $stmt->bindParam(':accessCode', $accessCode);
            $stmt->execute();
            $sameCode = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($sameCode) {
                $_SESSION['error'] = "Class with the same access code already exists";
                header("Location: ClassHomePage.php");
                exit;
            }

            $stmt = $conn->prepare("insert into Class (ClassID, ClassName, TeacherID, accessCode, ClassDescription) values (:classID, :className, :teacherID, :accessCode, :classDescription)");
            $stmt->bindParam(':classID', $classID);
            $stmt->bindParam(':className', $className);
            $stmt->bindParam(':teacherID', $teacherID);
            $stmt->bindParam(':accessCode', $accessCode);
            $stmt->bindParam(':classDescription', $classDescription);
            $stmt->execute();
            try {
            $targetDirectory = '/home/group9/public_html/Notes';
            $newDirectory = $targetDirectory . '/' . $classID;
            mkdir($newDirectory);
            } catch (Exception $e){
                echo "Database Error: ", $e->getMessage();
            }
            header("Location: ClassHomePage.php");
            exit;
        }
    }

?>