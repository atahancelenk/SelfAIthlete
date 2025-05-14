<?php
include 'auth.php';
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <title>Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f4f4f4;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            text-decoration: none;
            color: #ecf0f1;
        }

        .logo span {
            color: #3498db;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: #ecf0f1;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: #3498db;
        }

        .user-area {
            position: relative;
        }

        .user-icon {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .user-box {
            display: none;
            position: absolute;
            right: 0;
            top: 35px;
            background-color: #34495e;
            padding: 1rem;
            border-radius: 8px;
            width: 220px;
            z-index: 1001;
        }

        .user-box.active {
            display: block;
        }

        .user-box p,
        .user-box a {
            margin: 0.3rem 0;
            color: #ecf0f1;
            text-decoration: none;
            display: block;
        }

        .logout-btn {
            margin-top: 10px;
            padding: 6px 12px;
            background-color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .hamburger div {
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 4px;
        }

        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }

            .nav-right {
                position: absolute;
                top: 60px;
                right: 0;
                flex-direction: column;
                align-items: flex-end;
                background-color: #2c3e50;
                width: 100%;
                display: none;
                padding: 1rem;
            }

            .nav-right.active {
                display: flex;
            }

            .nav-links {
                flex-direction: column;
                width: 100%;
                align-items: flex-end;
            }

            .nav-links li {
                margin: 0.5rem 0;
            }

            .user-box {
                position: static;
                margin-top: 1rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">Self<span>AIthlete</span></a>

            <div class="hamburger" id="hamburger">
                <div></div>
                <div></div>
                <div></div>
            </div>

            <div class="nav-right" id="navRight">
                <ul class="nav-links" id="navLinks">
                    <li><a href="exercises.php">Exercises</a></li>
                    <li><a href="diet.php">Diet Table</a></li>
                    <li><a href="statistic.php">Statistics</a></li>
                    <li><a href="ai.php">AI</a></li>
                </ul>
                <div class="user-area">
                    <i class="bi bi-person user-icon" id="userIcon"></i>
                    <div class="user-box" id="userBox">
                        <?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_email'])): ?>
                            <p>Username : <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></p>
                            <p>Email : <span><?php echo htmlspecialchars($_SESSION['user_email']); ?></span></p>
                            <form action="logout.php" method="post">
                                <button type="submit" class="logout-btn">Log out</button>
                            </form>
                        <?php else: ?>
                            <a href="login.php">Giriş Yap</a>
                            <a href="register.php">Kaydol</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navRight = document.getElementById('navRight');
        const userIcon = document.getElementById('userIcon');
        const userBox = document.getElementById('userBox');

        hamburger.addEventListener('click', () => {
            navRight.classList.toggle('active');
        });

        userIcon.addEventListener('click', () => {
            userBox.classList.toggle('active');
        });
    </script>
</body>

</html>