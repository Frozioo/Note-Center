<!-- 
    Will be used to display the user's profile information
    Allows the user to change their password
-->

<?php
    require "/home/group9/connections/connect.php";
    
    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit;
    }

    $adminCheck = "select * from Admin where AdminUserName = :email";
    $stmt = $conn->prepare($adminCheck);
    $stmt->bindParam(':email', $_SESSION['email']);
    $stmt->execute();
    $is_admin = false;
    if ($stmt->rowCount() > 0) {
        $is_admin = true;
    }
    
    // $stmt = conn->query('SELECT * FROM Student WHERE StudentUserName = :email');
    // $stmt->bind_param(':email', $_SESSION["email"]);
    // $stmt->execute();
    // $queryResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_SESSION["email"];
        $newPassword = $_POST["newPassword"];
        $newPassword2 = $_POST["newPassword2"];

        if($newPassword == $newPassword2){
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Student SET StudentPassword = :newPassword WHERE StudentUserName = :email");
            $stmt->bindParam(':newPassword', $hashedPassword);
            $stmt->bindParam(':email', $email);
            if($stmt->execute()){
                header('Location: index.php');
                exit;
            } else{
                $message = "Failed to update password.";
                echo $message;
            }
        } else{
            $message = "Passwords do not match";
            echo $message;
        }

        
        
    }    
?>    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheet.css">
    <link rel="icon" href="./assets/notecenterlogo.png">
    <title>Contact Support</title>
    <style>
        body {
            /* font-family: Arial, sans-serif; */
            line-height: 1.6;
            /* The margin is causing the header to be misaligned */
            /* margin: 6px; */
        }
        h1, h2, h3 {
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <nav>
        <ul>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <?php
                if ($is_admin){
                    echo '<li class="hideWhenSmall"><a href="ContactForms.php">Admin Portal</a></li>';
                }
            ?>
            <li class="hideWhenSmall"><a href="ContactPage.php">Contact Support</a></li>
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
            ?>
            <li><a href="ContactPage.php">Contact Support</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    
    <div>
        <h1>Profile Information</h1>
        <?php

            $students = "select * from Student where StudentUserName = :email";
            $stmt = $conn->prepare($students);
            $stmt->bindParam(':email', $_SESSION['email']);
            $stmt->execute();
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            //echo $student['FirstName'];

            $teachers = "select * from Teacher where TeacherUserName = :email";
            $stmt = $conn->prepare($teachers);
            $stmt->bindParam(':email', $_SESSION['email']);
            $stmt->execute();
            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student){
                echo 'ID: ' . $student["StudentID"] . '<br>';
                echo 'First name: ' . $student["FirstName"] . '<br>';
                echo 'Last name: ' . $student["LastName"] . '<br>';
                echo 'Email: ' . $student['StudentUserName'] . '<br>';
            } else {
                echo 'ID: ' . $teacher["TeacherID"] . '<br>';
                echo 'First name: ' . $teacher["FirstName"] . '<br>';
                echo 'Last name: ' . $teacher["LastName"] . '<br>';
                echo 'Email: ' . $teacher["TeacherUserName"] . '<br>';
            } 
        ?>
    </div>
    

    <h2> Reset Password</h2>
    <p>Click the link below to get instructions emailed to reset your password.<p>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" class="form-container">   
        <label for="newPassword">New Password:</label><br>
        <input type="password" id="newPassword" name="newPassword" placeholder="Enter New Password" required><br>
    
        <label for="newPassword2">Confirm Password:</label><br>
        <input type="password" id="newPassword2" name="newPassword2" placeholder="Confirm Password" required><br>
        <input type="submit" value="Submit" class="submit-button">
    </form>

    <script>
        function showSideBar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.display = 'flex';
        }
        function hideSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.display = 'none';
        }
    </script>
</body>
</html>