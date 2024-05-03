<?php
    require "/home/group9/connections/connect.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Information requried to create a new account and assigning them to variables
        $userType = $_POST["userType"];
        $id = $_POST["id"];
        $uploadnotes = true;
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $password2 = $_POST["password2"];
        $empty = true;
        
        if ($password != $password2) {
            $empty = false;
            $message = "Passwords do not match";
        } else {
            try {
                // Added code to check if user already exists
                if ($userType == "Student") {
                    $stmt = $conn->prepare("select * from Student where StudentUserName = :email or StudentID = :id");
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                } elseif ($userType == "Teacher") {
                    $stmt = $conn->prepare("select * from Teacher where TeacherUserName = :email or TeacherID = :id");
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                if ($user) {
                    $empty = false;
                    $message = "User already exists";
                } else {
                    //Creating student account
                    if ($userType == "Student") {
                        $stmt = $conn->prepare("insert into Student (StudentID, StudentUserName, StudentPassword, CanUploadNotes, 
                        FirstName, LastName) values (:id, :email, :password, :uploadnotes, :firstname, :lastname)");
                        $stmt->bindParam(':id', $id);
                        $stmt->bindParam(':email', $email);
                        $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
                        $stmt->bindParam(':uploadnotes', $uploadnotes);
                        $stmt->bindParam(':firstname', $firstname);
                        $stmt->bindParam(':lastname', $lastname);
                        $stmt->execute();
                        header("Location: index.php");
                    } elseif ($userType == "Teacher") {
                        //Creating teacher account
                        $stmt = $conn->prepare("insert into Teacher (TeacherID, TeacherUserName, TeacherPassword, LastName, FirstName) values (:id, :email, :password, :lastname, :firstname)");
                        $stmt->bindParam(':id', $id);
                        $stmt->bindParam(':email', $email);
                        $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
                        $stmt->bindParam(':lastname', $lastname);
                        $stmt->bindParam(':firstname', $firstname);
                        $stmt->execute();
                        header("Location: index.php");
                    }
                }
            } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheet.css">
    <link rel="icon" href="./assets/notecenterlogo.png">
    <title>Sign Up</title>
</head>
<body class="login-body">
    <div class="header">
        <img src="./assets/notecenter.png" width="700" height="200">
    </div>
    <div class="form-container">
        <h2>Sign Up</h2>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
            <br><label for="userType">I am a:</label><br>
            <input type="radio" id="student" name="userType" value="Student" required>
            <label for="student">Student</label>

            <input type="radio" id="teacher" name="userType" value="Teacher" required>
            <label for="teacher">Teacher</label><br>

            <label for="id">ID Number:</label><br>
            <input type="text" id="id" name="id" placeholder="Enter ID Number" required><br>

            <label for="firstname">First Name:</label><br>
            <input type="text" id="firstname" name="firstname" placeholder="Enter First Name" required><br>

            <label for="lastname">Last Name:</label><br>
            <input type="text" id="lastname" name="lastname" placeholder="Enter Last Name" required><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" placeholder="Enter Email" required><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" placeholder="Enter Password" required><br>
            <label for="password2">Confirm Password:</label><br>
            <input type="password" id="password2" name="password2" placeholder="Confirm Password" required><br>
            <?php if(!$empty) {echo "<div class='error'>".$message."</div>";} ?>

            <input type="submit" value="Sign Up" class="signup-button2">
        </form>
        <a href="index.php">Already have an account? Login</a>
    </div>
</body>
<footer>
    <div class="footer">
        <p>Copyright © 2024 Group 9</p>
    </div>
</html>