<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
    require '/home/group9/connections/connect.php';

    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit;
    }
    $adminCheck = "select * from Admin where AdminUserName = :email";
    $stmtAdmin = $conn->prepare($adminCheck);
    $stmtAdmin->bindParam(':email', $_SESSION['email']);
    $stmtAdmin->execute();
    $is_admin = false;
    if ($stmtAdmin->rowCount() > 0) {
        $is_admin = true;
    }

    $teacherCheck = "select * from Teacher where TeacherUserName = :email";
    $stmtTeacher = $conn->prepare($teacherCheck);
    $stmtTeacher->bindParam(':email', $_SESSION['email']);
    $stmtTeacher->execute();
    $is_teacher = false;
    if ($stmtTeacher->rowCount() > 0) {
        $is_teacher = true;
    }
?>

<!DOCTYPE hmtl>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheet.css">
    <link rel="icon" href="./assets/notecenterlogo.png">
	<title>Class Home Page</title>
</head>

<div id="classCreation" class="classCreation">
    <div class="class-content">
        <!-- <span class="close">&times;</span> -->
        <form action="createClass.php" method="post">
                <label for="classID">Class ID</label><br>
                <input type="text" id="classID" name="classID" placeholder='123'><br>
                <label for="className">Class Name</label><br>
                <input type="text" id="className" name="className" placeholder='CSCI 123'><br>
                <label for="classDescription">Class Description</label><br>
                <input type="text" id="classDescription" name="classDescription" placeholder='Computer Science 1'><br>
                <label for="accessCode">Access Code</label><br>
                <input type="text" id="accessCode" name="accessCode" placeholder='12345'><br>
                <p>Note: Only teachers can create classes</p>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<p class="error">' . $_SESSION['error'] . '</p>';
                    unset($_SESSION['error']);
                }
                ?>
                <input type="submit" value="Create">
        </form>
    </div>
</div>

<div id="joinClasses" class="joinClasses">
    <div class="join-content">
        <!-- <span class="close">&times;</span> -->
        <form action="joinClass.php" method="post">
            <label for="accessCode">Access Code</label><br>
            <input type="text" id="accessCode" name="accessCode" placeholder='12345'><br>
            <input type="submit" value="Join">
        </form>
    </div>
</div>

