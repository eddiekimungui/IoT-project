<?php session_start();
if(!isset($_SESSION['username'])){
	//redirect to log in page
	header("location: login.php");
	exit();
 }
$servername = "localhost";
$dBUsername = "user name";;
$dBPassword = "database password";
$dBName = "database name";





$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);

if (!$conn) {
	die("Failed connection: ".mysqli_connect_error());
}


if (isset($_POST['toggle_door'])) {
	$sql = "SELECT * FROM door_status;";
	$result   = mysqli_query($conn, $sql);
	$row  = mysqli_fetch_assoc($result);
	
	if($row['status'] == 0){
		$update = mysqli_query($conn, "UPDATE door_status SET status = 1 WHERE id = 1;");
	
}		
	else{
		$update = mysqli_query($conn, "UPDATE door_status SET status = 0 WHERE id = 1;");		
	}
}

if(isset($_POST['RESET'])){
	$sql = "SELECT * FROM door_status;";
	$result   = mysqli_query($conn, $sql);
	$row  = mysqli_fetch_assoc($result);
	if($row['r_access'] == 1){	
		$update = mysqli_query($conn, "UPDATE door_status SET r_access = 0 WHERE id = 1;");		
	}
}


$sql = "SELECT * FROM door_status;";
$result   = mysqli_query($conn, $sql);
$row  = mysqli_fetch_assoc($result);	
?>

<style>
	.wrapper{
		width: 100%;
		padding-top: 50px;
	}
	.col_3{
		width: 33.3333333%;
		float: left;
		min-height: 1px;
	}
	#submit_button{
		background-color: #2bbaff; 
		color: #FFF; 
		font-weight: bold; 
		font-size: 40; 
		border-radius: 15px;
    	text-align: center;
    	margin:5px;
	}
	#submit_button2{
		background-color: #00FF00; 
		color: #FFF; 
		font-weight: bold; 
		font-size: 20; 
		border-radius: 5px;
    	text-align: center;
	}
	.door_img{
		height: 240px;		
		width: 100%;
		object-fit: cover;
		object-position: center;
	}
	
	@media only screen and (max-width: 600px) {
		.col_3 {
			width: 100%;
		}
		.wrapper{
			width: 100%;
			padding-top: 5px;
		}
		.door_img{
			height:320px;		
			width: 90%;
			margin-right: 15%;
			margin-left: 15%;
			object-fit: cover;
			object-position: center;
		}
	}
         button{
            position: absolute;
         	top: 0;
         	right: 0;
         	background-color: #2bbaff; 
         	color: #FFF;
         }
         body {
        background-color: #f1f1f1; 
       
      }

</style>

<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
	<div class="wrapper" id="refresh">
		<button type="button" onclick="location.href='logout.php'">logout</button>
		<div class="col_3">
		</div>

		<div class="col_3">
			
			<?php echo '<h1 style="text-align: center;">The status of the door is: '.$row['status'].'</h1>';?>
			<?php if ($row['r_access']==1) {
                  echo '<h1 style="text-align: center;">Someone is at the door </h1>';
			}  ?>                                                
			<div class="col_3">
			</div>
			
			<div class="col_3" style="text-align: center;">
			<form action="index.php" method="post" id="DOOR" enctype="multipart/form-data">			
				<input id="submit_button" type="submit" name="toggle_door" value="Toggle door" />
				<input id="submit_button2" type="submit" name="RESET" value="RESET" />
			</form>
				
			<script type="text/javascript">
			$(document).ready (function () {
				var updater = setTimeout (function () {
					$('div#refresh').load ('index.php', 'update=true');
				}, 1000);
			});
			</script>
			<br>
			<br>
			<?php
				if($row['status'] == 0){?>
				<div class="door_img">
					<img id="contest_img" src="img/door_closed.png" width="100%" height="100%">
				</div>
			<?php	
				}
			
				else{ ?>
				<div class="door_img">
					<img id="contest_img" src="img/door_open.png" width="100%" height="100%">
					<p><?php echo date($row['created_at']); ?></p>
				</div>
			<?php
				}
			?>
			
				
				
				
			</div>
				
			<div class="col_3">
			</div>
		</div>

		<div class="col_3">
		</div>
	</div>
</body>
</html>

</html>