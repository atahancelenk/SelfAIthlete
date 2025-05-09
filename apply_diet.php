<?php
include 'auth.php';
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['diet_id'])) {
    $user_id = $_SESSION['user_id'];
    $diet_id = intval($_POST['diet_id']);
    $today = date('Y-m-d');

    // Aynı güne ikinci kayıt olmasın diye kontrol
    $check = $conn->prepare("SELECT id FROM user_diet_activity WHERE user_id = ? AND date = ?");
    $check->bind_param("is", $user_id, $today);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO user_diet_activity (user_id, diet_plan_id, date) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $user_id, $diet_id, $today);
        $insert->execute();
    }

    header("Location: home.php");
    exit;
} else {
    header("Location: home.php");
    exit;
}
