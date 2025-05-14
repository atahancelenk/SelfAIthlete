<?php
session_start();
include 'connection.php';

if (isset($_POST['submit-btn'])) {

    $filter_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $email = mysqli_real_escape_string($conn, $filter_email);

    $filter_password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $password = mysqli_real_escape_string($conn, $filter_password);

    $select_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('Query failed: ' . mysqli_error($conn));

    if (mysqli_num_rows($select_user) > 0) {
        $row = mysqli_fetch_assoc($select_user);
        if ($row['user_type'] == 'admin') {
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['user_type'] = 'admin';
            header('location:admin_pannel.php');
        } else if ($row['user_type'] == 'user') {
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_type'] = 'user';
            header('location:home.php');
        } else {
            $message[] = 'Incorrect email or password!';
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
            <h1>Login Now</h1>
            <div class="input-field">
                <label>Your Email</label><br>
                <input type="email" name="email" placeholder="Enter Your Email" required>
            </div>
            <div class="input-field">
                <label>Your Password</label><br>
                <input type="password" name="password" placeholder="Enter Your Password" required>
            </div>
            <input type="submit" name="submit-btn" value="Login Now" class="btn">
            <p>Do not have an account? <a href="register.php">Register Now</a></p>
            <p>Want to go back <a href="index.php">home</a>?</p>
        </form>
    </section>
</body>

</html>