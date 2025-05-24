<?php
session_start();
include 'connection.php';

$user_id = $_SESSION['user_id'];

$events = [];

// Exercise planları çek
$stmt1 = $conn->prepare("
    SELECT cl.date, 'exercise' AS type, ep.name 
    FROM calendar_logs cl
    JOIN exercise_plans ep ON cl.plan_id = ep.id
    WHERE cl.user_id = ? AND cl.type = 'exercise'
");
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$result1 = $stmt1->get_result();

while ($row = $result1->fetch_assoc()) {
    $events[] = [
        'title' => "Exercise Plan: " . $row['name'],
        'start' => $row['date'],
        'allDay' => true,
    ];
}

// Diet planları çek
$stmt2 = $conn->prepare("
    SELECT cl.date, 'diet' AS type, dp.name 
    FROM calendar_logs cl
    JOIN diet_plans dp ON cl.plan_id = dp.id
    WHERE cl.user_id = ? AND cl.type = 'diet'
");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

while ($row = $result2->fetch_assoc()) {
    $events[] = [
        'title' => "Diet Plan: " . $row['name'],
        'start' => $row['date'],
        'allDay' => true,
    ];
}

// JSON çıktısı
header('Content-Type: application/json');
echo json_encode($events);
