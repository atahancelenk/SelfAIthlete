<?php
include 'auth.php';
include 'connection.php';

// Örnek kullanıcı ID'si, gerçek projede session'dan alınır
$user_id = 1;

// Haftalık, aylık, yıllık verileri çek
function getData($conn, $interval)
{
    $query = "SELECT DATE(created_at) as date, SUM(total_calories) as calories FROM diet_plans
                  WHERE user_id = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL $interval)
                  GROUP BY DATE(created_at) ORDER BY created_at ASC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $GLOBALS['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

$weeklyData = getData($conn, '7 DAY');
$monthlyData = getData($conn, '1 MONTH');
$yearlyData = getData($conn, '1 YEAR');
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>İstatistikler</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <?php include 'header.php'; ?>
</head>

<body>
    <h1>İstatistikler</h1>

    <h2>Haftalık Kalori Takibi</h2>
    <canvas id="weeklyChart" width="600" height="300"></canvas>

    <h2>Aylık Kalori Takibi</h2>
    <canvas id="monthlyChart" width="600" height="300"></canvas>

    <h2>Yıllık Kalori Takibi</h2>
    <canvas id="yearlyChart" width="600" height="300"></canvas>

    <script>
        function renderChart(id, labels, data, labelText) {
            new Chart(document.getElementById(id), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: labelText,
                        data: data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false,
                        tension: 0.2
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // PHP'den gelen verileri JS'ye aktar
        const weeklyLabels = <?= json_encode(array_column($weeklyData, 'date')) ?>;
        const weeklyValues = <?= json_encode(array_column($weeklyData, 'calories')) ?>;

        const monthlyLabels = <?= json_encode(array_column($monthlyData, 'date')) ?>;
        const monthlyValues = <?= json_encode(array_column($monthlyData, 'calories')) ?>;

        const yearlyLabels = <?= json_encode(array_column($yearlyData, 'date')) ?>;
        const yearlyValues = <?= json_encode(array_column($yearlyData, 'calories')) ?>;

        renderChart("weeklyChart", weeklyLabels, weeklyValues, "Günlük Kalori");
        renderChart("monthlyChart", monthlyLabels, monthlyValues, "Günlük Kalori");
        renderChart("yearlyChart", yearlyLabels, yearlyValues, "Günlük Kalori");
    </script>
</body>

</html>