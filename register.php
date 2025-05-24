<?php
session_start();
include 'connection.php';

if (isset($_POST['submit-btn'])) {
    $filter_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $name = mysqli_real_escape_string($conn, $filter_name);

    $filter_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $email = mysqli_real_escape_string($conn, $filter_email);

    $filter_password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $password = mysqli_real_escape_string($conn, $filter_password);

    $filter_cpassword = filter_var($_POST['cpassword'], FILTER_SANITIZE_STRING);
    $cpassword = mysqli_real_escape_string($conn, $filter_cpassword);

    $select_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('Query failed: ' . mysqli_error($conn));

    if (mysqli_num_rows($select_user) > 0) {
        $message[] = 'User already exists!';
    } else {
        if ($password != $cpassword) {
            $message[] = 'Confirm password not matched!';
        } else {
            mysqli_query($conn, "INSERT INTO users(name, email, password) VALUES('$name', '$email', '$password')") or die('Query failed: ' . mysqli_error($conn));
            $message[] = 'Registered successfully!';
            header('location:login.php');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="css/signin.css">
    <title>Register Page</title>
</head>

<body>

    <section class="form-container">

        <?php
        if (isset($message)) {
            foreach ($message as $msg) {
                echo '
                        <div class="message">
                            <span>' . $msg . '</span>
                            <i class="bx bx-x" onclick="this.parentElement.remove();"></i>
                        </div>
                    ';
            }
        }
        ?>

        <form method="post">
            <h1>Register Now</h1>
            <input type="text" name="name" placeholder="Enter Your Name" required>
            <input type="email" name="email" placeholder="Enter Your Email" required>
            <input type="password" name="password" placeholder="Enter Your Password" required>
            <input type="password" name="cpassword" placeholder="Confrim Your Password" required>
            <input type="submit" name="submit-btn" value="Register Now" class="btn">
            <p>Already have an account? <a href="login.php">Login</a></p>
            <p>Want to go back <a href="index.php">home</a>?</p>
        </form>
    </section>
</body>

</html>