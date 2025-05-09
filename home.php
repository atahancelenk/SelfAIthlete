<?php
include 'auth.php';
include 'connection.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Diyet planlarını çek
$diet_q = $conn->prepare("SELECT * FROM diet_plans WHERE user_id = ?");
$diet_q->bind_param("i", $user_id);
$diet_q->execute();
$diet_result = $diet_q->get_result();

// Egzersiz planlarını çek
$exercise_q = $conn->prepare("SELECT * FROM exercise_plans WHERE user_id = ?");
$exercise_q->bind_param("i", $user_id);
$exercise_q->execute();
$exercise_result = $exercise_q->get_result();

// Bugün uygulanan planlar
$today = date('Y-m-d');

$bugun_diyet_q = $conn->prepare("SELECT dp.id, dp.name FROM user_diet_activity uda JOIN diet_plans dp ON uda.diet_plan_id = dp.id WHERE uda.user_id = ? AND uda.date = ?");
$bugun_diyet_q->bind_param("is", $user_id, $today);
$bugun_diyet_q->execute();
$bugun_diyet_q->bind_result($bugun_diyet_id, $bugun_diyet);
$bugun_diyet_q->fetch();
$bugun_diyet_q->close();

$bugun_egzersiz_q = $conn->prepare("SELECT ep.name FROM user_exercise_activity uea JOIN exercise_plans ep ON uea.exercise_plan_id = ep.id WHERE uea.user_id = ? AND uea.date = ?");
$bugun_egzersiz_q->bind_param("is", $user_id, $today);
$bugun_egzersiz_q->execute();
$bugun_egzersiz_q->bind_result($bugun_egzersiz);
$bugun_egzersiz_q->fetch();
$bugun_egzersiz_q->close();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Ana Sayfa | SelfAIthlete</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h2>Hoş geldin, <?= htmlspecialchars($user_name) ?> 👋</h2>
        <p>Bugün hangi planını uygulamak istersin?</p>

        <!-- Bugün uygulanan planlar -->
        <div class="today-section">
            <h3>📅 Bugün Uygulanan Planlar:</h3>
            <p><strong>Diyet:</strong> <?= $bugun_diyet ?? 'Henüz seçilmedi' ?></p>
            <p><strong>Egzersiz:</strong> <?= $bugun_egzersiz ?? 'Henüz seçilmedi' ?></p>
        </div>

        <!-- Diyet özeti -->
        <?php
        if (isset($bugun_diyet_id)) {
            $nut_q = $conn->prepare("SELECT total_calories, total_protein, total_carbs, total_fat FROM diet_plans WHERE id = ? AND user_id = ?");
            $nut_q->bind_param("ii", $bugun_diyet_id, $user_id);
            $nut_q->execute();
            $nut_q->bind_result($cal, $pro, $carb, $fat);
            $nut_q->fetch();
            $nut_q->close();
        ?>
            <div class="nutrient-summary">
                <h4>🍽️ Bugünkü Diyet Özeti</h4>
                <ul>
                    <li><strong>Kalori:</strong> <?= $cal ?> kcal</li>
                    <li><strong>Protein:</strong> <?= $pro ?> g</li>
                    <li><strong>Karbonhidrat:</strong> <?= $carb ?> g</li>
                    <li><strong>Yağ:</strong> <?= $fat ?> g</li>
                </ul>
            </div>
        <?php } ?>

        <!-- Diyet planları -->
        <div class="section">
            <h3>🍽️ Diyet Planların</h3>
            <?php while ($plan = $diet_result->fetch_assoc()): ?>
                <div class="plan-box">
                    <p><strong><?= htmlspecialchars($plan['name']) ?></strong> - <?= $plan['total_calories'] ?> kcal</p>
                    <form method="post" action="apply_diet.php">
                        <input type="hidden" name="diet_id" value="<?= $plan['id'] ?>">
                        <button type="submit">Bugün Uygula</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Egzersiz planları -->
        <div class="section">
            <h3>🏋️ Egzersiz Planların</h3>
            <?php while ($plan = $exercise_result->fetch_assoc()): ?>
                <div class="plan-box">
                    <p><strong><?= htmlspecialchars($plan['name']) ?></strong></p>
                    <form method="post" action="apply_exercise.php">
                        <input type="hidden" name="exercise_id" value="<?= $plan['id'] ?>">
                        <button type="submit">Bugün Uygula</button>
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
                    const dateToDay = dateStr => {
                        const days = ["Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi"];
                        const dayIndex = new Date(dateStr).getDay();
                        return days[dayIndex];
                    };

                    const labels = data.labels.map(date => {
                        const status = data.daily_statuses[date] ?? '❌';
                        return dateToDay(date) + " " + status;
                    });
                    const chartData = data.data;

                    new Chart(document.getElementById("myChart"), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Alınan Kalori',
                                data: chartData,
                                backgroundColor: 'rgba(75,192,192,0.4)'
                            }]
                        }
                    });

                    // Hedefle birlikte haftalık durumu göster
                    document.getElementById("weekly_status").innerHTML =
                        `Haftalık Hedef: ${data.weekly_status} (${data.weekly_total} / ${data.weekly_goal} kcal)`;
                });
        </script>
</body>

</html>