<body class="class-body">
    <nav>
        <ul>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <?php
                if ($is_admin){
                    echo '<li class="hideWhenSmall"><a href="ContactForms.php">Admin Portal</a></li>';
                }
                // if ($is_teacher){
                //     echo '<li class="hideWhenSmall"><a href="#" id="createClass">Create Class</a></li>';
                // }
                if (!$is_admin){
            echo '<li class="hideWhenSmall"><a href="#" id="createClass">Create Class</a></li>';
            echo '<li class="hideWhenSmall"><a href="#" id="joinClass">Join Class</a></li>';
            echo '<li class="hideWhenSmall"><a href="ContactPage.php">Contact Support</a></li>';
            echo '<li class="hideWhenSmall"><a href="profile.php">Profile</a></li>';
                }
            ?>
            <li class="hideWhenSmall"><a href="logout.php">Logout</a></li>
            <li class="menu-button" onclick=showSideBar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg></a></li>
        </ul>
        <ul class="sidebar">
            <li onclick=hideSidebar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a></li>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <?php
                if ($is_admin){
                    echo '<li><a href="adminUserList.php">Admin Portal</a></li>';
                }
                // if ($is_teacher){
                //     echo '<li><a href="#" id="createClass2">Create Class</a></li>';
                // }
            ?>
            <li><a href="#" id="createClass2">Create Class</a></li>
            <li><a href="#" id="joinClass2">Join Class</a></li>
            <li><a href="ContactPage.php">Contact Support</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
            
        </ul>
    </nav>
    <div class="classPageStyle">
        <h1 class="classPageGreeting">My Courses</h1><br>
        <?php
            $email = $_SESSION['email'];
            $studentCheck = "select * from Student where StudentUserName = :email";
            $stmtStudent = $conn->prepare($studentCheck);
            $stmtStudent->bindParam(':email', $email);
            $stmtStudent->execute();
            $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);
            $studentID = $student['StudentID'];

            $query = "select Class.*, Teacher.FirstName, Teacher.LastName from Class join StudentClass on Class.ClassID = StudentClass.ClassID join Teacher on Class.TeacherID = Teacher.TeacherID where StudentClass.StudentID = :studentID";
            $stmtTeacher = $conn->prepare($query);
            $stmtTeacher->bindParam(':studentID', $studentID);
            $stmtTeacher->execute();

            if ($student){
            if ($stmtTeacher->rowCount() > 0) {
                echo '<div class="grid-container">';
                while ($row = $stmtTeacher->fetch()) {
                    echo '<a href="classPage.php?ClassID=' . $row["ClassID"] . '" class="classSelectButton">';
                    echo '<div class="classIDStuff"><b>' . $row["ClassName"] . '</b></div>';
                    echo '<div class="classNameStuff">' . $row["ClassDescription"] . '</div>';
                    echo '<div class="teacherName">Teacher: ' . $row['FirstName'] . ' ' . $row['LastName'] . '</div>';
                    echo '</a>';
                }
                echo '</div>';
            } else {
                echo '<div class = "notInClass">
                You are not currently in any classes!<br><br>
                Please ask your teacher for the access code to their class.
                    </div>';
            }
        }
        ?>

        <?php
            $email = $_SESSION['email'];
            $teacherCheck = "select * from Teacher where TeacherUserName = :email";
            $stmtTeacher2 = $conn->prepare($teacherCheck);
            $stmtTeacher2->bindParam(':email', $email);
            $stmtTeacher2->execute();
            $teacher = $stmtTeacher2->fetch(PDO::FETCH_ASSOC);
            $teacherID = $teacher['TeacherID'];

            $query = "select * from Class where TeacherID = :teacherID";
            $stmtClass = $conn->prepare($query);
            $stmtClass->bindParam(':teacherID', $teacherID);
            $stmtClass->execute();
            if ($teacher){
                if ($stmtClass->rowCount() > 0) {
                    echo '<div class="grid-container">';
                    while ($row = $stmtClass->fetch()) {
                        echo '<a href="classPage.php?ClassID=' . $row["ClassID"] . '" class="classSelectButton">';
                        echo '<div class="classIDStuff"><b>' . $row["ClassName"] . '</b></div>';
                        echo '<div class="classNameStuff">' . $row["ClassDescription"] . '</div>';
                        echo '<div class="teacherName">Teacher: ' . $teacher['FirstName'] . ' ' . $teacher['LastName'] . '</div>';
                        echo '</a>';
                    }
                    echo '</div>';
                }
                else {
                    echo '<div class = "notInClass">
                    You have not created any classes!<br><br>
                    Please use the create class button to create a class.
                        </div>';
                }
            }
        ?>

        <!-- Working on admin view and access to classes -->
        <?php
        if ($is_admin){

            $queryAdmin = "select * from Class";
            $stmtAdminView = $conn->prepare($queryAdmin);
            $stmtAdminView->execute();
            if ($stmtAdminView->rowCount() > 0) {
                echo '<div class="grid-container">';
                while ($row = $stmtAdminView->fetch()) {
                    echo '<a href="classPage.php?ClassID=' . $row["ClassID"] . '" class="classSelectButton">';
                    echo '<div class="classIDStuff"><b>' . $row["ClassName"] . '</b></div>';
                    echo '<div class="classNameStuff">' . $row["ClassDescription"] . '</div>';
                    $query2 = "select * from Teacher where TeacherID=" . $row['TeacherID'];
                    $temp2 = $conn->prepare($query2);
                    $temp2->execute();
                    while ($temp3 = $temp2->fetch()){
                        echo '<div class="teacherName">Teacher: ' . $temp3['FirstName'] . ' ' . $temp3['LastName'] . '</div>';
                    }
                    echo '</a>';
                }
                echo '</div>';
            }
        }
        ?>
    </div>

    
    
    <script>
        var form = document.getElementById("classCreation");
        var btn = document.getElementById("createClass");
        var btnside = document.getElementById("createClass2");
        
        var joinForm = document.getElementById("joinClasses");
        var joinBtn = document.getElementById("joinClass");
        var joinBtnside = document.getElementById("joinClass2");

        btn.onclick = function() {
            form.style.display = "block";
        }

        btnside.onclick = function() {
            form.style.display = "block";
        }

        joinBtn.onclick = function() {
            joinForm.style.display = "block";
        }

        joinBtnside.onclick = function() {
            joinForm.style.display = "block";
        }

        window.onclick = function(event) {
            if (event.target == form) {
                form.style.display = "none";
            }
            if (event.target == joinForm) {
                joinForm.style.display = "none";
            }
        }

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
</div>
</html>
