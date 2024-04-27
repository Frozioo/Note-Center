<!-- 
    TODO:
    - Reformat the page to look better
 -->

<?php
    session_start();
    require "/home/group9/connections/connect.php";
    $email = $_SESSION['email'];
    $classID = $_GET["ClassID"];

    $adminCheck = "select * from Admin where AdminUserName = :email";
    $tempVar = $conn->prepare($adminCheck);
    $tempVar->bindParam(':email', $_SESSION['email']);
    $tempVar->execute();
    $admin = false;
    if ($tempVar->rowCount() > 0) {
    $admin = true;
    }

    // Check if the user is a student in the class
    $studentCheck = "select * from Student where StudentUserName = :email";
    $stmt = $conn->prepare($studentCheck);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $studentID = $student['StudentID'];
        $stmt = $conn->prepare("select * from StudentClass where StudentID = :StudentID and ClassID = :classID");
        $stmt->bindParam(':StudentID', $studentID);
        $stmt->bindParam(':classID', $classID);
        $stmt->execute();
        $userClass = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Check if the user is the teacher of the class
    $teacherCheck = "select * from Teacher where TeacherUserName = :email";
    $stmt = $conn->prepare($teacherCheck);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teacher || $admin) {
        $teacherID = $teacher['TeacherID'];
        $stmt = $conn->prepare("select * from Class where TeacherID = :TeacherID and ClassID = :classID");
        $stmt->bindParam(':TeacherID', $teacherID);
        $stmt->bindParam(':classID', $classID);
        $stmt->execute();
        $teacherClass = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
    if (!$admin){
        if (!$userClass && !$teacherClass) {
            // If the user is not in the class and is not the teacher of the class, redirect them to the ClassHomePage
            header("Location: ClassHomePage.php");
            exit;
        }
    }
    

    $stmt = $conn->prepare("select * from Class where ClassID = :classID");
    $stmt->bindParam(':classID', $classID);
    $stmt->execute();
    $class = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheet.css">
    <link rel="icon" href="./assets/notecenterlogo.png">
    <title><?php echo $class["ClassName"];?></title>
</head>
<body class="class-body">
    <nav>
        <ul>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <?php
            if ($admin){
                echo '<li class="hideWhenSmall"><a href="ContactForms.php">Admin Portal</a></li>';
            }
            if ($teacher || $admin){
                echo '<li class="hideWhenSmall"><a href="teacherStudentList.php?classID=' . $classID . '">Class Roster</a></li>';
                //Need to make this get the roster of current class
            }
            ?>
        
         
            <li class="hideWhenSmall"><a href="ContactPage.php">Contact Support</a></li>
            <li class="hideWhenSmall"><a href="profile.php">Profile</a></li>
            <li class="hideWhenSmall"><a href="logout.php">Logout</a></li>
            <li class="menu-button" onclick=showSideBar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg></a></li>
        </ul>
        <ul class="sidebar">
            <li onclick=hideSidebar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a></li>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <?php
            if ($is_admin){
            echo '<li><a href="ContactForms.php">Admin Portal</a></li>';
            }
            if ($teacher){
                echo '<li><a href="teacherStudentList.php?classID=' . $classID . '">Class Roster</a></li>';
            }
            ?>
            <li><a href="ContactPage.php">Contact Support</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Now only teachers can see TeacherID/AccessCode -->
  
    <p class = 'classHeader'><strong></strong><b> <?php echo $class["ClassName"]; ?></b></p>
    <p class = 'classHeader'><?php echo $class["ClassDescription"]; ?></p>
    <p class = "classInfoOne"><?php if ($admin){ echo '<strong>Class ID:</strong> ' . $class["ClassID"];} ?></p>
    <p class = "classInfoTwo"><?php if ($teacher || $admin){echo '<strong>Teacher ID: </strong>' . $class["TeacherID"];} ?></p>
    <p class = "classInfoThree"><?php if ($teacher || $admin){echo '<strong>Access Code: </strong>' . $class["accessCode"];} ?></p>

    <?php
        // session_start();
        // require "/home/group9/connections/connect.php";

        // $classID = $_GET["ClassID"];

        // $stmt = $conn->prepare("select * from Class where ClassID = :classID");
        // $stmt->bindParam(':classID', $classID);
        // $stmt->execute();
        // $class = $stmt->fetch(PDO::FETCH_ASSOC);

        // $studentID = $_SESSION['email'];
        // $stmt = $conn->prepare("select * from Notes where StudentUserName = :studentID and ClassID = :classID");
        // $stmt->bindParam(':studentID', $studentID);
        // $stmt->bindParam(':classID', $classID);
        // $stmt->execute();
        // $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        session_start();
        require "/home/group9/connections/connect.php";
    
        $classID = $_GET["ClassID"];
    
        $stmt = $conn->prepare("select * from Class where ClassID = :classID");
        $stmt->bindParam(':classID', $classID);
        $stmt->execute();
        $class = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($teacher || $admin) {
            $stmt = $conn->prepare("select * from Notes where ClassID = :classID");
        } else {
            $studentID = $_SESSION['email'];
            $stmt = $conn->prepare("select * from Notes where StudentUserName = :studentID and ClassID = :classID");
            $stmt->bindParam(':studentID', $studentID);
        }
    
        $stmt->bindParam(':classID', $classID);
        $stmt->execute();
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        

    ?>
    <!-- Fixed the classID error, don't change this -->

    <?php
    $stmtQuery = "select CanUploadNotes from Student where StudentUserName=:email";
    $stmtUpload = $conn->prepare($stmtQuery);
    $stmtUpload->bindParam(':email', $email);
    $stmtUpload->execute();
    
    $CanUploadNotes = $stmtUpload->fetchColumn();

    if ($CanUploadNotes == 1){
        echo '<div id="uploadNotes">';
        echo "<form action='upload.php' method='post' enctype='multipart/form-data' onsubmit=\"return confirm('Are you sure you want to submit these notes?');\">";
        echo "<input type='hidden' name='ClassID' value=" . $classID . " ?'>";
        echo '<label for="fileToUpload" class="browseButton">Browse Files</label>';
        echo '<input type="file" name="fileToUpload" id="fileToUpload" class="selectFileButton">';
        echo '<input type="submit" value="Upload File" name="submit" class="uploadButton">';
        echo '</form>';
        echo '</div>';
    }
    ?>
    
    <button id="toggleNotesButton" class = "showNotesButton">Show Notes</button>
        

<?php
//Delete Class Button for Teachers
if ($teacher || $admin){
echo "<form action='deleteClass.php'  method='post' onsubmit=\"return confirm('Pressing OK will delete this class PERMANENTLY!');\">";
echo "<input type='hidden' name='ClassID' value='$classID'>";
echo "<input type='submit' class='deleteClassButton' value='Delete Class'>";
echo "</form>";
}
?>

<script>
    document.getElementById('toggleNotesButton').addEventListener('click', function() {
        var notesDisplay = document.getElementById('notesDisplay');
        var button = document.getElementById('toggleNotesButton');

        if (notesDisplay.style.display == 'none') {
            notesDisplay.style.display = 'block';
            button.textContent = 'Hide Notes';
        } else {
            notesDisplay.style.display = 'none';
            button.textContent = 'Show Notes';
        }
    });
</script>
    <div class="notesDisplay">
        <div id="notesDisplay" style="display: none;">
        <?php
            // Sort the notes by their upload date
            usort($notes, function($a, $b) {
                return strtotime($b['UploadDate']) - strtotime($a['UploadDate']);
            });

            foreach ($notes as $note) {
                $filePath = $note['NotePath'];
                $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                $studentUserName = $note['StudentUserName'];
                $uploadDate = $note['UploadDate'];


                // Delete button for each note
                echo "<form action='delete.php' method='post' onsubmit=\"return confirm('Are you sure you want to delete these notes?');\">";
                echo "<input type='hidden' name='ClassID' value='$classID'>";
                echo "<input type='hidden' name='notePath' value='$filePath'>";
                echo "<br>";
                echo "<input type='submit' value='Delete Note' class='deleteButton'>";
                echo "<span class='uploadedBy'>Uploaded by: " . htmlspecialchars($studentUserName) . "</span>";
                echo "<span class='uploadedDate'>Uploaded on: " . htmlspecialchars($uploadDate) . "</span>";
                echo "</form>";

                if ($fileExtension == 'txt') {
                    // Display text file content
                    $content = file_get_contents($filePath);
                    echo "<pre>" . htmlspecialchars($content) . "</pre>";
                } elseif ($fileExtension == 'pdf') {
                    // Display PDF in the browser
                    $url = str_replace('/home/group9/public_html', 'https://turing.cs.olemiss.edu/~group9/', $filePath);
                    echo "<iframe src='$url' width='49.9999%' height='499.9999px'></iframe>";
                } elseif ($fileExtension == 'docx') {
                    // Display Word document in the browser
                    $url = str_replace('/home/group9/public_html', 'https://turing.cs.olemiss.edu/~group9/', $filePath);
                    echo "<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=$url' width='33.33333333%' height='333.33333px'></iframe>";
                }
            }
        ?>
        </div>
    </div>

</body>
</html>