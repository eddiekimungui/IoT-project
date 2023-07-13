<?php 
session_start();

$servername = "localhost";
$dBUsername = "user name";
$dBPassword = "database password";
$dBName = "database name";


$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);
if (isset($_POST['login'])) 
  {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $stmt = mysqli_prepare($conn, "SELECT * FROM door_status WHERE username = ? AND password = ? ");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $row['username'];
        header('Location: index.php');
    } 
    else {
        echo "<script type='text/javascript'>alert('invalid user name or password!');
		document.location='login.php'</script>";
    }
}
?>
<style>
    .form_c{
        padding-top: 200px;
    }
    .log_form{
        
        padding-top: 70px;
        padding-bottom: 70px;
    }
    .page_title{
        color: deepskyblue;
        font-weight: bold;
        padding-top: 5px;
        padding-bottom: 5px;
        font-size: 20px;
    }
     body {
        background-image: url("img/logwallpaper.jpg");
        background-size: cover;
      }

</style>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>login page</title>
</head>
<body>   
    <center>
        <div class="form_c">
        <center class="page_title">
            log in form
        </center>
    
        <form class="log_form" action="login.php" method="POST">
            <input type="text" id="user" name="username" placeholder="username" required><br><br>
            <input type="password" id="user" name="password" placeholder="password" required><br><br>
            <button type="submit" id="btn" name="login">login</button><br><br>
        </form> 
    </div>
    </center>
</body>
</html>
