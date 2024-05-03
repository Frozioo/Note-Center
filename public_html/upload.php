<?php
    session_start();
    require "/home/group9/connections/connect.php";
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        //Choosing the directory based on what class you are currently in
    $classID = $_POST["ClassID"];
    $target_dir = "/home/group9/public_html/Notes/" . $classID . "/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        //Try to upload the file if it doesn't already exist in the class
    try {
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        if($fileType != "pdf" && $fileType != "doc" && $fileType != "docx" && $fileType != "txt") {
            echo "Sorry, only PDF, DOC, DOCX & TXT files are allowed.";
            $uploadOk = 0;
        }
        if ($_FILES['fileToUpload']['error']) {
            echo 'Upload error: ' . $_FILES['fileToUpload']['error'];
        }
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";

                $email = $_SESSION['email'];
                $classID = $_POST["ClassID"];
                $stmt = $conn->prepare("insert into Notes (StudentUserName, ClassID, NotePath) VALUES (:email, :classID, :notePath)");
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':classID', $classID);
                $stmt->bindParam(':notePath', $target_file);
                $stmt->execute();
                header("Location: classPage.php?ClassID=" . $classID);
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}
?>