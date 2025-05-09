<?php
include 'auth.php';
include 'connection.php';

$user_id = $_SESSION['user_id'];
$range = $_GET['range'] ?? 'weekly_home';

if ($range === 'weekly_home') {
    $monday = date('Y-m-d', strtotime('monday this week'));
    $sunday = date('Y-m-d', strtotime($monday . ' +6 days'));

    // Günlük sıfırlı liste
    $days = [];
    for ($i = 0; $i < 7; $i++) {
        $day = date('Y-m-d', strtotime($monday . " +$i day"));
        $days[$day] = 0;
    }

    // Hedefler
    $goal_stmt = $conn->prepare("SELECT daily_goal, weekly_goal FROM calorie_goals WHERE user_id = ?");
    $goal_stmt->bind_param("i", $user_id);
    $goal_stmt->execute();
    $goal_result = $goal_stmt->get_result()->fetch_assoc();

    $daily_goal = $goal_result['daily_goal'] ?? 2000;
    $weekly_goal = $goal_result['weekly_goal'] ?? 14000;

    // Kalori verileri
    $stmt = $conn->prepare("
        SELECT uda.date, dp.total_calories 
        FROM user_diet_activity uda 
        JOIN diet_plans dp ON uda.diet_plan_id = dp.id 
        WHERE uda.user_id = ? AND uda.date BETWEEN ? AND ?
    ");
    $stmt->bind_param("iss", $user_id, $monday, $sunday);
    $stmt->execute();
    $result = $stmt->get_result();

    $statuses = [];
    $weekly_total = 0;

    while ($row = $result->fetch_assoc()) {
        $date = $row['date'];
        $calories = (int)$row['total_calories'];
        $days[$date] = $calories;
        $statuses[$date] = $calories >= $daily_goal ? '✅' : '❌';
        $weekly_total += $calories;
    }

    $week_status = $weekly_total >= $weekly_goal ? '✅' : '❌';

    echo json_encode([
        'labels' => array_keys($days),
        'data' => array_values($days),
        'daily_statuses' => $statuses,
        'weekly_total' => $weekly_total,
        'weekly_status' => $week_status,
        'weekly_goal' => $weekly_goal,
        'daily_goal' => $daily_goal
    ]);
}
