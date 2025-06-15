<?php 
include "koneksi.php";
error_reporting(0);
session_start();

if (isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $cpassword = md5($_POST['cpassword']);

    if ($password == $cpassword) {
        $sql = "SELECT * FROM user WHERE email='$email'";
        $result = mysqli_query($mysqli, $sql);
        if (!$result->num_rows > 0) {
            $sql = "INSERT INTO user (username, email, password) 
                    VALUES ('$username', '$email', '$password')";
            $result = mysqli_query($mysqli, $sql);
            if ($result) {
                echo "<script>alert('Wow! User Registration Completed.')</script>";
                echo "<script>window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Woops! Something Went Wrong.')</script>";
            }
        } else {
            echo "<script>alert('Woops! Email Already Exists.')</script>";
        }
    } else {
        echo "<script>alert('Password Not Matched.')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Akun</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="container">
       <form action="" method="POST" class="login-email">
        <p style="font-size: 2rem;font-weight: 850;">REGISTER</p>
        <div class="input-group"><input type="text" placeholder="User Name" name="username" value="<?php echo $username; ?>" required></div>
        <div class="input-group"><input type="email" placeholder="Email" name="email" value="<?php echo $email; ?>" required></div>
        <div class="input-group"><input type="password" placeholder="Password" name="password" required></div>
        <div class="input-group"><input type="password" placeholder="Confirm Password" name="cpassword" required></div>
        <div class="input-group"><button name="submit" class="btn">Register</button></div>

        <p class="login-register-text">Have an Account?
            <a href="login.php">Login</a>
        </p>
       </form>
    </div>
</body>
</html>
