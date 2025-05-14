<?php
include 'auth.php';
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $plan_id = intval($_POST['plan_id']);

    // Aynı gün aynı planı tekrar eklemesin
    $check = $conn->prepare("SELECT * FROM diet_log WHERE user_id = ? AND diet_plan_id = ? AND date = CURDATE()");
    $check->bind_param("ii", $user_id, $plan_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        header("Location: home.php?msg=already_applied");
        exit;
    }

    // Eklemeye uygunsa logla
    $stmt = $conn->prepare("INSERT INTO diet_log (user_id, diet_plan_id, date) VALUES (?, ?, CURDATE())");
    $stmt->bind_param("ii", $user_id, $plan_id);
    if ($stmt->execute()) {
        header("Location: calendar.php?msg=success");
    } else {
        header("Location: home.php?msg=error");
    }
}
