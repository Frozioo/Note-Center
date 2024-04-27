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
    
    if (!$is_admin){
        header('Location: ClassHomePage.php');
        exit;
    }
    
    // Retrieve contact form submissions from the database
    $stmt = $conn->query("SELECT * FROM ContactForms");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheet.css">
    <link rel="icon" href="./assets/notecenterlogo.png">
    <title>Contact Form Submissions</title>
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
        table{
            width: 100%;
            border-collapse: collapse;
        }
        th, td{
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }
        th{
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <li><a href="adminStudentList.php">Student List</a></li>
            <li><a href="adminTeacherList.php">Teacher List</a></li>
            <li class="hideWhenSmall"><a href="ContactPage.php">Contact Support</a></li>
            <li class="hideWhenSmall"><a href="logout.php">Logout</a></li>
            <li class="menu-button" onclick=showSideBar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg></a></li>
        </ul>
        <ul class="sidebar">
            <li onclick=hideSidebar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a></li>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <li><a href="ContactPage.php">Contact Support</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <h1>Contact Form Submissions</h1>
<div class="table-container">
    <?php if (count($contacts) > 0): ?>
        <table class="contactTable">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?php echo $contact['FirstName']; ?></td>
                        <td><?php echo $contact['LastName']; ?></td>
                        <td><?php echo $contact['Email']; ?></td>
                        <td><?php echo $contact['ClassID']; ?></td>
                        <td><?php echo $contact['Subject1']; ?></td>
                        <td><?php echo $contact['Message1']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No contact form submissions found.</p>
    <?php endif; ?>
    <div class="delete-buttons">
        <?php foreach ($contacts as $contact): ?>
            <div class="delete-button-row">
                <a href="deleteContact.php?Email=<?php echo $contact['Email']; ?>" class="deleteForm" onclick="return confirm('Are you sure you want to delete this form?')">Delete</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

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
