<?php
$servername = "localhost";
$dBUsername = "user name";
$dBPassword = "database password";
$dBName = "database name";
$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);
if (!$conn) {
	die("Connection failed: ".mysqli_connect_error());
}

//Read the database
if (isset($_POST['check_DOOR_status'])) {
	$door_id = $_POST['check_DOOR_status'];	
	$sql = "SELECT * FROM door_status WHERE id = '$door_id';";
	$result   = mysqli_query($conn, $sql);
	$row  = mysqli_fetch_assoc($result);
	if($row['status'] == 0){
		echo "DOOR_is_closed";
	}
	else{
		echo "DOOR_is_open";
	}	
}	

//Update the database whenever there is a break in
if (isset($_POST['toggle_DOOR'])) {
	$door_id = $_POST['toggle_DOOR'];	
	$sql = "SELECT * FROM door_status WHERE id = '$door_id';";
	$result   = mysqli_query($conn, $sql);
	$row  = mysqli_fetch_assoc($result);
	if($row['status'] == 0){
		$update = mysqli_query($conn, "UPDATE door_status SET status = 1 WHERE id = 1;");
		echo "DOOR_is_open";
	}
	
}
//update database whenever someone requests access
if(isset($_POST['request_access']))	{
	$door_id = $_POST['request_access'];
	$sql = "SELECT * FROM door_status WHERE id = '$door_id';";
	$result   = mysqli_query($conn, $sql);
	$row  = mysqli_fetch_assoc($result);
	if($row['r_access'] == 0){
		$update = mysqli_query($conn, "UPDATE door_status SET r_access = 1 WHERE id = 1;");
	}
}
//else do nothing
else{

}
?>