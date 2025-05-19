<?php
include 'koneksi.php';
session_start();
error_reporting(0);

if (isset($_POST['submit'])) {
	$email = $_POST['email'];
	$password = md5($_POST['password']);

	$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
	$result = mysqli_query($mysqli, $sql);
	if ($result->num_rows > 0) {
		$row = mysqli_fetch_assoc($result);
		$_SESSION['email'] = $row['email'];
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['password'] = $row['password'];
		header("Location: ../../index.php");
	} else {
		echo "<script>alert('Woops! Email Atau Password anda Salah.')</script>";
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style2.css ">
</head>
<body>
    <div class="container">
       <form action="" method="POST" class="login-email">
        <p style="font-size: 2rem;font-weight:850;">Login</p>
        <div class="input-group"><input type="text" placeholder="Email" name="email" value="<?php echo $email; ?>"required></div>
        <div class="input-group"><input type="password" placeholder="Password" name="password" value="<?php echo $_POST['$password']; ?>"required></div>
        <div class="input-group"><button name="submit" class="btn">Login</button></div>
        <p class="login-register-text">Don't Have an Account?
            <a href="register.php">Register</a>
        </p>
       </form>
    </div>
</body>
</html>