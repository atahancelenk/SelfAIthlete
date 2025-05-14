<?php
include 'auth.php';
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exercise_id'])) {
    $user_id = $_SESSION['user_id'];
    $exercise_id = intval($_POST['exercise_id']);
    $today = date('Y-m-d');

    // calendar_logs'a ekle
    $log_stmt = $conn->prepare("INSERT INTO calendar_logs (user_id, type, plan_id, date) VALUES (?, 'exercise', ?, ?)");
    $log_stmt->bind_param("iis", $user_id, $exercise_id, $today);
    $log_stmt->execute();

    // Aynı güne ikinci kayıt olmasın diye kontrol
    $check = $conn->prepare("SELECT id FROM user_exercise_activity WHERE user_id = ? AND date = ?");
    $check->bind_param("is", $user_id, $today);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO user_exercise_activity (user_id, exercise_plan_id, date) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $user_id, $exercise_id, $today);
        $insert->execute();
    }

    header("Location: home.php");
    exit;
} else {
    header("Location: home.php");
    exit;
}
