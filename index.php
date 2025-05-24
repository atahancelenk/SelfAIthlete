<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>SelfAIthlete - Your Personal Fitness Companion</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --hover-color: #2980b9;
            --danger-color: #e74c3c;
            --success-color: #2ecc71;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f4f4;
        }

        header {
            background-color: var(--primary-color);
            color: white;
            padding: 0.8rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            color: var(--light-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo span {
            color: var(--secondary-color);
        }

        .logo-icon {
            font-size: 2rem;
            color: var(--secondary-color);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }

        .nav-links a {
            color: var(--light-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 0;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links a:hover {
            color: var(--secondary-color);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--secondary-color);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .active-link {
            color: var(--secondary-color) !important;
        }

        .active-link::after {
            width: 100% !important;
        }

        .user-area {
            position: relative;
        }

        .user-icon {
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-icon:hover {
            transform: scale(1.1);
            color: var(--secondary-color);
        }

        .user-box {
            display: none;
            position: absolute;
            right: 0;
            top: 45px;
            background-color: var(--dark-color);
            padding: 1.2rem;
            border-radius: 8px;
            width: 250px;
            z-index: 1001;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-box.active {
            display: block;
        }

        .user-box p {
            margin: 0.5rem 0;
            color: var(--light-color);
            font-size: 0.95rem;
        }

        .user-box span {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .logout-btn {
            margin-top: 15px;
            padding: 8px 16px;
            background-color: var(--danger-color);
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 0.5rem;
        }

        .hamburger div {
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 3px 0;
            transition: all 0.3s ease;
        }

        .hamburger.active div:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .hamburger.active div:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active div:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        @media (max-width: 992px) {
            .nav-links {
                gap: 1rem;
            }
        }

        @media (max-width: 768px) {
            header {
                padding: 0.8rem 1.5rem;
            }

            .hamburger {
                display: flex;
            }

            .nav-right {
                position: fixed;
                top: 70px;
                right: -100%;
                flex-direction: column;
                align-items: flex-start;
                background-color: var(--primary-color);
                width: 280px;
                height: calc(100vh - 70px);
                padding: 2rem;
                transition: right 0.3s ease;
                box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
            }

            .nav-right.active {
                right: 0;
            }

            .nav-links {
                flex-direction: column;
                width: 100%;
                gap: 1.5rem;
            }

            .nav-links li {
                width: 100%;
            }

            .nav-links a {
                padding: 0.5rem 0;
            }

            .user-box {
                position: static;
                margin-top: 1.5rem;
                width: 100%;
                animation: none;
            }

            .user-area {
                width: 100%;
            }
        }

        /* Hero Section Styles (from original index.php) */
        .hero {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 5rem 2rem;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 2rem;
        }

        .cta-button {
            display: inline-block;
            background-color: #e74c3c;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .cta-button:hover {
            background-color: #c0392b;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .features {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .features h2 {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2.5rem;
            color: #2c3e50;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #3498db;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="bi bi-activity logo-icon"></i>
                Self<span>AIthlete</span>
            </a>

            <div class="hamburger" id="hamburger">
                <div></div>
                <div></div>
                <div></div>
            </div>

            <div class="nav-right" id="navRight">
                <ul class="nav-links" id="navLinks">
                    <li><a href="exercises.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'exercises.php' ? 'active-link' : ''; ?>">
                            <i class="bi bi-activity"></i> Exercises
                        </a></li>
                    <li><a href="diet.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'diet.php' ? 'active-link' : ''; ?>">
                            <i class="bi bi-egg-fried"></i> Nutrition
                        </a></li>
                    <li><a href="statistic.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'statistic.php' ? 'active-link' : ''; ?>">
                            <i class="bi bi-graph-up"></i> Statistics
                        </a></li>
                    <li><a href="ai.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'ai.php' ? 'active-link' : ''; ?>">
                            <i class="bi bi-robot"></i> AI Coach
                        </a></li>
                </ul>
                <div class="user-area">
                    <div style="position: relative;">
                        <i class="bi bi-person user-icon" id="userIcon"></i>
                    </div>
                    <div class="user-box" id="userBox">
                        <?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_email'])): ?>
                            <p><i class="bi bi-person-fill"></i> Username: <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></p>
                            <p><i class="bi bi-envelope-fill"></i> Email: <span><?php echo htmlspecialchars($_SESSION['user_email']); ?></span></p>
                            <form action="logout.php" method="post">
                                <button type="submit" class="logout-btn">
                                    <i class="bi bi-box-arrow-right"></i> Log Out
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="login.php" style="color: var(--light-color); text-decoration: none; display: block; margin-bottom: 10px;">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                            <a href="register.php" style="color: var(--light-color); text-decoration: none; display: block;">
                                <i class="bi bi-person-plus"></i> Register
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Your Personal Fitness Companion</h1>
            <p>AI-powered personalized fitness and nutrition plans tailored to your unique needs, goals, and lifestyle.</p>
            <?php if (!isset($_SESSION['user_name'])): ?>
                <a href="register.php" class="cta-button">Get Started for Free</a>
            <?php else: ?>
                <a href="dashboard.php" class="cta-button">Go to Dashboard</a>
            <?php endif; ?>
        </section>

        <section class="features">
            <h2>Why Choose SelfAIthlete?</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-robot"></i>
                    </div>
                    <h3>AI-Powered Recommendations</h3>
                    <p>Get personalized exercise and meal plan suggestions based on your fitness level, goals, allergies, and schedule.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3>Progress Tracking</h3>
                    <p>Visualize your progress with interactive charts and earn achievement badges as you hit milestones.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-book"></i>
                    </div>
                    <h3>Educational Resources</h3>
                    <p>Learn about exercises with muscle diagrams and GIF tutorials, and get detailed nutritional information.</p>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const hamburger = document.getElementById('hamburger');
        const navRight = document.getElementById('navRight');
        const userIcon = document.getElementById('userIcon');
        const userBox = document.getElementById('userBox');
        const navLinks = document.querySelectorAll('.nav-links a');

        // Toggle mobile menu
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navRight.classList.toggle('active');
        });

        // Toggle user dropdown
        userIcon.addEventListener('click', (e) => {
            e.stopPropagation();
            userBox.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userBox.contains(e.target) && e.target !== userIcon) {
                userBox.classList.remove('active');
            }
        });

        // Close mobile menu when clicking a link
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    hamburger.classList.remove('active');
                    navRight.classList.remove('active');
                }
            });
        });

        // Highlight active link based on current page
        const currentPage = window.location.pathname.split('/').pop();
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active-link');
            }
        });

        // Add animation to logo on page load
        document.addEventListener('DOMContentLoaded', () => {
            const logo = document.querySelector('.logo');
            logo.style.opacity = '0';
            logo.style.transform = 'translateY(-20px)';
            logo.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                logo.style.opacity = '1';
                logo.style.transform = 'translateY(0)';
            }, 300);
        });
    </script>
    <?php include 'footer.php'; ?>
</body>

</html>