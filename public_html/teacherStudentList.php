<?php
    require '/home/group9/connections/connect.php';

    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit;

    $stmt = $conn->prepare("select * from Class where ClassID = :classID");
    $stmt->bindParam(':classID', $classID);
    $stmt->execute();
    $class = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    $classID = $_GET['classID'];
?>

<!DOCTYPE hmtl>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheet.css">
    <link rel="icon" href="./assets/notecenterlogo.png">
	<title>Class Roster</title>
</head>

<body>

<nav>
        <ul>
            <li><a href="ClassHomePage.php">Note Center</a></li>
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


<div class = "adminUserListStyle"> 

<div class = "tableHeaders">
    <b><u>Students</u></b>
</div>

<!-- Toggles a students ability to upload notes. -->

<?php
    if(isset($_POST['StudentID']) && isset($_POST['CanUploadNotes']))
    {
        $sID = $_POST['StudentID'];
        if ($_POST['CanUploadNotes'] == 1){
            $query2 = ("update Student set CanUploadNotes=0 where StudentID=?");
            $qr = $conn->prepare($query2);
            $qr->execute([$sID]);
        } else {
            $query2 = ("update Student set CanUploadNotes=1 where StudentID=?");
            $qr = $conn->prepare($query2);
            $qr->execute([$sID]);
        }  
    } elseif (isset($_POST['StudentID'])) {
        $sID = $_POST['StudentID'];
        $queryFindUser = ("select StudentUserName from Student where StudentID=?");
        $qrFindUser = $conn->prepare($queryFindUser);
        $qrFindUser->execute([$sID]);
        $userName = $qrFindUser->fetch(PDO::FETCH_ASSOC);

        $queryDelNotes = ("delete from Notes where StudentUserName=?");
        $qrDelNotes = $conn->prepare($queryDelNotes);
        $qrDelNotes->execute([$userName['StudentUserName']]);

        $query2 = ("delete from StudentClass where StudentID=?");
        $qr = $conn->prepare($query2);
        $qr->execute([$sID]);
    }
?>

<!-- Student Table-->

<table class = 'tableAlignments'>
    <?php        
        //Need to input whatever class it is currently pulling from.
        $query = "select * from Student inner join StudentClass on Student.StudentID = StudentClass.StudentID where StudentClass.ClassID = ?";
        $qr = $conn->prepare($query);
        $qr->execute([$classID]);
        if ($qr->rowCount() > 0){
            echo '<tr>';
            echo '<th>StudentID</th>';
            echo '<th>Student Email</th>';
            echo '<th>Student First Name</th>';
            echo '<th>Student Last Name</th>';
            echo '<th>Can Upload Notes</th>';
            echo '</tr>';
	    while ($row = $qr->fetch()){
 		    echo "<tr>";
		    echo "<td><input type=text name = 'StudentID' class = 'tableFonts' disabled value = '". $row['StudentID']."'></td>";
			echo "<td><input type=text name = 'StudentUserName' class = 'tableFonts' disabled value = '". $row['StudentUserName']."'></td>";
		    echo "<td><input type=text name = 'StudentFNAME' class = 'tableFonts' disabled value = '". $row['FirstName']."'></td>";
		    echo "<td><input type=text name = 'StudentLNAME' class = 'tableFonts' disabled value = '". $row['LastName']."'></td>";
            if ($row['CanUploadNotes'] == 1){
                echo "<td><input type=bool name = 'CanUploadNotes' class = 'tableFonts' disabled value = '". 'Can Upload'."'></td>";
            } else {
                echo "<td><input type=bool name = 'CanUploadNotes' class = 'tableFonts' disabled value = '". 'Can NOT Upload'."'></td>";
            }
            echo "<td>";
            echo "<form action = 'teacherStudentList.php?classID=$classID' method = 'POST' onsubmit=\"return confirm('Update this students note permissions?');\">";
            echo "<input type = 'hidden' name = 'StudentID' value = '" . $row['StudentID'] . "'>";
            echo "<input type = 'hidden' name = 'CanUploadNotes' value = '" . $row['CanUploadNotes'] . "'>";
            echo "<input type=submit class = 'buttonFonts' value='Toggle Upload Permissions '>";
            echo "</form>";
            echo "</td>";
            echo "<td>";
            echo "<form action = 'teacherStudentList.php?classID=$classID' method = 'POST' onsubmit=\"return confirm('Remove this student from this class?');\">";
            echo "<input type = 'hidden' name = 'StudentID' value = '" . $row['StudentID'] . "'>";
            echo "<input type=submit class = 'buttonFonts' value='Remove from Class'>";
            echo "</form>";
            echo "</td>";
		    echo "</tr>";
        }
    } else {
        echo '<div class = "notInClass">There are currently no students in this class. Provide the access code for them to join!</div>';
    }
    ?>
</table>
</body>
</html>
