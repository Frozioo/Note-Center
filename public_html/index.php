<?php
    require '/home/group9/connections/connect.php';
 	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $empty = true;
        if (empty($email) || empty($password)) {
            $empty = false;
            $message = "Email or Password is empty";
        } else {
            // Student login
            $stmt = $conn->prepare("select * from Student where StudentUserName = :email");
            $stmt->bindParam(':email', $email);
            // $stmt->bindParam(':password', $password);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && password_verify($password, $row['StudentPassword']) || $row && $password == $row['StudentPassword']) {
                session_start();
                $_SESSION['email'] = $row['StudentUserName'];
                header("Location: ClassHomePage.php");
            } else {
                // Teacher login
                $stmt = $conn->prepare("select * from Teacher where TeacherUserName = :email");
                $stmt->bindParam(':email', $email);
                // $stmt->bindParam(':password', $password);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && password_verify($password, $row['TeacherPassword']) || $row && $password == $row['TeacherPassword']) {
                    session_start();
                    $_SESSION['email'] = $row['TeacherUserName'];
                    header("Location: ClassHomePage.php");
                } else {
                    $stmt = $conn->prepare("select * from Admin where AdminUsername = :email");
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    // Admin login
                    if ($row && $password == $row['AdminPassword']) {
                        session_start();
                        $_SESSION['email'] = $row['AdminUsername'];
                        header("Location: ClassHomePage.php");
                    } else {
                        $empty = false;
                        $message = "Email or Password is incorrect";
                    }
                }   
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
    <title>Note Center</title>
</head>
<body class="login-body">
    <div class="header">
        <img src="./assets/notecenter.png" width="700" height="200">
    </div>
    <div class="form-container">
        <h2>Login Portal</h2>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
            <br><label for="email">Email:</label><br>
            <input type="text" id="email" name="email" placeholder="Enter Email" class="email-input"><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" placeholder="Enter Password" class="password-input">
            <div class='error'>
                <?php if(!$empty) {echo $message;} ?>
            </div>

            <input type="submit" value="Login" name="login" class="login-button">
        </form>
        <form action="signup.php" method="POST">
            <input type="submit" value="Signup" class="signup-button">
        </form>
    </div>
</body>
<footer>
    <div class="footer">
        <p>Copyright © 2024 Group 9</p>
    </div>
</html>

