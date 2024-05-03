<?php
    require '/home/group9/connections/connect.php';
    //If user did not log in properly kick them to login page
    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit;
    }
    //Checking if the user is an admin and kicking them to class page if they try to access this page w/out being an admin
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

<!-- Standard HTML -->

<!DOCTYPE hmtl>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheet.css">
    <link rel="icon" href="./assets/notecenterlogo.png">
	<title>Admin Student List</title>
</head>

<body>

<nav>
        <ul>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <li class="hideWhenSmall"><a href="ContactForms.php">Admin Portal</a></li>
            <li class="hideWhenSmall"><a href="ContactPage.php">Contact Support</a></li>
            <li class="hideWhenSmall"><a href="logout.php">Logout</a></li>
            <li class="menu-button" onclick=showSideBar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg></a></li>
        </ul>
        <ul class="sidebar">
            <li onclick=hideSidebar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a></li>
            <li><a href="ClassHomePage.php">Note Center</a></li>
            <li><a href="ContactForms.php">Admin Portal</a></li>
            <li><a href="ContactPage.php">Contact Support</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>


<div class = "adminUserListStyle"> 

<div class = "tableHeaders">
    <b><u>Students</u></b>
</div>

<?php
try {
if ($_SERVER["REQUEST_METHOD"]=="POST")
{
    //Post method for toggling a students ability to upload notes on or off
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
       
    } else {
        //Post method for deleting a student
        $sID = $_POST['StudentID'];

        $queryFindUser = ("select StudentUserName from Student where StudentID=?");
        $qrFindUser = $conn->prepare($queryFindUser);
        $qrFindUser->execute([$sID]);
        $userName = $qrFindUser->fetch(PDO::FETCH_ASSOC);
        //Deleting all of the notes that a student has uploaded
        $queryDelNotes = ("delete from Notes where StudentUserName=?");
        $qrDelNotes = $conn->prepare($queryDelNotes);
        $qrDelNotes->execute([$userName['StudentUserName']]);
        //Deleting all instances of the student from their classes
        $query2 = ("delete from StudentClass where StudentID=?");
        $qr = $conn->prepare($query2);
        $qr->execute([$sID]);
        //Deleting the student
        $query = ("delete from Student where StudentID=?");
        $ps = $conn->prepare($query);
        $ps->execute([$sID]);
    }
}
} catch (Exception $e) {
    echo "Database Error: ", $e->getMessage();
}
?>

<!-- Student Table-->

<table class = 'tableAlignments'>
	<tr>
		<th>StudentID</th>
		<th>Student Email</th>
		<th>Student First Name</th>
		<th>Student Last Name</th>
        <th>Can Upload Notes</th>
	</tr>
    <?php        
	    $query = "select * from Student";
	    $qr = $conn->query($query);
	    while ($row = $qr->fetch()){
 		    echo "<tr>";
		    echo "<td><input type=text name = 'StudentID' class = 'tableFonts' disabled value = '". $row['StudentID']."'></td>";
			echo "<td><input type=text name = 'StudentUserName' class = 'tableFonts' disabled value = '". $row['StudentUserName']."'></td>";
		    echo "<td><input type=text name = 'StudentFNAME' class = 'tableFonts' disabled value = '". $row['FirstName']."'></td>";
		    echo "<td><input type=text name = 'StudentLNAME' class = 'tableFonts' disabled value = '". $row['LastName']."'></td>";
            //Transforming the databases 0/1 value to show whether a student can or can not upload notes.
            if ($row['CanUploadNotes'] == 1){
                echo "<td><input type=bool name = 'CanUploadNotes' class = 'tableFonts' disabled value = '". 'Can Upload'."'></td>";
            } else {
                echo "<td><input type=bool name = 'CanUploadNotes' class = 'tableFonts' disabled value = '". 'Can NOT Upload'."'></td>";
            }
            try {
                //Form for the toggling student's upload permissions
            echo "<td>";
            echo "<form action = 'adminStudentList.php' method = 'POST' onsubmit=\"return confirm('Update this students note Permissions?');\">";
            echo "<input type = 'hidden' name = 'StudentID' value = '" . $row['StudentID'] . "'>";
            echo "<input type = 'hidden' name = 'CanUploadNotes' value = '" . $row['CanUploadNotes'] . "'>";
            echo "<input type=submit class = 'buttonFonts' value='Toggle Upload Permissions '>";
            echo "</form>";
            echo "</td>";
            } catch (Exception $e){
                echo "Database Error: ", $e->getMessage();
            }
            //Form for the delete button on the table
            echo "<td>";
            echo "<form action = 'adminStudentList.php' method = 'POST' onsubmit=\"return confirm('Remove this student permanently?');\">";
            echo "<input type = 'hidden' name = 'StudentID' value = '" . $row['StudentID'] . "'>";
            echo "<input type=submit class = 'buttonFonts' value='Delete'>";
            echo "</form>";
            echo "</td>";
		    echo "</tr>";
        }
        
        
    ?>
</table>
</body>
</html>
