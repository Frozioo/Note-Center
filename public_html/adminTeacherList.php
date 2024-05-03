<?php
    require '/home/group9/connections/connect.php';
    //If user did not log in properly kick them to login page
    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit;
    }
    //Checking if user is an admin, kick them back to main page if not an admin
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
	<title>Admin Teacher List</title>
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
    <b><u>Teachers</u></b>
</div>

<?php
try {
if ($_SERVER["REQUEST_METHOD"]=="POST")
{
    //Post method for deleting a teacher from the database
   if (isset($_POST['TeacherID'])){
        $tID = $_POST['TeacherID'];
        $temp = ("select ClassID from Class where TeacherID=?");
        $tempVar3 = $conn->prepare($temp);
        $tempVar3->execute([$tID]);
        $classes = $tempVar3->fetchAll(PDO::FETCH_COLUMN);
    //Deleting every student out of the class that a teacher teaches
        foreach ($classes as $classID) {
            $temp2 = "delete from StudentClass where ClassID=?";
            $tempVar2 = $conn->prepare($temp2);
            $tempVar2->execute([$classID]);
        }
    //Deleting the class after all students have been removed
        $query2 = ("delete from Class where TeacherID=?");
        $qr = $conn->prepare($query2);
        $qr->execute([$tID]);
    //Deleting the teacher
        $query = ("delete from Teacher where TeacherID=?");
        $ps = $conn->prepare($query);
        $ps->execute([$tID]);

    } 
}
} catch (Exception $e) {
    echo "Database Error: ", $e->getMessage();
}

?>

<!-- Teacher Table -->

<table class = 'TeacherTable'>
	<tr>
		<th>TeacherID</th>
		<th>Teacher Email</th>
		<th>Teacher First Name</th>
		<th>Teacher Last Name</th>
	</tr> 
<?php
	$query = "select * from Teacher";
	$qr = $conn->query($query);
	while ($row = $qr->fetch())
	{
        echo "<tr>";
        echo "<td><input type=text name = 'TeacherID' class = 'tableFonts' disabled value = '". $row['TeacherID']."'></td>";
        echo "<td><input type=text name = 'TeacherUserName' class = 'tableFonts' disabled value = '". $row['TeacherUserName']."'></td>";
        echo "<td><input type=text name = 'TeacherFNAME' class = 'tableFonts' disabled value = '". $row['FirstName']."'></td>";
        echo "<td><input type=text name = 'TeacherLNAME' class = 'tableFonts' disabled value = '". $row['LastName']."'></td>";
        echo "<td>";
        //Form for deleting a teacher from the database
        echo "<form action = 'adminTeacherList.php' method = 'POST' onsubmit=\"return confirm('Remove this teacher permanently?');\">";
        echo "<input type = 'hidden' name = 'TeacherID' value = '" . $row['TeacherID'] . "'>";
        echo "<input type=submit class = 'buttonFonts' value='Delete'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
	}	
?>
</table>
</body>
</html>
