<?php
    require '/home/group9/connections/connect.php';

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

?>

<!DOCTYPE hmtl>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheet.css">
    <link rel="icon" href="./assets/notecenterlogo.png">
	<title>Admin Portal</title>
</head>

<body>

<nav>
        <ul>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <li class="hideWhenSmall"><a href="adminStudentList.php">Student List</a></li>
            <li class="hideWhenSmall"><a href="adminTeacherList.php">Teacher List</a></li>
            <li class="hideWhenSmall"><a href="ContactPage.php">Contact Support</a></li>
            <li class="hideWhenSmall"><a href="logout.php">Logout</a></li>
            <li class="menu-button" onclick=showSideBar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg></a></li>
        </ul>
        <ul class="sidebar">
            <li onclick=hideSidebar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a></li>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <li><a href="adminStudentList.php">Student List</a></li>
            <li><a href="adminTeacherList.php">Teacher List</a></li>
            <li><a href="ContactPage.php">Contact Support</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="adminUserList.php">Admin</a></li>
        </ul>
    </nav>


<div class = "adminUserListStyle"> 


</body>
</html>
