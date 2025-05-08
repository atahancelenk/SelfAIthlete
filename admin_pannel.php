<?php
    include 'admin_auth.php';
    include 'connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Admin Pannel</title>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    <div class="line4"></div>
    <section class="dashboard">
        <div class="box-container">
            <div class="box">
                <?php 
                    $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user' ") or die('query failed');
                    $num_of_users = mysqli_num_rows($select_users);
                ?>
                <h3><?php echo $num_of_users; ?></h3>
                <p>Total Normal Users</p>
            </div>
            <div class="box">
                <?php 
                    $select_admins = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'admin' ") or die('query failed');
                    $num_of_admins = mysqli_num_rows($select_admins);
                ?>
                <h3><?php echo $num_of_admins; ?></h3>
                <p>Total Normal Users</p>
            </div>
            <div class="box">
                <?php 
                    $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
                    $num_of_users = mysqli_num_rows($select_users);
                ?>
                <h3><?php echo $num_of_users; ?></h3>
                <p>Total Registered Users</p>
            </div>
        </div>
    </section>
</body>
</html>