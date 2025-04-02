<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Document</title>
</head>
<body>
    <header class="header">
        <div class="flex">
            <a href="admin_pannel.php" class="logo"><img src="img/logo.png" width="150px" height="150px"></a>
            <nav class="navbar">
                <a href="admin_pannel.php">Dashboard</a>
                <a href="admin_exercise.php">Exercises</a>
                <a href="admin_diet.php">Diet Table</a>
                <a href="admin_users.php">Users</a>
                <a href="home.html">Logout</a>
            </nav>
            <div class="icons">
                <i class="bi bi-person" id="user-btn"></i>
                <i class="bi bi-list" id="menu-btn"></i>
            </div>
            <div class="user-box">
                <p>Username : <span><?php echo $_SESSION['admin_name']; ?></span></p>
                <p>Email : <span><?php echo $_SESSION['admin_email']; ?></span></p>
                <form method="post">
                    <button type="submit" class="logout-btn">Log out</button>
                </form>
            </div>
        </div>
    </header>
    <div class="banner">
        <div class="detail">
            <h1>Admin Dashboard</h1>
            <p>Welcome to the admin panel. Here you can manage users, exercises, and diet plans.</p>
            <p>Use the navigation menu to access different sections of the admin panel.</p>
        </div>
    </div>
    <div class="line"></div>
</body>
</html>