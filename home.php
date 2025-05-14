<?php
include 'auth.php';
include 'connection.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get diet plans
$diet_q = $conn->prepare("SELECT * FROM diet_plans WHERE user_id = ?");
$diet_q->bind_param("i", $user_id);
$diet_q->execute();
$diet_result = $diet_q->get_result();

// Get exercise plans
$exercise_q = $conn->prepare("SELECT * FROM exercise_plans WHERE user_id = ?");
$exercise_q->bind_param("i", $user_id);
$exercise_q->execute();
$exercise_result = $exercise_q->get_result();

// Today's applied plans
$today = date('Y-m-d');

$today_diet_q = $conn->prepare("SELECT dp.id, dp.name FROM user_diet_activity uda JOIN diet_plans dp ON uda.diet_plan_id = dp.id WHERE uda.user_id = ? AND uda.date = ?");
$today_diet_q->bind_param("is", $user_id, $today);
$today_diet_q->execute();
$today_diet_q->bind_result($today_diet_id, $today_diet);
$today_diet_q->fetch();
$today_diet_q->close();

$today_exercise_q = $conn->prepare("SELECT ep.name FROM user_exercise_activity uea JOIN exercise_plans ep ON uea.exercise_plan_id = ep.id WHERE uea.user_id = ? AND uea.date = ?");
$today_exercise_q->bind_param("is", $user_id, $today);
$today_exercise_q->execute();
$today_exercise_q->bind_result($today_exercise);
$today_exercise_q->fetch();
$today_exercise_q->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home | SelfAIthlete</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <?php include 'header.php'; ?>
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .plan-box {
            padding: 15px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            margin-bottom: 15px;
        }

        .plan-box button {
            margin-top: 10px;
        }

        .today-section,
        .nutrient-summary {
            padding: 15px;
            background: #e9f7ef;
            border-left: 5px solid #28a745;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        h3 i {
            margin-right: 5px;
        }

        button {
            transition: all 0.2s ease;
        }

        button:hover {
            transform: scale(1.03);
        }

        .section h3 {
            margin-top: 30px;
            margin-bottom: 15px;
        }

        form input,
        form button {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Welcome, <?= htmlspecialchars($user_name) ?> 👋</h2>
        <p>Which plan would you like to follow today?</p>

        <!-- Today's applied plans -->
        <div class="today-section">
            <h3>📅 Today's Plans:</h3>
            <p><strong>Diet:</strong> <?= $today_diet ?? 'Not selected yet' ?></p>
            <p><strong>Exercise:</strong> <?= $today_exercise ?? 'Not selected yet' ?></p>
        </div>

        <!-- Diet summary -->
        <?php
        if (isset($today_diet_id)) {
            $nut_q = $conn->prepare("SELECT total_calories, total_protein, total_carbs, total_fat FROM diet_plans WHERE id = ? AND user_id = ?");
            $nut_q->bind_param("ii", $today_diet_id, $user_id);
            $nut_q->execute();
            $nut_q->bind_result($cal, $pro, $carb, $fat);
            $nut_q->fetch();
            $nut_q->close();
        ?>
            <div class="nutrient-summary">
                <h4>🍽️ Today's Diet Summary</h4>
                <ul>
                    <li><strong>Calories:</strong> <?= $cal ?> kcal</li>
                    <li><strong>Protein:</strong> <?= $pro ?> g</li>
                    <li><strong>Carbs:</strong> <?= $carb ?> g</li>
                    <li><strong>Fat:</strong> <?= $fat ?> g</li>
                </ul>
            </div>
        <?php } ?>

        <!-- Diet plans -->
        <div class="section">
            <h3>🍽️ Your Diet Plans</h3>
            <?php while ($plan = $diet_result->fetch_assoc()) : ?>
                <div class="plan-box">
                    <p><strong><?= htmlspecialchars($plan['name']) ?></strong> - <?= $plan['total_calories'] ?> kcal</p>
                    <form method="post" action="apply_diet.php">
                        <input type="hidden" name="diet_id" value="<?= $plan['id'] ?>">
                        <button type="submit">Apply Today</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Exercise plans -->
        <div class="section">
            <h3>🏋️ Your Exercise Plans</h3>
            <?php while ($plan = $exercise_result->fetch_assoc()) : ?>
                <div class="plan-box">
                    <p><strong><?= htmlspecialchars($plan['name']) ?></strong></p>
                    <form method="post" action="apply_exercise.php">
                        <input type="hidden" name="exercise_id" value="<?= $plan['id'] ?>">
                        <button type="submit">Apply Today</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <form action="save_calorie_goals.php" method="POST">
            <label>Daily Calorie Goal:</label>
            <input type="number" name="daily_goal" required>
            <label>Weekly Calorie Goal:</label>
            <input type="number" name="weekly_goal" required>
            <button type="submit">Save Goals</button>
        </form>

        <canvas id="myChart"></canvas>
        <div id="weekly_status"></div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            fetch('chart_data.php?range=weekly_home')
                .then(res => res.json())
                .then(data => {
                    // Create labels with day names and status emojis
                    const labels = data.labels.map((day, index) => {
                        return `${day} ${data.daily_statuses[index]}`;
                    });

                    new Chart(document.getElementById("myChart"), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Calories Consumed',
                                data: data.data,
                                backgroundColor: 'rgba(75,192,192,0.4)'
                            }]
                        }
                    });

                    // Show weekly status with goal
                    document.getElementById("weekly_status").innerHTML =
                        `Weekly Goal: ${data.weekly_status} (${data.weekly_total} / ${data.weekly_goal} kcal)`;
                });
        </script>
</body>

</html>