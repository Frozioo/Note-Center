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
    // Testing to see if it shows that correct user is logged in. Remove later.
    // echo "Logged in as: " . $_SESSION['email'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstName = htmlspecialchars($_POST['firstName']);
        $lastName = htmlspecialchars($_POST['lastName']);
        $email = htmlspecialchars($_POST['email1']);
        $subject = htmlspecialchars($_POST['subject1']);
        $message = htmlspecialchars($_POST['message1']);
        $classID = htmlspecialchars($_POST['classID']);

        $stmt = $conn->prepare("INSERT INTO ContactForms (FirstName, LastName, Email, Subject1, Message1, ClassID) VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->execute([$firstName, $lastName, $email, $subject, $message, $classID]);
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
            margin: 0px;
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
            ?>
            <li><a href="ContactPage.php">Contact Support</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <h1>Contact Administrator</h1>
    <p>If you have any questions, feedback, or issues, please feel free to contact the administrator using the form below:</p>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" class="form-container">

        <label for="firstName">First Name:</label><br>
        <input type="text" id="firstName" name="firstName" placeholder="Enter First Name" required><br>

        <label for="lastName">Last Name:</label><br>
        <input type="text" id="lastName" name="lastName" placeholder="Enter Last Name" required><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email1" name="email1" placeholder="Enter Email" required><br>

        <label for="Subject">Subject:</label><br>
        <input type="text" id="subject1" name="subject1" placeholder="Enter Subject" required><br>

        <label for="Subject">ClassID:</label><br>
        <input type="text" id="classID" name="classID" placeholder="Enter ClassID" required><br>

        <label for="message">Message:</label>
        <textarea id="message1" name="message1" rows="5" required></textarea>
        <button type="submit" class="submit-button">Submit</button>

        
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